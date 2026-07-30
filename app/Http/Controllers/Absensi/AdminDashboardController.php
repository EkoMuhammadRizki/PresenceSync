<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Kehadiran;
use App\Models\AturanJam;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;

class AdminDashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Utama (Admin & Kesiswaan).
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // 1. Statistik Row 1
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalGuru = Guru::count();
        $totalKelas = Kelas::where('status', 'aktif')->count();

        // Kehadiran hari ini (hadir + terlambat)
        $kehadiranHariIni = Kehadiran::whereDate('tanggal', $today)->get();
        $totalHadir = $kehadiranHariIni->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalTerlambat = $kehadiranHariIni->where('status', 'terlambat')->count();

        // 2. Daftar kelas untuk filter dropdown select2
        $kelas = Kelas::where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();

        // 3. Tabel Keterlambatan Hari Ini (Row 2, Column 2)
        // Kolom: nama, kelas, status, menit terlambat
        // Sorting berdasarkan waktu masuk terbaru
        $terlambats = Kehadiran::whereDate('tanggal', $today)
            ->where('status', 'terlambat')
            ->with(['siswa.kelas', 'aturanJam'])
            ->get()
            ->map(function ($k) {
                $jamMasukSiswa = Carbon::parse($k->jam_masuk);
                // Default school start time is the check-in rules start time
                $schoolStartTime = $k->aturanJam ? Carbon::parse($k->aturanJam->jam_masuk) : Carbon::parse('07:00:00');
                
                $minutesLate = 0;
                if ($jamMasukSiswa->greaterThan($schoolStartTime)) {
                    $diffSeconds = abs($jamMasukSiswa->diffInSeconds($schoolStartTime));
                    $minutesLate = (int) round($diffSeconds / 60);
                }
                
                // Format display text: less than 1 hour -> X Menit; 1 hour or more -> Y Jam Z Menit
                if ($minutesLate < 60) {
                    $durasiTerlambat = $minutesLate . ' Menit';
                    $durasiTerlambatSingkat = $minutesLate . ' mnt';
                } else {
                    $hours = (int) floor($minutesLate / 60);
                    $mins = $minutesLate % 60;
                    if ($mins > 0) {
                        $durasiTerlambat = $hours . ' Jam ' . $mins . ' Menit';
                        $durasiTerlambatSingkat = $hours . 'j ' . $mins . 'm';
                    } else {
                        $durasiTerlambat = $hours . ' Jam';
                        $durasiTerlambatSingkat = $hours . 'j';
                    }
                }
                
                return [
                    'nama' => $k->siswa ? $k->siswa->nama : 'Siswa',
                    'foto' => ($k->siswa && $k->siswa->user) ? $k->siswa->user->avatar_url : null,
                    'email' => ($k->siswa && $k->siswa->user) ? $k->siswa->user->email : null,
                    'kelas' => ($k->siswa && $k->siswa->kelas) ? $k->siswa->kelas->nama : '-',
                    'status' => 'Terlambat',
                    'menit_terlambat' => $minutesLate,
                    'durasi_terlambat' => $durasiTerlambat,
                    'durasi_terlambat_singkat' => $durasiTerlambatSingkat,
                    'waktu' => $k->jam_masuk,
                ];
            })
            ->sortByDesc('waktu') // Latest first
            ->values();

        // 4. Data Row 3: Persentase Kehadiran per Kelas Hari Ini
        $kehadiranPerKelas = Kelas::where('status', 'aktif')
            ->withCount(['siswas as total_siswa' => function($q) {
                $q->where('status', 'aktif');
            }])
            ->get()
            ->map(function ($k) use ($today) {
                $siswaIds = Siswa::where('kelas_id', $k->id)->where('status', 'aktif')->pluck('id');
                $totalSiswaKelas = count($siswaIds);
                
                $kehadiranKelas = Kehadiran::whereIn('siswa_id', $siswaIds)
                    ->whereDate('tanggal', $today)
                    ->get();

                $hadir = $kehadiranKelas->whereIn('status', ['hadir', 'terlambat'])->count();
                $terlambat = $kehadiranKelas->where('status', 'terlambat')->count();
                $sakitIzin = $kehadiranKelas->whereIn('status', ['sakit', 'izin'])->count();
                $alpha = $kehadiranKelas->where('status', 'alpha')->count();

                $persentase = $totalSiswaKelas > 0 ? round(($hadir / $totalSiswaKelas) * 100) : 0;

                return [
                    'id' => $k->id,
                    'nama' => $k->nama,
                    'tingkat' => $k->tingkat,
                    'total_siswa' => $totalSiswaKelas,
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
                    'sakit_izin' => $sakitIzin,
                    'alpha' => $alpha,
                    'persentase' => $persentase,
                ];
            })
            ->sortByDesc('persentase')
            ->values();

        // 5. Data Row 3: Aktivitas Absensi & Log Terbaru (Terakhir 6 Record Check-in)
        $aktivitasTerbaru = Kehadiran::with(['siswa.user', 'siswa.kelas', 'guru.user'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get()
            ->map(function ($act) {
                $isGuru = !empty($act->guru_id);
                $nama = $isGuru ? ($act->guru->nama ?? 'Guru') : ($act->siswa->nama ?? 'Siswa');
                $role = $isGuru ? 'Guru' : ($act->siswa->kelas->nama ?? 'Siswa');
                $foto = $isGuru 
                    ? ($act->guru->user->avatar_url ?? null)
                    : ($act->siswa->user->avatar_url ?? null);

                return [
                    'nama' => $nama,
                    'role' => $role,
                    'foto' => $foto,
                    'status' => $act->status,
                    'waktu' => $act->jam_masuk ? \Illuminate\Support\Carbon::parse($act->jam_masuk)->format('H:i') : '-',
                    'tanggal' => $act->tanggal,
                    'created_at_human' => \Illuminate\Support\Carbon::parse($act->created_at)->diffForHumans(),
                ];
            });

        $showPanduan = !session()->get('panduan_singkat_shown', false) && !request()->is('absensi/kesiswaan/dashboard');

        return view('pages.absensi.dashboard', compact(
            'totalSiswa', 'totalGuru', 'totalKelas', 'totalHadir', 'totalTerlambat', 'kelas', 'terlambats', 'showPanduan',
            'kehadiranPerKelas', 'aktivitasTerbaru'
        ));
    }

    /**
     * Get Trend Kehadiran Data for ApexCharts and summary cards.
     * Supports two modes:
     * - 'hourly': when start_date === end_date (today). Returns per-slot data (06:00-07:30).
     * - 'daily': when a date range is selected. Returns per-date data.
     */
    public function getTrendData(Request $request)
    {
        // Default: today only
        $today = Carbon::now()->toDateString();
        $startDate = $today;
        $endDate = $today;

        if ($request->filled('start_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->filled('end_date') ? $request->input('end_date') : $startDate;
        }

        $classId = $request->input('kelas_id');
        $isToday = ($startDate === $endDate);

        $totals = ['kehadiran' => 0, 'ketidakhadiran' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];

        if ($isToday) {
            // --- HOURLY MODE ---
            // Slot check-in windows: 06:00-07:30 in 15-minute intervals
            $slots = [
                '06:00', '06:15', '06:30', '06:45',
                '07:00', '07:15', '07:30',
            ];

            $query = Kehadiran::whereDate('kehadirans.tanggal', $startDate)
                ->join('siswas', 'kehadirans.siswa_id', '=', 'siswas.id')
                ->select('kehadirans.jam_masuk', 'kehadirans.status');

            if ($classId) {
                $query->where('siswas.kelas_id', $classId);
            }

            $rows = $query->get();

            // Count totals for summary cards
            foreach ($rows as $row) {
                $status = $row->status;
                if (in_array($status, ['hadir', 'terlambat'])) $totals['kehadiran']++;
                if (in_array($status, ['sakit', 'izin', 'alpha'])) $totals['ketidakhadiran']++;
                if ($status === 'izin') $totals['izin']++;
                if ($status === 'sakit') $totals['sakit']++;
                if ($status === 'alpha') $totals['alpa']++;
            }

            // Build per-slot buckets
            $slotData = [];
            foreach ($slots as $slot) {
                $slotData[$slot] = ['kehadiran' => 0, 'ketidakhadiran' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
            }

            foreach ($rows as $row) {
                if (!$row->jam_masuk) continue;
                $jamMasuk = Carbon::parse($row->jam_masuk);
                // Find which 15-min bucket this falls into
                $slotMinutes = $jamMasuk->hour * 60 + (int)floor($jamMasuk->minute / 15) * 15;
                $slotTime = sprintf('%02d:%02d', intdiv($slotMinutes, 60), $slotMinutes % 60);
                if (!isset($slotData[$slotTime])) {
                    // Clamp to nearest existing slot
                    $slotTime = $slots[0];
                    foreach ($slots as $s) {
                        [$sh, $sm] = explode(':', $s);
                        if ($slotMinutes >= (int)$sh * 60 + (int)$sm) $slotTime = $s;
                    }
                }
                $status = $row->status;
                if (in_array($status, ['hadir', 'terlambat'])) $slotData[$slotTime]['kehadiran']++;
                if (in_array($status, ['sakit', 'izin', 'alpha'])) $slotData[$slotTime]['ketidakhadiran']++;
                if ($status === 'izin') $slotData[$slotTime]['izin']++;
                if ($status === 'sakit') $slotData[$slotTime]['sakit']++;
                if ($status === 'alpha') $slotData[$slotTime]['alpa']++;
            }

            $results = [];
            foreach ($slots as $slot) {
                $results[] = array_merge(['tanggal' => $slot], $slotData[$slot]);
            }

            return response()->json([
                'success' => true,
                'mode' => 'hourly',
                'totals' => $totals,
                'chart' => $results,
            ]);
        }

        // --- DAILY MODE ---
        $query = Kehadiran::whereBetween('kehadirans.tanggal', [$startDate, $endDate])
            ->join('siswas', 'kehadirans.siswa_id', '=', 'siswas.id')
            ->selectRaw("kehadirans.tanggal,
                sum(case when kehadirans.status in ('hadir', 'terlambat') then 1 else 0 end) as kehadiran,
                sum(case when kehadirans.status in ('sakit', 'izin', 'alpha') then 1 else 0 end) as ketidakhadiran,
                sum(case when kehadirans.status = 'izin' then 1 else 0 end) as izin,
                sum(case when kehadirans.status = 'sakit' then 1 else 0 end) as sakit,
                sum(case when kehadirans.status = 'alpha' then 1 else 0 end) as alpa");

        if ($classId) {
            $query->where('siswas.kelas_id', $classId);
        }

        $data = $query->groupBy('kehadirans.tanggal')->orderBy('kehadirans.tanggal')->get();

        $period = CarbonPeriod::create($startDate, $endDate);
        $results = [];

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $results[$dateStr] = [
                'tanggal' => $date->translatedFormat('d M'),
                'kehadiran' => 0, 'ketidakhadiran' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0,
            ];
        }

        foreach ($data as $row) {
            $dateStr = Carbon::parse($row->tanggal)->toDateString();
            if (isset($results[$dateStr])) {
                $results[$dateStr]['kehadiran'] = (int) $row->kehadiran;
                $results[$dateStr]['ketidakhadiran'] = (int) $row->ketidakhadiran;
                $results[$dateStr]['izin'] = (int) $row->izin;
                $results[$dateStr]['sakit'] = (int) $row->sakit;
                $results[$dateStr]['alpa'] = (int) $row->alpa;
                $totals['kehadiran'] += (int) $row->kehadiran;
                $totals['ketidakhadiran'] += (int) $row->ketidakhadiran;
                $totals['izin'] += (int) $row->izin;
                $totals['sakit'] += (int) $row->sakit;
                $totals['alpa'] += (int) $row->alpa;
            }
        }

        return response()->json([
            'success' => true,
            'mode' => 'daily',
            'totals' => $totals,
            'chart' => array_values($results),
        ]);
    }

    /**
     * Get Trend Kehadiran Guru Data for ApexCharts and summary cards.
     * Supports two modes:
     * - 'hourly': when start_date === end_date (today). Returns per-slot data (06:00-07:30).
     * - 'daily': when a date range is selected. Returns per-date data.
     */
    public function getGuruTrendData(Request $request)
    {
        // Default: today only
        $today = Carbon::now()->toDateString();
        $startDate = $today;
        $endDate = $today;

        if ($request->filled('start_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->filled('end_date') ? $request->input('end_date') : $startDate;
        }

        $totalGuruCount = Guru::count();
        $isToday = ($startDate === $endDate);
        $totals = ['kehadiran' => 0, 'ketidakhadiran' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];

        if ($isToday) {
            // --- HOURLY MODE ---
            $slots = [
                '06:00', '06:15', '06:30', '06:45',
                '07:00', '07:15', '07:30',
            ];

            $rows = Kehadiran::whereDate('tanggal', $startDate)
                ->whereNotNull('guru_id')
                ->select('jam_masuk', 'status')
                ->get();

            foreach ($rows as $row) {
                $status = $row->status;
                if (in_array($status, ['hadir', 'terlambat'])) $totals['kehadiran']++;
                if (in_array($status, ['sakit', 'izin', 'alpha'])) $totals['ketidakhadiran']++;
                if ($status === 'izin') $totals['izin']++;
                if ($status === 'sakit') $totals['sakit']++;
                if ($status === 'alpha') $totals['alpa']++;
            }

            $slotData = [];
            foreach ($slots as $slot) {
                $slotData[$slot] = ['kehadiran' => 0, 'ketidakhadiran' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
            }

            foreach ($rows as $row) {
                if (!$row->jam_masuk) continue;
                $jamMasuk = Carbon::parse($row->jam_masuk);
                $slotMinutes = $jamMasuk->hour * 60 + (int)floor($jamMasuk->minute / 15) * 15;
                $slotTime = sprintf('%02d:%02d', intdiv($slotMinutes, 60), $slotMinutes % 60);
                if (!isset($slotData[$slotTime])) {
                    $slotTime = $slots[0];
                    foreach ($slots as $s) {
                        [$sh, $sm] = explode(':', $s);
                        if ($slotMinutes >= (int)$sh * 60 + (int)$sm) $slotTime = $s;
                    }
                }
                $status = $row->status;
                if (in_array($status, ['hadir', 'terlambat'])) $slotData[$slotTime]['kehadiran']++;
                if (in_array($status, ['sakit', 'izin', 'alpha'])) $slotData[$slotTime]['ketidakhadiran']++;
                if ($status === 'izin') $slotData[$slotTime]['izin']++;
                if ($status === 'sakit') $slotData[$slotTime]['sakit']++;
                if ($status === 'alpha') $slotData[$slotTime]['alpa']++;
            }

            $results = [];
            foreach ($slots as $slot) {
                $results[] = array_merge(['tanggal' => $slot], $slotData[$slot]);
            }

            return response()->json([
                'success' => true,
                'mode' => 'hourly',
                'totals' => $totals,
                'chart' => $results,
            ]);
        }

        // --- DAILY MODE ---
        $period = CarbonPeriod::create($startDate, $endDate);
        $results = [];

        $teacherAttendances = Kehadiran::whereBetween('tanggal', [$startDate, $endDate])
            ->whereNotNull('guru_id')
            ->selectRaw("tanggal,
                sum(case when status in ('hadir', 'terlambat') then 1 else 0 end) as kehadiran,
                sum(case when status in ('sakit', 'izin', 'alpha') then 1 else 0 end) as ketidakhadiran,
                sum(case when status = 'izin' then 1 else 0 end) as izin,
                sum(case when status = 'sakit' then 1 else 0 end) as sakit,
                sum(case when status = 'alpha' then 1 else 0 end) as alpa")
            ->groupBy('tanggal')
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->tanggal)->toDateString();
            });

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $att = $teacherAttendances->get($dateStr);

            $hadir = $att ? (int) $att->kehadiran : 0;
            $ketidakhadiran = $att ? (int) $att->ketidakhadiran : 0;
            $izin = $att ? (int) $att->izin : 0;
            $sakit = $att ? (int) $att->sakit : 0;
            $alpa = $att ? (int) $att->alpa : 0;

            if ($date->isWeekday() && !$att && $totalGuruCount > 0) {
                $hadir = $totalGuruCount;
            }

            $results[$dateStr] = [
                'tanggal' => $date->translatedFormat('d M'),
                'kehadiran' => $hadir,
                'ketidakhadiran' => $ketidakhadiran,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
            ];

            $totals['kehadiran'] += $hadir;
            $totals['ketidakhadiran'] += $ketidakhadiran;
            $totals['izin'] += $izin;
            $totals['sakit'] += $sakit;
            $totals['alpa'] += $alpa;
        }

        return response()->json([
            'success' => true,
            'mode' => 'daily',
            'totals' => $totals,
            'chart' => array_values($results),
        ]);
    }
}
