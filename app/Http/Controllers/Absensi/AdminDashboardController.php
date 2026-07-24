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

        $showPanduan = !session()->get('panduan_singkat_shown', false) && !request()->is('absensi/kesiswaan/dashboard');

        return view('pages.absensi.dashboard', compact(
            'totalSiswa', 'totalGuru', 'totalKelas', 'totalHadir', 'totalTerlambat', 'kelas', 'terlambats', 'showPanduan'
        ));
    }

    /**
     * Get Trend Kehadiran Data for ApexCharts and summary cards.
     */
    public function getTrendData(Request $request)
    {
        // Default range: last 7 days
        $startDate = Carbon::now()->subDays(6)->toDateString();
        $endDate = Carbon::now()->toDateString();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
        }

        $classId = $request->input('kelas_id');

        // Query status counts grouped by date
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

        $data = $query->groupBy('kehadirans.tanggal')
            ->orderBy('kehadirans.tanggal')
            ->get();

        // Fill missing dates
        $period = CarbonPeriod::create($startDate, $endDate);
        $results = [];
        $totals = [
            'kehadiran' => 0,
            'ketidakhadiran' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
        ];

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $results[$dateStr] = [
                'tanggal' => $date->translatedFormat('d M'),
                'kehadiran' => 0,
                'ketidakhadiran' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alpa' => 0,
            ];
        }

        foreach ($data as $row) {
            // Group raw date string
            $dateStr = Carbon::parse($row->tanggal)->toDateString();
            if (isset($results[$dateStr])) {
                $results[$dateStr]['kehadiran'] = (int) $row->kehadiran;
                $results[$dateStr]['ketidakhadiran'] = (int) $row->ketidakhadiran;
                $results[$dateStr]['izin'] = (int) $row->izin;
                $results[$dateStr]['sakit'] = (int) $row->sakit;
                $results[$dateStr]['alpa'] = (int) $row->alpa;

                // Accumulate totals
                $totals['kehadiran'] += (int) $row->kehadiran;
                $totals['ketidakhadiran'] += (int) $row->ketidakhadiran;
                $totals['izin'] += (int) $row->izin;
                $totals['sakit'] += (int) $row->sakit;
                $totals['alpa'] += (int) $row->alpa;
            }
        }

        return response()->json([
            'success' => true,
            'totals' => $totals,
            'chart' => array_values($results),
        ]);
    }

    /**
     * Get Trend Kehadiran Guru Data for ApexCharts and summary cards.
     */
    public function getGuruTrendData(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth()->toDateString();
        $endDate = Carbon::now()->endOfMonth()->toDateString();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
        }

        $totalGuruCount = Guru::count();

        $period = CarbonPeriod::create($startDate, $endDate);
        $results = [];
        $totals = [
            'kehadiran' => 0,
            'ketidakhadiran' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
        ];

        // Fetch teacher attendances
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
            'totals' => $totals,
            'chart' => array_values($results),
        ]);
    }
}
