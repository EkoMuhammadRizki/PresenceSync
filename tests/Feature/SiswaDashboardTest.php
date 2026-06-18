<?php

use App\Models\AturanJam;
use App\Models\Kehadiran;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

function createActiveSemesterAndRules()
{
    $ta = TahunAjaran::create([
        'nama'          => '2025/2026',
        'bulan_mulai'   => '2025-07-01',
        'bulan_selesai' => '2026-06-30',
        'status'        => 'aktif',
    ]);

    $semester = Semester::create([
        'tahun_ajaran_id' => $ta->id,
        'jenis'           => 'genap',
        'tanggal_mulai'   => '2026-01-01',
        'tanggal_selesai' => '2026-06-30',
        'status'          => 'aktif',
    ]);

    $aturan = AturanJam::create([
        'hari'                    => 'Senin',
        'jam_masuk'               => '07:00:00',
        'toleransi_keterlambatan' => 15,
        'jam_pulang'              => '15:30:00',
        'is_aktif'                => true,
    ]);

    return compact('semester', 'aturan');
}

test('student dashboard can be loaded by authenticated student', function () {
    $user = User::factory()->create();
    $siswa = Siswa::create([
        'user_id'       => $user->id,
        'nama'          => 'Budiono',
        'jenis_kelamin' => 'L',
    ]);

    $response = $this->actingAs($user)->get(route('siswa.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Budiono');
    $response->assertSee('Status Hari Ini');
});

test('non-student user gets 403 on student dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('siswa.dashboard'));
    $response->assertStatus(403);
});

test('student can check in successfully', function () {
    createActiveSemesterAndRules();

    $user = User::factory()->create();
    $siswa = Siswa::create([
        'user_id'       => $user->id,
        'nama'          => 'Budiono',
        'jenis_kelamin' => 'L',
    ]);

    $response = $this->actingAs($user)->post(route('siswa.presensi'));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $kehadiran = Kehadiran::where('siswa_id', $siswa->id)->first();
    expect($kehadiran)->not->toBeNull();
    expect(Carbon::parse($kehadiran->tanggal)->toDateString())->toBe(now()->toDateString());
});

test('student cannot double check in on same day', function () {
    $data = createActiveSemesterAndRules();

    $user = User::factory()->create();
    $siswa = Siswa::create([
        'user_id'       => $user->id,
        'nama'          => 'Budiono',
        'jenis_kelamin' => 'L',
    ]);

    // First check in
    Kehadiran::create([
        'siswa_id'      => $siswa->id,
        'semester_id'   => $data['semester']->id,
        'aturan_jam_id' => $data['aturan']->id,
        'tanggal'       => now()->toDateString(),
        'status'        => 'hadir',
    ]);

    // Try second check in
    $response = $this->actingAs($user)->post(route('siswa.presensi'));
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('student can submit excuse successfully', function () {
    createActiveSemesterAndRules();

    $user = User::factory()->create();
    $siswa = Siswa::create([
        'user_id'       => $user->id,
        'nama'          => 'Budiono',
        'jenis_kelamin' => 'L',
    ]);

    $response = $this->actingAs($user)->post(route('siswa.izin'), [
        'status'     => 'izin',
        'keterangan' => 'Keperluan keluarga',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $kehadiran = Kehadiran::where('siswa_id', $siswa->id)->first();
    expect($kehadiran)->not->toBeNull();
    expect($kehadiran->status)->toBe('izin');
    expect($kehadiran->keterangan)->toBe('Keperluan keluarga');
});
