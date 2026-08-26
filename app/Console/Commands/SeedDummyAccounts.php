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

        $existingKesiswaan = User::where('email', 'kesiswaan@sman1ciparay.com')->first();
        if ($existingKesiswaan) {
            $this->line('  ⚠ Akun kesiswaan sudah ada, dilewati.');
            $userKesiswaan = $existingKesiswaan;
        } else {
            $userKesiswaan = User::create([
                'first_name'        => 'Tim',
                'last_name'         => 'Kesiswaan',
                'email'             => 'kesiswaan@sman1ciparay.com',
                'password'          => Hash::make('kesiswaan123'),
                'email_verified_at' => now(),
            ]);
            $userKesiswaan->assignRole('kesiswaan');

            $info = new UserInfo();
            $info->company = 'SMAN 1 Ciparay - Bagian Kesiswaan';
            $info->phone   = '08198765432';
            $info->country = 'ID';
            $info->user()->associate($userKesiswaan);
            $info->save();

            $this->line('  ✓ Akun kesiswaan@sman1ciparay.com (kesiswaan123) dibuat');
        }

        // ─── 2. AKUN GURU (dengan Wali Kelas) ────────────────────────────────
        $this->info('👨‍🏫 Membuat akun Guru...');

        $existingGuruUser = User::where('email', 'budi.santoso@sman1ciparay.com')->first();
        if ($existingGuruUser) {
            $this->line('  ⚠ Akun guru sudah ada, mengambil data existing.');
            $userGuru = $existingGuruUser;
            $guru     = Guru::where('user_id', $userGuru->id)->first();
        } else {
            $userGuru = User::create([
                'first_name'        => 'Budi',
                'last_name'         => 'Santoso',
                'email'             => 'budi.santoso@sman1ciparay.com',
                'password'          => Hash::make('guru123'),
                'email_verified_at' => now(),
            ]);
            $userGuru->assignRole('guru');

            $infoGuru = new UserInfo();
            $infoGuru->company = 'SMAN 1 Ciparay';
            $infoGuru->phone   = '081234567890';
            $infoGuru->country = 'ID';
            $infoGuru->user()->associate($userGuru);
            $infoGuru->save();

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
            $existingSiswa = Siswa::where('nis', $nis)->first();
            if ($existingSiswa) {
                $this->line("  ⚠ Siswa NIS {$nis} sudah ada, dilewati.");
                $createdSiswas[] = $existingSiswa;
                continue;
            }

            // Buat user untuk siswa
            $firstName = explode(' ', $nama)[0];
            $lastName  = implode(' ', array_slice(explode(' ', $nama), 1)) ?: '-';
            $email     = strtolower(str_replace(' ', '.', $nama)) . '@siap.siswa';

            $userSiswa = User::create([
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'email'             => $email,
                'password'          => Hash::make($pasLabel),
                'email_verified_at' => now(),
            ]);
            $userSiswa->assignRole('siswa');

            $infoSiswa = new UserInfo();
            $infoSiswa->user()->associate($userSiswa);
            $infoSiswa->save();

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

            $createdSiswas[] = $siswa;
            $sekLabel = $isSek ? ' [SEKRETARIS]' : '';
            $this->line("  ✓ Siswa: {$nama} (NIS: {$nis} / {$pasLabel}){$sekLabel}");
        }

        // ─── 4. DATA KEHADIRAN 2 BULAN (Jun–Jul 2026) ────────────────────────
        $this->info('📅 Membuat data kehadiran 2 bulan (Jun–Jul 2026)...');

        // Status distribusi: realistis dengan mayoritas hadir
        $statusPool = [
            'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir',  // 8x
            'terlambat', 'terlambat',                                                    // 2x
            'izin',                                                                      // 1x
            'sakit',                                                                     // 1x
            'alpha',                                                                     // 1x
        ];

        // Jam masuk dummy sesuai status
        $getJamMasuk = function (string $status): ?string {
            return match ($status) {
                'hadir'     => '06:5' . rand(0, 9) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT),
                'terlambat' => '07:' . rand(16, 45) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT),
                default     => null,
            };
        };

        $startDate = Carbon::parse('2026-06-02'); // Senin pertama Juni
        $endDate   = Carbon::parse('2026-07-31');

        $hariKerja = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            if ($current->isWeekday()) { // Senin–Jumat
                $hariKerja[] = $current->toDateString();
            }
            $current->addDay();
        }

        $totalKehadiran = 0;
        foreach ($createdSiswas as $siswa) {
            // Cek apakah sudah ada kehadiran untuk siswa ini
            $existingCount = Kehadiran::where('siswa_id', $siswa->id)
                ->whereYear('tanggal', 2026)
                ->count();
            if ($existingCount > 0) {
                $this->line("  ⚠ Kehadiran siswa {$siswa->nama} sudah ada ({$existingCount} record), dilewati.");
                continue;
            }

            shuffle($statusPool); // Randomize order per siswa
            $poolSize = count($statusPool);

            foreach ($hariKerja as $idx => $tgl) {
                $status   = $statusPool[$idx % $poolSize];
                $jamMasuk = $getJamMasuk($status);

                // Tentukan aturan jam berdasarkan hari
                $dayName = Carbon::parse($tgl)->locale('id')->isoFormat('dddd');
                $aj = AturanJam::where('hari', ucfirst(strtolower($dayName)))->first() ?? $aturanJam;

                Kehadiran::create([
                    'siswa_id'        => $siswa->id,
                    'guru_id'         => $guru->id,
                    'semester_id'     => $semester->id,
                    'aturan_jam_id'   => $aj->id,
                    'tanggal'         => $tgl,
                    'jam_masuk'       => $jamMasuk,
                    'jam_pulang'      => in_array($status, ['hadir', 'terlambat']) ? '15:30:00' : null,
                    'status'          => $status,
                    'keterangan'      => match ($status) {
                        'izin'  => 'Izin keperluan keluarga',
                        'sakit' => 'Sakit demam',
                        'alpha' => null,
                        default => null,
                    },
                    'source'          => 'manual',
                ]);
                $totalKehadiran++;
            }
        }
        $this->line("  ✓ Total {$totalKehadiran} record kehadiran dibuat");

        // ─── 5. DATA PENGADUAN dari Siswa Sekretaris ─────────────────────────
        $this->info('📢 Membuat data pengaduan...');

        $sekretaris = collect($createdSiswas)->firstWhere('is_sekretaris', true);
        if ($sekretaris) {
            $existingPengaduan = Pengaduan::where('siswa_id', $sekretaris->id)->count();
            if ($existingPengaduan === 0) {
                $pengaduanData = [
                    [
                        'tanggal'  => '2026-06-10',
                        'deskripsi' => 'Mohon perhatian Bapak/Ibu Wali Kelas, terdapat beberapa siswa yang sering tidak membawa alat tulis sehingga mengganggu jalannya pembelajaran. Mohon ada tindakan lebih lanjut.',
                    ],
                    [
                        'tanggal'  => '2026-07-02',
                        'deskripsi' => 'Ingin melaporkan bahwa beberapa siswa di kelas sering terlambat pada jam pertama. Kami berharap ada penanganan dari pihak sekolah agar kedisiplinan kelas dapat terjaga dengan baik.',
                    ],
                    [
                        'tanggal'  => '2026-07-15',
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
            } else {
                $this->line("  ⚠ Pengaduan sudah ada ({$existingPengaduan} record), dilewati.");
            }
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
