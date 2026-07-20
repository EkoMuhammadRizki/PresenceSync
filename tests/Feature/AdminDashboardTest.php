<?php

use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Kehadiran;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin dashboard renders correctly with statistics, late comers table and trend data endpoint', function () {
    // 1. Create admin user
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->assignRole($adminRole);

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

    $kelas = Kelas::create(['nama' => 'X-IPA', 'tingkat' => 10, 'status' => 'aktif', 'tahun_ajaran_id' => $tahunAjaran->id]);
    $guru = Guru::create([
        'nama' => 'Budi Guru',
        'nip' => '12345678',
        'email' => 'budi@guru.com',
        'no_hp' => '081234567',
        'alamat' => 'Alamat Budi',
    ]);
    $siswaUser = User::factory()->create(['first_name' => 'Riko', 'last_name' => 'Siswa']);
    $siswa = Siswa::create([
        'user_id' => $siswaUser->id,
        'nama' => 'Riko Siswa',
        'nis' => '10001',
        'kelas_id' => $kelas->id,
        'jenis_kelamin' => 'L',
        'status' => 'aktif',
    ]);

    // Create a late attendance record for today
    $kehadiran = Kehadiran::create([
        'siswa_id' => $siswa->id,
        'semester_id' => $semester->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '07:45:00',
        'status' => 'terlambat',
    ]);

    // 3. Request index page
    $response = $this->actingAs($admin)->get('/absensi/dashboard');
    $response->assertStatus(200);

    // Check stats are rendered
    $response->assertSee('Total Siswa');
    $response->assertSee('Total Guru');
    $response->assertSee('Total Kelas');
    
    // Check late comers table rendered the student
    $response->assertSee('Riko Siswa');
    $response->assertSee('X-IPA');
    $response->assertSee('45 Menit'); // 07:45 vs default 07:00 is 45 minutes late

    // 4. Request AJAX trend data
    $ajaxResponse = $this->actingAs($admin)->getJson('/absensi/dashboard/trend-data');
    $ajaxResponse->assertStatus(200);
    $ajaxResponse->assertJsonStructure([
        'success',
        'totals' => [
            'kehadiran',
            'ketidakhadiran',
            'izin',
            'sakit',
            'alpa'
        ],
        'chart' => [
            '*' => [
                'tanggal',
                'kehadiran',
                'ketidakhadiran',
                'izin',
                'sakit',
                'alpa'
            ]
        ]
    ]);
});
