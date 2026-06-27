<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Carbon;

class KesiswaanDashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Kesiswaan (Rekap Seluruh Sekolah).
     */
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        // Total seluruh siswa aktif
        $totalSiswa = Siswa::where('status', 'aktif')->count();

        // Kehadiran hari ini seluruh sekolah
        $kehadiranHariIni = Kehadiran::whereDate('tanggal', $today)->get();
        $hadirHariIni = $kehadiranHariIni->whereIn('status', ['hadir', 'terlambat'])->count();
        $izinSakitHariIni = $kehadiranHariIni->whereIn('status', ['izin', 'sakit'])->count();
        $terlambatHariIni = $kehadiranHariIni->where('status', 'terlambat')->count();
        $belumAbsen = $totalSiswa - $kehadiranHariIni->count();

        // Rekap per kelas
        $kelasAll = Kelas::where('status', 'aktif')->with('guru')->get();
        $rekapKelas = [];

        foreach ($kelasAll as $kelas) {
            $siswaCount = Siswa::where('kelas_id', $kelas->id)->count();
            $siswaIds = Siswa::where('kelas_id', $kelas->id)->pluck('id');

            $kehadiranKelas = Kehadiran::whereIn('siswa_id', $siswaIds)
                ->whereDate('tanggal', $today)
                ->get();

            $rekapKelas[] = [
                'kelas'       => $kelas,
                'wali_kelas'  => $kelas->guru ? $kelas->guru->nama : '-',
                'total_siswa' => $siswaCount,
                'hadir'       => $kehadiranKelas->whereIn('status', ['hadir', 'terlambat'])->count(),
                'terlambat'   => $kehadiranKelas->where('status', 'terlambat')->count(),
                'izin'        => $kehadiranKelas->where('status', 'izin')->count(),
                'sakit'       => $kehadiranKelas->where('status', 'sakit')->count(),
                'alpha'       => $siswaCount - $kehadiranKelas->count(),
            ];
        }

        return view('pages.absensi.kesiswaan-dashboard', compact(
            'user', 'totalSiswa', 'hadirHariIni', 'izinSakitHariIni', 'terlambatHariIni', 'belumAbsen', 'rekapKelas'
        ));
    }
}
