<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kehadiran;
use App\Models\AturanJam;
use App\Models\Semester;
use Illuminate\Support\Carbon;

class DummyKehadiranSeeder extends Seeder
{
    public function run()
    {
        $siswas = Siswa::all();
        $gurus = Guru::all();
        $semester = Semester::first();
        $aturan = AturanJam::first();

        // Hapus data kehadiran lama agar seeder bersih
        Kehadiran::query()->delete();

        $statuses = ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'];
        $jamsHariIni = ['06:05:00', '06:18:00', '06:28:00', '06:42:00', '07:05:00', '07:18:00'];

        // 1. Data Siswa untuk Hari Ini & 30 Hari Kebelakang
        foreach ($siswas as $sIdx => $s) {
            for ($daysAgo = 30; $daysAgo >= 0; $daysAgo--) {
                $date = Carbon::today()->subDays($daysAgo)->toDateString();
                
                // Distribusi status yang bervariasi
                $status = $statuses[($sIdx * 3 + $daysAgo * 7) % count($statuses)];
                $jam = $jamsHariIni[($sIdx + $daysAgo) % count($jamsHariIni)];

                Kehadiran::create([
                    'siswa_id' => $s->id,
                    'guru_id' => null,
                    'aturan_jam_id' => $aturan ? $aturan->id : 1,
                    'semester_id' => $semester ? $semester->id : 1,
                    'tanggal' => $date,
                    'jam_masuk' => in_array($status, ['hadir', 'terlambat']) ? $jam : null,
                    'status' => $status,
                    'keterangan' => 'Presensi Siswa ' . ucfirst($status),
                ]);
            }
        }

        // 2. Data Guru untuk Hari Ini & 30 Hari Kebelakang
        foreach ($gurus as $gIdx => $g) {
            for ($daysAgo = 30; $daysAgo >= 0; $daysAgo--) {
                $date = Carbon::today()->subDays($daysAgo)->toDateString();
                $status = $statuses[($gIdx * 2 + $daysAgo * 5) % count($statuses)];
                $jam = $jamsHariIni[($gIdx + $daysAgo) % count($jamsHariIni)];

                Kehadiran::create([
                    'siswa_id' => null,
                    'guru_id' => $g->id,
                    'aturan_jam_id' => $aturan ? $aturan->id : 1,
                    'semester_id' => $semester ? $semester->id : 1,
                    'tanggal' => $date,
                    'jam_masuk' => in_array($status, ['hadir', 'terlambat']) ? $jam : null,
                    'status' => $status,
                    'keterangan' => 'Presensi Guru ' . ucfirst($status),
                ]);
            }
        }
    }
}
