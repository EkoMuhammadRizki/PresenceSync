<?php

use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Carbon\Carbon;

test('authenticated student can view the SIMPEG monthly attendance grid on dashboard', function () {
    $user = User::factory()->create();
    $kelas = Kelas::create([
        'nama' => 'X - 1',
        'tingkat' => 10,
        'status' => 'aktif'
    ]);
    
    $siswa = Siswa::create([
        'id' => $user->id,
        'user_id' => $user->id,
        'kelas_id' => $kelas->id,
        'nama' => 'Test Student Name',
        'nis' => '12345',
        'jenis_kelamin' => 'L',
    ]);

    $response = $this->actingAs($user)->get(route('siswa.dashboard', [
        'periode' => '202607'
    ]));

    $response->assertStatus(200);
    $response->assertSee('Daftar Kehadiran');
    $response->assertSee('Rab, 01-07-2026'); // First day of July 2026
    $response->assertSee('Juli 2026');
});

test('authenticated student can export their monthly attendance grid to Excel', function () {
    $user = User::factory()->create();
    $kelas = Kelas::create([
        'nama' => 'X - 1',
        'tingkat' => 10,
        'status' => 'aktif'
    ]);
    
    $siswa = Siswa::create([
        'id' => $user->id,
        'user_id' => $user->id,
        'kelas_id' => $kelas->id,
        'nama' => 'Test Student Name',
        'nis' => '12345',
        'jenis_kelamin' => 'L',
    ]);

    $response = $this->actingAs($user)->get(route('siswa.dashboard.export', [
        'periode' => '202607'
    ]));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->assertHeader('Content-Disposition', 'attachment; filename="Laporan_Kehadiran_Test_Student_Name_202607.xlsx"');
});
