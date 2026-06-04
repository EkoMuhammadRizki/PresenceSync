<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Jurusan;
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
        $aturanNormal = AturanJam::create([
            'nama'                    => 'Jadwal Normal',
            'jam_masuk'               => '07:00:00',
            'toleransi_keterlambatan' => 15,
            'jam_pulang'              => '15:30:00',
            'is_aktif'                => true,
        ]);

        $aturanRamadhan = AturanJam::create([
            'nama'                    => 'Jadwal Ramadhan',
            'jam_masuk'               => '07:30:00',
            'toleransi_keterlambatan' => 10,
            'jam_pulang'              => '14:00:00',
            'is_aktif'                => false,
        ]);

        // 2. Tahun Ajaran & Semester
        $ta1 = TahunAjaran::create([
            'nama'          => '2025/2026',
            'bulan_mulai'   => '2025-07-01',
            'bulan_selesai' => '2026-06-30',
            'status'        => 'aktif',
        ]);

        $ta2 = TahunAjaran::create([
            'nama'          => '2024/2025',
            'bulan_mulai'   => '2024-07-01',
            'bulan_selesai' => '2025-06-30',
            'status'        => 'selesai',
        ]);

        $semGanjil = Semester::create([
            'tahun_ajaran_id' => $ta1->id,
            'jenis'           => 'ganjil',
            'tanggal_mulai'   => '2025-07-01',
            'tanggal_selesai' => '2025-12-31',
            'status'          => 'selesai',
        ]);

        $semGenap = Semester::create([
            'tahun_ajaran_id' => $ta1->id,
            'jenis'           => 'genap',
            'tanggal_mulai'   => '2026-01-01',
            'tanggal_selesai' => '2026-06-30',
            'status'          => 'aktif',
        ]);

        // 3. Jurusan
        $jurusanIPA = Jurusan::create([
            'kode' => 'IPA',
            'nama' => 'Ilmu Pengetahuan Alam',
            'deskripsi' => 'Fokus pada ilmu fisika, kimia, biologi, dan matematika.',
        ]);

        $jurusanIPS = Jurusan::create([
            'kode' => 'IPS',
            'nama' => 'Ilmu Pengetahuan Sosial',
            'deskripsi' => 'Fokus pada sosiologi, geografi, ekonomi, dan sejarah.',
        ]);

        $jurusanRPL = Jurusan::create([
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Fokus pada pemrograman, pengembangan software, dan database.',
        ]);

        // 4. Guru
        $guru1 = Guru::create([
            'nip'           => '198105122008011003',
            'nama'          => 'Budi Santoso, S.Pd',
            'email'         => 'budisantoso@sekolah.sch.id',
            'no_hp'         => '081234567890',
            'alamat'        => 'Jl. Sukasenang No. 12',
        ]);

        $guru2 = Guru::create([
            'nip'           => '198502152010022004',
            'nama'          => 'Siti Rahayu, M.Pd',
            'email'         => 'sitirahayu@sekolah.sch.id',
            'no_hp'         => '081298765432',
            'alamat'        => 'Jl. Pahlawan No. 45',
        ]);

        $guru3 = Guru::create([
            'nip'           => '199008242019031005',
            'nama'          => 'Hendra Wijaya, S.T',
            'email'         => 'hendrawijaya@sekolah.sch.id',
            'no_hp'         => '085732165498',
            'alamat'        => 'Jl. Cihampelas No. 101',
        ]);

        $guru4 = Guru::create([
            'nip'           => '198811022015042006',
            'nama'          => 'Dewi Lestari, S.Pd',
            'email'         => 'dewilestari@sekolah.sch.id',
            'no_hp'         => '081345678912',
            'alamat'        => 'Jl. Dago No. 8',
        ]);

        // 5. Kelas
        $kelas1 = Kelas::create([
            'jurusan_id' => $jurusanIPA->id,
            'guru_id'    => $guru1->id,
            'nama'       => 'X-1',
            'tingkat'    => '10',
            'status'     => 'aktif',
        ]);

        $kelas2 = Kelas::create([
            'jurusan_id' => $jurusanIPA->id,
            'guru_id'    => $guru3->id,
            'nama'       => 'XI-1',
            'tingkat'    => '11',
            'status'     => 'aktif',
        ]);

        $kelas3 = Kelas::create([
            'jurusan_id' => $jurusanIPS->id,
            'guru_id'    => $guru2->id,
            'nama'       => 'XII-1',
            'tingkat'    => '12',
            'status'     => 'aktif',
        ]);

        // 6. Siswa & User Akun
        $siswaData = [
            ['nama' => 'Ahmad Subarjo', 'nisn' => '0054321098', 'nis' => '10201', 'jk' => 'L', 'kelas' => $kelas1],
            ['nama' => 'Budi Setiadi', 'nisn' => '0054321099', 'nis' => '10202', 'jk' => 'L', 'kelas' => $kelas1],
            ['nama' => 'Candra Wijaya', 'nisn' => '0054321100', 'nis' => '10203', 'jk' => 'L', 'kelas' => $kelas1],
            ['nama' => 'Dedi Kurniawan', 'nisn' => '0054321101', 'nis' => '10204', 'jk' => 'L', 'kelas' => $kelas1],
            ['nama' => 'Erna Lestari', 'nisn' => '0054321102', 'nis' => '10205', 'jk' => 'P', 'kelas' => $kelas1],
            ['nama' => 'Fahmi Idris', 'nisn' => '0061234567', 'nis' => '11301', 'jk' => 'L', 'kelas' => $kelas2],
            ['nama' => 'Gita Gutawa', 'nisn' => '0061234568', 'nis' => '11302', 'jk' => 'P', 'kelas' => $kelas2],
            ['nama' => 'Hendra Setiawan', 'nisn' => '0075678901', 'nis' => '12401', 'jk' => 'L', 'kelas' => $kelas3],
        ];

        $siswas = [];
        foreach ($siswaData as $index => $data) {
            $email = Str::slug($data['nama'], '.') . '@siswa.presencesync.sch.id';
            
            $nameParts = explode(' ', trim($data['nama']), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? $nameParts[0];

            $user = User::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $email,
                'password'   => Hash::make('password123'),
            ]);

            $siswas[] = Siswa::create([
                'user_id'        => $user->id,
                'kelas_id'       => $data['kelas']->id,
                'nama'           => $data['nama'],
                'nisn'           => $data['nisn'],
                'nis'            => $data['nis'],
                'jenis_kelamin'  => $data['jk'],
                'tanggal_lahir'  => '2009-08-15',
                'alamat'         => 'Jl. Sukarno Hatta No. ' . ($index + 12),
                'fingerprint_id' => 'FP00' . ($index + 1),
            ]);
        }

        // 7. Mata Pelajaran
        $mapelMatematika = MataPelajaran::create([
            'guru_id' => $guru1->id,
            'kode'    => 'MTK',
            'nama'    => 'Matematika Peminatan',
        ]);

        $mapelFisika = MataPelajaran::create([
            'guru_id' => $guru3->id,
            'kode'    => 'FIS',
            'nama'    => 'Fisika',
        ]);

        $mapelSosiologi = MataPelajaran::create([
            'guru_id' => $guru2->id,
            'kode'    => 'SOS',
            'nama'    => 'Sosiologi',
        ]);

        // 8. Jadwal Pelajaran
        JadwalPelajaran::create([
            'kelas_id'          => $kelas1->id,
            'mata_pelajaran_id' => $mapelMatematika->id,
            'semester_id'       => $semGenap->id,
            'hari'              => 'Senin',
            'jam_mulai'         => '07:30:00',
            'jam_selesai'       => '09:00:00',
        ]);

        JadwalPelajaran::create([
            'kelas_id'          => $kelas1->id,
            'mata_pelajaran_id' => $mapelFisika->id,
            'semester_id'       => $semGenap->id,
            'hari'              => 'Senin',
            'jam_mulai'         => '09:15:00',
            'jam_selesai'       => '10:45:00',
        ]);

        JadwalPelajaran::create([
            'kelas_id'          => $kelas3->id,
            'mata_pelajaran_id' => $mapelSosiologi->id,
            'semester_id'       => $semGenap->id,
            'hari'              => 'Selasa',
            'jam_mulai'         => '08:00:00',
            'jam_selesai'       => '10:00:00',
        ]);

        // 9. Kehadirans
        $dates = [
            '2026-05-18', // Senin kemarin
            '2026-05-19', // Selasa kemarin
            '2026-05-20', // Hari ini
        ];

        $statuses = ['hadir', 'hadir', 'hadir', 'terlambat', 'sakit', 'izin', 'alpha'];

        foreach ($dates as $date) {
            foreach ($siswas as $idx => $siswa) {
                // Hanya isikan data ke kelas 1 untuk contoh yang padat
                if ($siswa->kelas_id !== $kelas1->id) continue;

                $status = $statuses[($idx + Carbon::parse($date)->day) % count($statuses)];
                
                $jamMasuk = null;
                $jamPulang = null;
                
                if ($status === 'hadir') {
                    $jamMasuk = '06:50:00';
                    $jamPulang = '15:35:00';
                } elseif ($status === 'terlambat') {
                    $jamMasuk = '07:20:00';
                    $jamPulang = '15:30:00';
                }

                Kehadiran::create([
                    'siswa_id'      => $siswa->id,
                    'semester_id'   => $semGenap->id,
                    'aturan_jam_id' => $aturanNormal->id,
                    'tanggal'       => $date,
                    'jam_masuk'     => $jamMasuk,
                    'jam_pulang'    => $jamPulang,
                    'status'        => $status,
                    'keterangan'    => $status === 'izin' ? 'Ada acara keluarga' : ($status === 'sakit' ? 'Demam tinggi' : null),
                ]);
            }
        }
    }
}
