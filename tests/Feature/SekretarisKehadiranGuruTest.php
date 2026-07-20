<?php

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Pengaduan;
use App\Models\Semester;
use App\Models\MataPelajaran;
use App\Models\KehadiranMataPelajaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('complaints list is paginated and shows exactly 10 items per page', function () {
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

    // Create 15 complaints
    for ($i = 1; $i <= 15; $i++) {
        Pengaduan::create([
            'siswa_id' => $siswa->id,
            'tanggal'  => '2026-07-20',
            'deskripsi' => "Pengaduan ke-{$i}",
        ]);
    }

    $response = $this->actingAs($user)->get(route('siswa.pengaduan'));
    $response->assertStatus(200);

    // Verify it is paginated correctly in the view data
    $recordsOnPage = $response->viewData('records');
    expect($recordsOnPage)->toBeInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class);
    expect($recordsOnPage->count())->toBe(10);
    expect($recordsOnPage->total())->toBe(15);
});

test('secretary can confirm teacher presence and absence status', function () {
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
    $semester = Semester::create([
        'jenis' => 'ganjil',
        'nama' => 'Ganjil 2026/2027',
        'tanggal_mulai' => '2026-07-01',
        'tanggal_selesai' => '2026-12-31',
        'tahun_ajaran_id' => \App\Models\TahunAjaran::create([
            'nama' => '2026/2027',
            'bulan_mulai' => '2026-07-01',
            'bulan_selesai' => '2027-06-30',
            'status' => 'aktif',
        ])->id,
        'status' => 'aktif',
    ]);
    $mapel = MataPelajaran::create([
        'kode' => 'MTK',
        'nama' => 'Matematika',
        'guru_id' => \App\Models\Guru::create([
            'nama' => 'Budi',
            'nip' => '987654',
            'jenis_kelamin' => 'L',
        ])->id,
    ]);

    $kehadiranMp = KehadiranMataPelajaran::create([
        'kelas_id'          => $kelas->id,
        'semester_id'       => $semester->id,
        'mata_pelajaran_id' => $mapel->id,
        'tanggal'           => '2026-07-20',
        'jam_mulai'         => '07:30',
        'jam_selesai'       => '09:00',
        'created_by'        => $siswa->id,
        'is_guru_hadir'     => true,
        'ada_konfirmasi_guru' => false,
    ]);

    $response = $this->actingAs($user)->post(route('siswa.sekretaris.kehadiran-mp.konfirmasi-guru', $kehadiranMp->id), [
        'is_guru_hadir' => 0,
        'ada_konfirmasi_guru' => 1,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'is_guru_hadir' => false,
        'ada_konfirmasi_guru' => true,
    ]);

    $this->assertDatabaseHas('kehadiran_mata_pelajarans', [
        'id' => $kehadiranMp->id,
        'is_guru_hadir' => 0,
        'ada_konfirmasi_guru' => 1,
    ]);
});
