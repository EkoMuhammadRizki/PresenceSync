<?php

namespace App\Console\Commands;

use App\Models\AturanJam;
use App\Models\Guru;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Pengaduan;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\User;
use App\Models\UserInfo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SeedDummyAccounts extends Command
{
    protected $signature   = 'db:seed-dummy {--force : Jalankan tanpa konfirmasi}';
    protected $description = 'Buat akun dummy untuk role Guru, Siswa, dan Kesiswaan lengkap dengan data dummy di dashboard masing-masing';

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('Ini akan menambahkan akun dummy ke database. Lanjutkan?')) {
            $this->warn('Operasi dibatalkan.');
            return self::SUCCESS;
        }

        $this->info('🚀 Memulai pembuatan akun dummy...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // ─── Pastikan semua Role tersedia di sistem Spatie ────────────────────
        foreach (['admin', 'kesiswaan', 'guru', 'siswa'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // ─── Semester & Aturan Jam ────────────────────────────────────────────
        $semester = Semester::where('status', 'aktif')->first();
        if (!$semester) {
            $this->error('❌ Semester aktif tidak ditemukan. Jalankan db:reset-clean terlebih dahulu.');
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
            return self::FAILURE;
        }

        $aturanJam = AturanJam::where('hari', 'Senin')->first();
        if (!$aturanJam) {
            $this->error('❌ AturanJam tidak ditemukan. Jalankan db:reset-clean terlebih dahulu.');
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
            return self::FAILURE;
        }

        // ─── 1. AKUN KESISWAAN ────────────────────────────────────────────────
        $this->info('📋 Membuat akun Kesiswaan...');

        $userKesiswaan = User::where('email', 'kesiswaan@sman1ciparay.com')->first();
        if (!$userKesiswaan) {
            $userKesiswaan = User::create([
                'first_name'        => 'Tim',
                'last_name'         => 'Kesiswaan',
                'email'             => 'kesiswaan@sman1ciparay.com',
                'password'          => Hash::make('kesiswaan123'),
                'email_verified_at' => now(),
            ]);

            $info = new UserInfo();
            $info->company = 'SMAN 1 Ciparay - Bagian Kesiswaan';
            $info->phone   = '08198765432';
            $info->country = 'ID';
            $info->user()->associate($userKesiswaan);
            $info->save();

            $this->line('  ✓ Akun kesiswaan@sman1ciparay.com (kesiswaan123) dibuat');
        } else {
            $this->line('  ⚠ Akun kesiswaan sudah ada, dilewati.');
        }

        if (!$userKesiswaan->hasRole('kesiswaan')) {
            $userKesiswaan->assignRole('kesiswaan');
        }

        // ─── 2. AKUN GURU (dengan Wali Kelas) ────────────────────────────────
        $this->info('👨‍🏫 Membuat akun Guru...');

        $userGuru = User::where('email', 'budi.santoso@sman1ciparay.com')->first();
        if (!$userGuru) {
            $userGuru = User::create([
                'first_name'        => 'Budi',
                'last_name'         => 'Santoso',
                'email'             => 'budi.santoso@sman1ciparay.com',
                'password'          => Hash::make('guru123'),
                'email_verified_at' => now(),
            ]);

            $infoGuru = new UserInfo();
            $infoGuru->company = 'SMAN 1 Ciparay';
            $infoGuru->phone   = '081234567890';
            $infoGuru->country = 'ID';
            $infoGuru->user()->associate($userGuru);
            $infoGuru->save();
        }

        if (!$userGuru->hasRole('guru')) {
            $userGuru->assignRole('guru');
        }

        $guru = Guru::where('user_id', $userGuru->id)->orWhere('nip', '198501012010011001')->first();
        if (!$guru) {
            $guru = Guru::create([
                'user_id'            => $userGuru->id,
                'nama'               => 'Drs. Budi Santoso, M.Pd',
                'nip'                => '198501012010011001',
                'jenis_kelamin'      => 'L',
                'tempat_lahir'       => 'Bandung',
                'tanggal_lahir'      => '1985-01-01',
                'agama'              => 'Islam',
                'alamat'             => 'Jl. Raya Ciparay No. 12, Bandung',
                'no_hp'              => '081234567890',
                'email'              => 'budi.santoso@sman1ciparay.com',
                'status'             => 'aktif',
                'status_kepegawaian' => 'PNS',
                'tugas_tambahan'     => 'Wali Kelas X IPA 1',
            ]);

            $this->line('  ✓ Guru: Drs. Budi Santoso, M.Pd (NIP: 198501012010011001 / guru123) dibuat');
        } else {
            $this->line('  ⚠ Akun guru sudah ada, mengambil data existing.');
        }

        // ─── Mata Pelajaran untuk Guru ────────────────────────────────────────
        $mapelBio = MataPelajaran::where('nama', 'Biologi')->where('guru_id', $guru->id)->first();
        if (!$mapelBio) {
            $mapelBio = MataPelajaran::create([
                'nama'    => 'Biologi',
                'kode'    => 'BIO',
                'guru_id' => $guru->id,
            ]);
        }

        // ─── Kelas Wali ───────────────────────────────────────────────────────
        $kelas = Kelas::where('guru_id', $guru->id)->first();
        if (!$kelas) {
            $kelas = Kelas::create([
                'nama'    => 'X IPA 1',
                'tingkat' => '10',
                'guru_id' => $guru->id,
                'status'  => 'aktif',
            ]);
            $this->line('  ✓ Kelas X IPA 1 (wali: Budi Santoso) dibuat');
        }

        // ─── 3. AKUN SISWA DUMMY (5 siswa di kelas X IPA 1) ──────────────────
        $this->info('🎓 Membuat akun Siswa dummy...');

        $siswaData = [
            // [nama, nis, jk, is_sekretaris, password_label]
            ['Rina Wulandari',    '2024100101', 'P', false, 'siswa123'],
            ['Ahmad Fauzi',       '2024100102', 'L', true,  'siswa123'],
            ['Siti Nurhayati',    '2024100103', 'P', false, 'siswa123'],
            ['Deni Ramadhan',     '2024100104', 'L', false, 'siswa123'],
            ['Mega Putri Lestari','2024100105', 'P', false, 'siswa123'],
        ];

        $createdSiswas = [];
        foreach ($siswaData as [$nama, $nis, $jk, $isSek, $pasLabel]) {
            $firstName = explode(' ', $nama)[0];
            $lastName  = implode(' ', array_slice(explode(' ', $nama), 1)) ?: '-';
            $email     = strtolower(str_replace(' ', '.', $nama)) . '@siap.siswa';

            // Ambil atau buat User
            $userSiswa = User::where('email', $email)->first();
            if (!$userSiswa) {
                $userSiswa = User::create([
                    'first_name'        => $firstName,
                    'last_name'         => $lastName,
                    'email'             => $email,
                    'password'          => Hash::make($pasLabel),
                    'email_verified_at' => now(),
                ]);
            }

            if (!$userSiswa->hasRole('siswa')) {
                $userSiswa->assignRole('siswa');
            }

            if (!UserInfo::where('user_id', $userSiswa->id)->exists()) {
                $infoSiswa = new UserInfo();
                $infoSiswa->user()->associate($userSiswa);
                $infoSiswa->save();
            }

            $siswa = Siswa::where('nis', $nis)->orWhere('user_id', $userSiswa->id)->first();
            if (!$siswa) {
                $siswa = Siswa::create([
                    'user_id'       => $userSiswa->id,
                    'kelas_id'      => $kelas->id,
                    'nama'          => $nama,
                    'nis'           => $nis,
                    'nisn'          => '00' . $nis,
                    'jenis_kelamin' => $jk,
                    'tempat_lahir'  => 'Bandung',
                    'tanggal_lahir' => '2007-06-15',
                    'agama'         => 'Islam',
                    'alamat'        => 'Jl. Contoh No. ' . rand(1, 100) . ', Ciparay',
                    'no_hp'         => '08' . rand(1000000000, 9999999999),
                    'nama_orang_tua'=> 'Orang Tua ' . $firstName,
                    'status'        => 'aktif',
                    'is_sekretaris' => $isSek,
                ]);

                $sekLabel = $isSek ? ' [SEKRETARIS]' : '';
                $this->line("  ✓ Siswa: {$nama} (NIS: {$nis} / {$pasLabel}){$sekLabel}");
            } else {
                $this->line("  ⚠ Siswa NIS {$nis} sudah ada, dilewati.");
            }

            $createdSiswas[] = $siswa;
        }

        // ─── 4. DATA KEHADIRAN 2 BULAN (Jun–Jul 2026) ────────────────────────
        $this->info('📅 Membuat data kehadiran 2 bulan (Jun–Jul 2026)...');

        $startDate = Carbon::parse('2026-06-01');
        $endDate   = Carbon::parse('2026-07-31');

        $hariKerja = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            if ($current->isWeekday()) { // Senin–Jumat
                $hariKerja[] = $current->toDateString();
            }
            $current->addDay();
        }

        // Hapus kehadiran lama untuk siswa dummy & guru agar di-refresh bersih
        $dummySiswaIds = collect($createdSiswas)->pluck('id')->toArray();
        Kehadiran::whereIn('siswa_id', $dummySiswaIds)->delete();
        Kehadiran::where('guru_id', $guru->id)->whereNull('siswa_id')->delete();

        $statusPool = [
            'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir',  // 8x
            'terlambat', 'terlambat',                                                    // 2x
            'izin',                                                                      // 1x
            'sakit',                                                                     // 1x
            'alpha',                                                                     // 1x
        ];

        $getJamMasuk = function (string $status): ?string {
            return match ($status) {
                'hadir'     => '06:' . str_pad((string)rand(30, 58), 2, '0', STR_PAD_LEFT) . ':' . str_pad((string)rand(0, 59), 2, '0', STR_PAD_LEFT),
                'terlambat' => '07:' . str_pad((string)rand(16, 45), 2, '0', STR_PAD_LEFT) . ':' . str_pad((string)rand(0, 59), 2, '0', STR_PAD_LEFT),
                default     => null,
            };
        };

        $totalKehadiran = 0;

        foreach ($createdSiswas as $sIdx => $siswa) {
            $poolSize = count($statusPool);

            foreach ($hariKerja as $idx => $tgl) {
                $dayName = Carbon::parse($tgl)->locale('id')->isoFormat('dddd');
                $aj = AturanJam::where('hari', ucfirst(strtolower($dayName)))->first() ?? $aturanJam;

                $status = $statusPool[($idx + $sIdx) % $poolSize];
                $jamMasuk = $getJamMasuk($status);
                $keterangan = match ($status) {
                    'izin'  => 'Izin keperluan keluarga',
                    'sakit' => 'Sakit demam',
                    default => null,
                };

                Kehadiran::create([
                    'siswa_id'      => $siswa->id,
                    'guru_id'       => $guru->id,
                    'semester_id'   => $semester->id,
                    'aturan_jam_id' => $aj->id,
                    'tanggal'       => $tgl,
                    'jam_masuk'     => $jamMasuk,
                    'jam_pulang'    => in_array($status, ['hadir', 'terlambat']) ? '15:30:00' : null,
                    'status'        => $status,
                    'keterangan'    => $keterangan,
                    'source'        => 'manual',
                ]);
                $totalKehadiran++;
            }
        }

        // Kehadiran Guru untuk hari-hari kerja Jun–Jul 2026
        foreach ($hariKerja as $tgl) {
            $dayName = Carbon::parse($tgl)->locale('id')->isoFormat('dddd');
            $aj = AturanJam::where('hari', ucfirst(strtolower($dayName)))->first() ?? $aturanJam;

            Kehadiran::create([
                'siswa_id'      => null,
                'guru_id'       => $guru->id,
                'semester_id'   => $semester->id,
                'aturan_jam_id' => $aj->id,
                'tanggal'       => $tgl,
                'jam_masuk'     => '06:40:00',
                'jam_pulang'    => '15:30:00',
                'status'        => 'hadir',
                'source'        => 'manual',
            ]);
            $totalKehadiran++;
        }

        // ─── Tambahan Khusus HARI INI untuk demo dashboard Guru & Kesiswaan ───
        $todayDate = Carbon::today()->toDateString();
        $todayDayName = Carbon::today()->locale('id')->isoFormat('dddd');
        $ajToday = AturanJam::where('hari', ucfirst(strtolower($todayDayName)))->first() ?? $aturanJam;

        // Siswa 1 (Rina) - Hadir
        Kehadiran::create([
            'siswa_id'      => $createdSiswas[0]->id,
            'guru_id'       => $guru->id,
            'semester_id'   => $semester->id,
            'aturan_jam_id' => $ajToday->id,
            'tanggal'       => $todayDate,
            'jam_masuk'     => '06:45:15',
            'jam_pulang'    => '15:30:00',
            'status'        => 'hadir',
            'keterangan'    => 'Tepat Waktu',
            'source'        => 'manual',
        ]);
        $totalKehadiran++;

        // Siswa 2 (Ahmad Fauzi) - Hadir
        Kehadiran::create([
            'siswa_id'      => $createdSiswas[1]->id,
            'guru_id'       => $guru->id,
            'semester_id'   => $semester->id,
            'aturan_jam_id' => $ajToday->id,
            'tanggal'       => $todayDate,
            'jam_masuk'     => '06:52:30',
            'jam_pulang'    => '15:30:00',
            'status'        => 'hadir',
            'keterangan'    => 'Tepat Waktu',
            'source'        => 'manual',
        ]);
        $totalKehadiran++;

        // Siswa 3 (Siti Nurhayati) - Terlambat
        Kehadiran::create([
            'siswa_id'      => $createdSiswas[2]->id,
            'guru_id'       => $guru->id,
            'semester_id'   => $semester->id,
            'aturan_jam_id' => $ajToday->id,
            'tanggal'       => $todayDate,
            'jam_masuk'     => '07:18:20',
            'jam_pulang'    => '15:30:00',
            'status'        => 'terlambat',
            'keterangan'    => 'Terlambat 18 menit',
            'source'        => 'manual',
        ]);
        $totalKehadiran++;

        // Siswa 4 (Deni Ramadhan) - Izin
        Kehadiran::create([
            'siswa_id'      => $createdSiswas[3]->id,
            'guru_id'       => $guru->id,
            'semester_id'   => $semester->id,
            'aturan_jam_id' => $ajToday->id,
            'tanggal'       => $todayDate,
            'jam_masuk'     => null,
            'jam_pulang'    => null,
            'status'        => 'izin',
            'keterangan'    => 'Izin urusan keluarga',
            'source'        => 'manual',
        ]);
        $totalKehadiran++;

        // Siswa 5 (Mega Putri Lestari) sengaja TIDAK dibuatkan record hari ini agar berstatus "Belum Absen"

        // Guru (Drs. Budi Santoso) - Hadir hari ini
        Kehadiran::create([
            'siswa_id'      => null,
            'guru_id'       => $guru->id,
            'semester_id'   => $semester->id,
            'aturan_jam_id' => $ajToday->id,
            'tanggal'       => $todayDate,
            'jam_masuk'     => '06:40:00',
            'jam_pulang'    => '15:30:00',
            'status'        => 'hadir',
            'source'        => 'manual',
        ]);
        $totalKehadiran++;

        $this->line("  ✓ Total {$totalKehadiran} record kehadiran (Juni–Juli 2026 + Hari Ini beragam) dibuat");

        // ─── 5. DATA PENGADUAN dari Siswa Sekretaris ─────────────────────────
        $this->info('📢 Membuat data pengaduan...');

        $sekretaris = collect($createdSiswas)->firstWhere('is_sekretaris', true);
        if ($sekretaris) {
            Pengaduan::where('siswa_id', $sekretaris->id)->delete();
            $pengaduanData = [
                [
                    'tanggal'   => '2026-06-10',
                    'deskripsi' => 'Mohon perhatian Bapak/Ibu Wali Kelas, terdapat beberapa siswa yang sering tidak membawa alat tulis sehingga mengganggu jalannya pembelajaran. Mohon ada tindakan lebih lanjut.',
                ],
                [
                    'tanggal'   => '2026-07-02',
                    'deskripsi' => 'Ingin melaporkan bahwa beberapa siswa di kelas sering terlambat pada jam pertama. Kami berharap ada penanganan dari pihak sekolah agar kedisiplinan kelas dapat terjaga dengan baik.',
                ],
                [
                    'tanggal'   => '2026-07-20',
                    'deskripsi' => 'Fasilitas kipas angin di ruang kelas X IPA 1 rusak sejak seminggu lalu dan belum ada perbaikan. Suasana kelas sangat panas terutama saat jam siang. Mohon segera diperbaiki.',
                ],
            ];

            foreach ($pengaduanData as $p) {
                Pengaduan::create([
                    'siswa_id'  => $sekretaris->id,
                    'tanggal'   => $p['tanggal'],
                    'deskripsi' => $p['deskripsi'],
                ]);
            }
            $this->line('  ✓ 3 pengaduan dari siswa sekretaris dibuat');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        // ─── Ringkasan Akhir ──────────────────────────────────────────────────
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║              ✅ AKUN DUMMY BERHASIL DIBUAT!                  ║');
        $this->info('╠══════════════════════════════════════════════════════════════╣');
        $this->info('║  KESISWAAN                                                   ║');
        $this->info('║    Email    : kesiswaan@sman1ciparay.com                     ║');
        $this->info('║    Password : kesiswaan123                                   ║');
        $this->info('╠══════════════════════════════════════════════════════════════╣');
        $this->info('║  GURU (Wali Kelas X IPA 1)                                  ║');
        $this->info('║    NIP      : 198501012010011001                             ║');
        $this->info('║    Password : guru123                                        ║');
        $this->info('╠══════════════════════════════════════════════════════════════╣');
        $this->info('║  SISWA                                                       ║');
        $this->info('║    NIS      : 2024100101 (Rina Wulandari)                   ║');
        $this->info('║    NIS      : 2024100102 (Ahmad Fauzi - SEKRETARIS)         ║');
        $this->info('║    NIS      : 2024100103 s/d 2024100105 (siswa lainnya)     ║');
        $this->info('║    Password : siswa123 (semua)                               ║');
        $this->info('╠══════════════════════════════════════════════════════════════╣');
        $this->info('║  DATA DUMMY                                                  ║');
        $this->info('║    Kehadiran : Jun–Jul 2026 (variasi hadir/izin/sakit/alpha) ║');
        $this->info('║    Pengaduan : 3 pengaduan dari sekretaris kelas             ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');

        return self::SUCCESS;
    }
}
