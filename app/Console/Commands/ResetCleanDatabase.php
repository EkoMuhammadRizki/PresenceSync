<?php

namespace App\Console\Commands;

use App\Models\AturanJam;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class ResetCleanDatabase extends Command
{
    protected $signature = 'db:reset-clean {--force : Jalankan tanpa konfirmasi}';
    protected $description = 'Truncate semua data transaksi & akun agar ID mulai dari 1 kembali bersih seperti baru';

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('Apakah Anda yakin ingin me-reset dan mengosongkan semua data database? Semua ID akan kembali ke 1.')) {
            $this->warn('Operasi dibatalkan.');
            return self::SUCCESS;
        }

        $this->info('Memulai pembersihan database...');
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        $tables = [
            'activity_log',
            'fingerprint_sync_logs',
            'kehadiran_mata_pelajaran_details',
            'kehadiran_mata_pelajarans',
            'kehadirans',
            'pengaduans',
            'parent_profiles',
            'siswas',
            'jadwal_pelajarans',
            'mata_pelajarans',
            'kelas',
            'gurus',
            'user_infos',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
            'users',
            'fingerprint_devices',
            'aturan_jams',
            'semesters',
            'tahun_ajarans',
            'roles',
            'permissions',
            'failed_jobs',
            'password_resets',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("  ✓ Truncate tabel: {$table}");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        $this->info('Semua tabel berhasil di-truncate (Auto Increment di-reset ke 1).');

        // 1. Inisialisasi Roles
        $this->info('Membuat Roles standar...');
        $roles = ['admin', 'guru', 'kesiswaan', 'siswa', 'orang_tua', 'editor'];
        foreach ($roles as $roleName) {
            Role::create(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Inisialisasi Akun Admin Utama (ID = 1)
        $this->info('Membuat Akun Admin Utama...');
        $admin = User::create([
            'first_name'        => 'Admin',
            'last_name'         => 'SIAP',
            'email'             => 'admin@sman1ciparay.com',
            'password'          => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $info = new UserInfo();
        $info->company = 'SMAN 1 Ciparay';
        $info->phone = '08123456789';
        $info->country = 'ID';
        $info->user()->associate($admin);
        $info->save();

        // 3. Inisialisasi Master Data Dasar
        $this->info('Membuat Master Data Dasar...');
        $ta = TahunAjaran::create([
            'nama'          => '2025/2026',
            'bulan_mulai'   => '2025-07-01',
            'bulan_selesai' => '2026-06-30',
            'status'        => 'aktif',
        ]);

        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'ganjil',
            'tanggal_mulai'   => '2025-07-01',
            'tanggal_selesai' => '2025-12-31',
            'status'          => 'selesai',
        ]);

        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'genap',
            'tanggal_mulai'   => '2026-01-01',
            'tanggal_selesai' => '2026-06-30',
            'status'          => 'aktif',
        ]);

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        foreach ($hariList as $hari) {
            AturanJam::create([
                'hari'                    => $hari,
                'jam_masuk'               => '07:00:00',
                'toleransi_keterlambatan' => 15,
                'jam_pulang'              => $hari === 'Jumat' ? '11:45:00' : '15:30:00',
                'is_aktif'                => true,
            ]);
        }

        $this->newLine();
        $this->info('===========================================================');
        $this->info('✅ SUKSES! Database telah bersih dan siap digunakan.');
        $this->info('Akun Login Admin:');
        $this->info('  Email    : admin@sman1ciparay.com');
        $this->info('  Password : admin123');
        $this->info('===========================================================');

        return self::SUCCESS;
    }
}
