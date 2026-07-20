<?php

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Pengaduan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('secretary student can view complaints page and submit a complaint', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $kelas = Kelas::create([
        'nama' => 'X-1',
        'tingkat' => '10',
        'status' => 'aktif',
    ]);
    $siswa = Siswa::create([
        'user_id'       => $user->id,
        'nama'          => 'Ahmad',
        'jenis_kelamin' => 'L',
        'is_sekretaris' => true,
        'kelas_id'      => $kelas->id,
        'nis'           => '10201'
    ]);

    // 1. Visit index page
    $response = $this->actingAs($user)->get(route('siswa.pengaduan'));
    $response->assertStatus(200);
    $response->assertSee('Pengaduan Siswa');
    $response->assertSee('X-1');

    // 2. Submit complaint
    $imageFile = UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg');

    $submitResponse = $this->actingAs($user)->post(route('siswa.pengaduan.store'), [
        'tanggal' => '2026-07-17',
        'deskripsi' => 'Pendinginan AC di kelas mati.',
        'bukti' => $imageFile,
    ]);

    $submitResponse->assertStatus(200);
    $submitResponse->assertJson(['success' => true]);

    // Check database
    $this->assertDatabaseHas('pengaduans', [
        'siswa_id' => $siswa->id,
        'tanggal' => '2026-07-17 00:00:00',
        'deskripsi' => 'Pendinginan AC di kelas mati.',
    ]);

    // Get the created record to find the path
    $record = Pengaduan::where('siswa_id', $siswa->id)->first();
    expect($record->bukti)->not->toBeNull();

    // Verify storage has the compressed file
    Storage::disk('public')->assertExists($record->bukti);

    // Verify size is compressed (should be small, especially under 100KB for the fake image)
    $size = Storage::disk('public')->size($record->bukti);
    expect($size)->toBeLessThan(100 * 1024); // Under 100KB
});

test('secretary student can filter complaints by date range', function () {
    $user = User::factory()->create();
    $kelas = Kelas::create([
        'nama' => 'X-1',
        'tingkat' => '10',
        'status' => 'aktif',
    ]);
    $siswa = Siswa::create([
        'user_id'       => $user->id,
        'nama'          => 'Ahmad',
        'jenis_kelamin' => 'L',
        'is_sekretaris' => true,
        'kelas_id'      => $kelas->id,
        'nis'           => '10201'
    ]);

    Pengaduan::create([
        'siswa_id' => $siswa->id,
        'tanggal' => '2026-07-01',
        'deskripsi' => 'Pengaduan Juli A',
    ]);

    Pengaduan::create([
        'siswa_id' => $siswa->id,
        'tanggal' => '2026-07-15',
        'deskripsi' => 'Pengaduan Juli B',
    ]);

    // Query in range
    $response = $this->actingAs($user)->get(route('siswa.pengaduan', [
        'tanggal_range' => '2026-07-01 hingga 2026-07-10'
    ]));
    $response->assertSee('Pengaduan Juli A');
    $response->assertDontSee('Pengaduan Juli B');
});
