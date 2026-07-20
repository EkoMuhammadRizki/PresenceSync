<?php

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Guru;
use App\Models\Kehadiran;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('homeroom teacher can view class details, export excel, and export pdf of students', function () {
    // 1. Create dependencies
    $user = User::factory()->create();
    $guru = Guru::create([
        'user_id' => $user->id,
        'nama' => 'Budi Guru Kelas',
        'jenis_kelamin' => 'L',
    ]);

    $tahunAjaran = TahunAjaran::create([
        'nama'          => '2025/2026',
        'bulan_mulai'   => '2025-07-01',
        'bulan_selesai' => '2026-06-30',
        'status'        => 'aktif',
    ]);
 
    $semester = Semester::create([
        'tahun_ajaran_id' => $tahunAjaran->id,
        'jenis'           => 'ganjil',
        'tanggal_mulai'   => '2025-07-01',
        'tanggal_selesai' => '2025-12-31',
        'status'          => 'aktif',
    ]);

    $kelas = Kelas::create([
        'nama' => 'XI-A',
        'tingkat' => '11',
        'status' => 'aktif',
        'guru_id' => $guru->id,
        'tahun_ajaran_id' => $tahunAjaran->id,
    ]);

    $siswa1 = Siswa::create([
        'user_id'       => User::factory()->create()->id,
        'nama'          => 'Ahmad Subarjo',
        'jenis_kelamin' => 'L',
        'is_sekretaris' => false,
        'kelas_id'      => $kelas->id,
        'nis'           => '10201',
        'nisn'          => '0054321098'
    ]);

    $siswa2 = Siswa::create([
        'user_id'       => User::factory()->create()->id,
        'nama'          => 'Eko Muhammad',
        'jenis_kelamin' => 'L',
        'is_sekretaris' => false,
        'kelas_id'      => $kelas->id,
        'nis'           => '10202',
        'nisn'          => '0054321099'
    ]);

    // Create some attendance records
    Kehadiran::create([
        'siswa_id' => $siswa1->id,
        'tanggal' => '2026-07-01',
        'status' => 'hadir',
        'semester_id' => $semester->id,
    ]);

    Kehadiran::create([
        'siswa_id' => $siswa2->id,
        'tanggal' => '2026-07-01',
        'status' => 'terlambat',
        'semester_id' => $semester->id,
    ]);

    // 2. Visit index page
    $response = $this->actingAs($user)->get(route('guru.kelas-wali'));
    $response->assertStatus(200);
    $response->assertSee('Daftar Siswa');
    $response->assertSee('Ahmad Subarjo');
    $response->assertSee('Eko Muhammad');
    $response->assertSee('Export PDF');
    $response->assertSee('Export Excel');
    $response->assertSee('Cari siswa...');

    // 3. Export Excel
    $excelResponse = $this->actingAs($user)->get(route('guru.kelas-wali.export-excel'));
    $excelResponse->assertStatus(200);
    $excelResponse->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    // 4. Export PDF
    $pdfResponse = $this->actingAs($user)->get(route('guru.kelas-wali.export-pdf'));
    $pdfResponse->assertStatus(200);
    $pdfResponse->assertHeader('Content-Type', 'application/pdf');
});
