<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\MataPelajaran;
use App\Models\AturanJam;
use App\Models\JadwalPelajaran;
use App\Models\Kehadiran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run()
    {
        // 1. Aturan Jam
        $aturanNormal = AturanJam::updateOrCreate(['hari' => 'Senin'], [
            'jam_masuk'               => '07:00:00',
            'toleransi_keterlambatan' => 15,
            'jam_pulang'              => '15:30:00',
            'is_aktif'                => true,
        ]);

        $aturanRamadhan = AturanJam::updateOrCreate(['hari' => 'Selasa'], [
            'jam_masuk'               => '07:30:00',
            'toleransi_keterlambatan' => 10,
            'jam_pulang'              => '14:00:00',
            'is_aktif'                => false,
        ]);

        // 2. Tahun Ajaran & Semester
        $ta1 = TahunAjaran::updateOrCreate(['nama' => '2025/2026'], [
            'bulan_mulai'   => '2025-07-01',
            'bulan_selesai' => '2026-06-30',
            'status'        => 'aktif',
        ]);

        $ta2 = TahunAjaran::updateOrCreate(['nama' => '2024/2025'], [
            'bulan_mulai'   => '2024-07-01',
            'bulan_selesai' => '2025-06-30',
            'status'        => 'selesai',
        ]);

        $semGanjil = Semester::updateOrCreate([
            'tahun_ajaran_id' => $ta1->id,
            'jenis'           => 'ganjil',
        ], [
            'tanggal_mulai'   => '2025-07-01',
            'tanggal_selesai' => '2025-12-31',
            'status'          => 'selesai',
        ]);

        $semGenap = Semester::updateOrCreate([
            'tahun_ajaran_id' => $ta1->id,
            'jenis'           => 'genap',
        ], [
            'tanggal_mulai'   => '2026-01-01',
            'tanggal_selesai' => '2026-06-30',
            'status'          => 'aktif',
        ]);

        // 4. Guru
        $userGuru = User::updateOrCreate(['email' => 'guru@demo.com'], [
            'first_name' => 'Budi',
            'last_name'  => 'Santoso, S.Pd',
            'password'   => Hash::make('demo'),
        ]);

        $guru1 = Guru::updateOrCreate(['user_id' => $userGuru->id], [
            'nip'     => '198105122008011003',
            'nama'    => 'Budi Santoso, S.Pd',
            'email'   => 'guru@demo.com',
            'no_hp'   => '081234567890',
            'alamat'  => 'Jl. Sukasenang No. 12',
        ]);

        // 5. Kelas
        $kelas1 = Kelas::updateOrCreate([
            'guru_id'    => $guru1->id,
            'nama'       => 'X-1',
            'tingkat'    => '10',
        ], [
            'status'     => 'aktif',
        ]);

        // 6. Siswa & User Akun
        $userSiswa = User::updateOrCreate(['email' => 'siswa@demo.com'], [
            'first_name' => 'Ahmad',
            'last_name'  => 'Subarjo',
            'password'   => Hash::make('demo'),
        ]);

        $siswa1 = Siswa::updateOrCreate(['user_id' => $userSiswa->id], [
            'kelas_id'       => $kelas1->id,
            'is_sekretaris'  => true,
            'nama'           => 'Ahmad Subarjo',
            'nisn'           => '0054321098',
            'nis'            => '10201',
            'jenis_kelamin'  => 'L',
            'tanggal_lahir'  => '2009-08-15',
            'alamat'         => 'Jl. Sukarno Hatta No. 12',
            'fingerprint_id' => 'FP001',
        ]);

        // 7. Mata Pelajaran
        $mapelMatematika = MataPelajaran::updateOrCreate(['kode' => 'MTK'], [
            'guru_id' => $guru1->id,
            'nama'    => 'Matematika Peminatan',
        ]);

        // 8. Jadwal Pelajaran
        JadwalPelajaran::updateOrCreate([
            'kelas_id'          => $kelas1->id,
            'mata_pelajaran_id' => $mapelMatematika->id,
            'semester_id'       => $semGenap->id,
            'hari'              => 'Senin',
        ], [
            'jam_mulai'         => '07:30:00',
            'jam_selesai'       => '09:00:00',
        ]);

        // 9. Kehadirans (EMPTY - No Kehadiran records created)

        // 10. Assign Guru role to existing guru users
        $guru1->user->assignRole('guru');

        // 11. Kesiswaan Account
        $kesiswaanUser = User::updateOrCreate(['email' => 'kesiswaan@demo.com'], [
            'first_name'        => 'Ratna',
            'last_name'         => 'Sari, S.Pd',
            'password'          => Hash::make('demo'),
            'email_verified_at' => now(),
        ]);
        $kesiswaanUser->assignRole('kesiswaan');

        Guru::updateOrCreate(['user_id' => $kesiswaanUser->id], [
            'nip'     => '199203172020042007',
            'nama'    => 'Ratna Sari, S.Pd',
            'email'   => 'kesiswaan@demo.com',
            'no_hp'   => '081256789012',
            'alamat'  => 'Jl. Merdeka No. 77',
        ]);

        // 12. Orang Tua Accounts
        $orangTua1 = User::updateOrCreate(['email' => 'orangtua@demo.com'], [
            'first_name'        => 'Subarjo',
            'last_name'         => 'Hidayat',
            'password'          => Hash::make('demo'),
            'email_verified_at' => now(),
        ]);
        $orangTua1->assignRole('orang_tua');
        $siswa1->update([
            'nama_orang_tua'    => 'Subarjo Hidayat',
            'orang_tua_user_id' => $orangTua1->id,
        ]);
    }
}
