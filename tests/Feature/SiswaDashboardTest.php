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

    $response = $this->actingAs($user)->post(route('siswa.presensi'), [
        'foto_base64' => 'data:image/jpeg;base64,abcdef',
        'latitude'    => -6.200000,
        'longitude'   => 106.800000,
    ]);
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
    $response = $this->actingAs($user)->post(route('siswa.presensi'), [
        'foto_base64' => 'data:image/jpeg;base64,abcdef',
        'latitude'    => -6.200000,
        'longitude'   => 106.800000,
    ]);
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

    $keteranganLong = str_repeat('A', 500);

    $response = $this->actingAs($user)->post(route('siswa.izin'), [
        'status'      => 'izin',
        'keterangan'  => $keteranganLong,
        'foto_base64' => 'data:image/jpeg;base64,abcdef',
        'latitude'    => -6.200000,
        'longitude'   => 106.800000,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $kehadiran = Kehadiran::where('siswa_id', $siswa->id)->first();
    expect($kehadiran)->not->toBeNull();
    expect($kehadiran->status)->toBe('izin');
    expect($kehadiran->keterangan)->toBe($keteranganLong);
});

test('secretary student can view subject attendance detail page and save data', function () {
    $data = createActiveSemesterAndRules();
    
    $user = User::factory()->create();
    $kelas = \App\Models\Kelas::create([
        'nama' => 'X-1',
        'tingkat' => '10',
        'status' => 'aktif',
    ]);
    $siswa = Siswa::create([
        'user_id'       => $user->id,
        'nama'          => 'Budiono',
        'jenis_kelamin' => 'L',
        'is_sekretaris' => true,
        'kelas_id'      => $kelas->id,
        'nis'           => '12345'
    ]);

    $mapel = \App\Models\MataPelajaran::create([
        'nama' => 'Matematika',
        'kode' => 'MTK01',
    ]);

    $record = \App\Models\KehadiranMataPelajaran::create([
        'kelas_id'          => $kelas->id,
        'semester_id'       => $data['semester']->id,
        'mata_pelajaran_id' => $mapel->id,
        'tanggal'           => now()->toDateString(),
        'jam_mulai'         => '08:00',
        'jam_selesai'       => '09:30',
        'created_by'        => $siswa->id,
    ]);

    // View detail page
    $response = $this->actingAs($user)->get(route('siswa.sekretaris.kehadiran-mp.daftar-hadir', $record->id));
    $response->assertStatus(200);
    $response->assertSee('Matematika');
    $response->assertSee('Budiono');
    $response->assertSee('12345');

    // Save attendance data
    $saveResponse = $this->actingAs($user)->post(route('siswa.sekretaris.kehadiran-mp.simpan', $record->id), [
        'siswa' => [
            [
                'siswa_id' => $siswa->id,
                'status' => 1,
                'keterangan' => 'Hadir tepat waktu'
            ]
        ]
    ]);
    $saveResponse->assertStatus(200);
    $saveResponse->assertJson(['success' => true]);

    $this->assertDatabaseHas('kehadiran_mata_pelajaran_details', [
        'kehadiran_mata_pelajaran_id' => $record->id,
        'siswa_id' => $siswa->id,
        'status' => true,
        'keterangan' => 'Hadir tepat waktu'
    ]);
});

