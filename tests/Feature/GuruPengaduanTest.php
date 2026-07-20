<?php

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Guru;
use App\Models\Pengaduan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('homeroom teacher can view student complaints, filter by date range, search by name, and export data', function () {
    $user = User::factory()->create();
    $guru = Guru::create([
        'user_id' => $user->id,
        'nama' => 'Budi Guru',
        'jenis_kelamin' => 'L',
    ]);

    $kelas = Kelas::create([
        'nama' => 'X-1',
        'tingkat' => '10',
        'status' => 'aktif',
        'guru_id' => $guru->id, // Budi Guru is the wali kelas of class X-1
    ]);

    $siswa1 = Siswa::create([
        'user_id'       => User::factory()->create()->id,
        'nama'          => 'Ahmad',
        'jenis_kelamin' => 'L',
        'is_sekretaris' => true,
        'kelas_id'      => $kelas->id,
        'nis'           => '10201'
    ]);

    $siswa2 = Siswa::create([
        'user_id'       => User::factory()->create()->id,
        'nama'          => 'Budi Siswa',
        'jenis_kelamin' => 'L',
        'is_sekretaris' => false,
        'kelas_id'      => $kelas->id,
        'nis'           => '10202'
    ]);

    // Create some complaints
    Pengaduan::create([
        'siswa_id' => $siswa1->id,
        'tanggal' => '2026-07-01',
        'deskripsi' => 'Pengaduan Ahmad 1 Juli',
    ]);

    Pengaduan::create([
        'siswa_id' => $siswa2->id,
        'tanggal' => '2026-07-15',
        'deskripsi' => 'Pengaduan Budi 15 Juli',
    ]);

    // 1. Visit index page
    $response = $this->actingAs($user)->get(route('guru.pengaduan'));
    $response->assertStatus(200);
    $response->assertSee('Daftar Pengaduan Siswa');
    $response->assertSee('Pengaduan Ahmad 1 Juli');
    $response->assertSee('Pengaduan Budi 15 Juli');

    // 2. Filter by date range
    $filterResponse = $this->actingAs($user)->get(route('guru.pengaduan', [
        'tanggal_range' => '2026-07-01 hingga 2026-07-10'
    ]));
    $filterResponse->assertSee('Pengaduan Ahmad 1 Juli');
    $filterResponse->assertDontSee('Pengaduan Budi 15 Juli');

    // 3. Search by student name
    $searchResponse = $this->actingAs($user)->get(route('guru.pengaduan', [
        'search' => 'Budi'
    ]));
    $searchResponse->assertDontSee('Pengaduan Ahmad 1 Juli');
    $searchResponse->assertSee('Pengaduan Budi 15 Juli');

    // 4. Export excel
    $exportResponse = $this->actingAs($user)->get(route('guru.pengaduan.export'));
    $exportResponse->assertStatus(200);
    $exportResponse->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});
