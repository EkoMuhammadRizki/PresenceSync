<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Carbon;

class GuruDashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Guru (Wali Kelas).
     */
    public function index()
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->first();

        if (!$guru) {
            abort(403, 'Anda tidak terdaftar sebagai guru.');
        }

        $today = Carbon::today()->toDateString();

        // Kelas yang diampu (wali kelas)
        $kelasWali = Kelas::where('guru_id', $guru->id)->where('status', 'aktif')->get();

        // Statistik hari ini untuk semua kelas wali
        $kelasIds = $kelasWali->pluck('id');
        $siswaIds = Siswa::whereIn('kelas_id', $kelasIds)->pluck('id');
        $totalSiswa = $siswaIds->count();

        $kehadiranHariIni = Kehadiran::whereIn('siswa_id', $siswaIds)
            ->whereDate('tanggal', $today)
            ->get();

        $hadirHariIni = $kehadiranHariIni->whereIn('status', ['hadir', 'terlambat'])->count();
        $izinSakitHariIni = $kehadiranHariIni->whereIn('status', ['izin', 'sakit'])->count();
        $alphaHariIni = $totalSiswa - $hadirHariIni - $izinSakitHariIni;

        // Detail kehadiran per kelas
        $kelasDetail = [];
        foreach ($kelasWali as $kelas) {
            $siswaDiKelas = Siswa::where('kelas_id', $kelas->id)->get();
            $siswaIdKelas = $siswaDiKelas->pluck('id');

            $kehadiranKelas = Kehadiran::with('siswa')
                ->whereIn('siswa_id', $siswaIdKelas)
                ->whereDate('tanggal', $today)
                ->get()
                ->keyBy('siswa_id');

            $dataSiswa = [];
            foreach ($siswaDiKelas as $siswa) {
                $kh = $kehadiranKelas->get($siswa->id);
                $dataSiswa[] = [
                    'siswa'  => $siswa,
                    'status' => $kh ? $kh->status : 'belum_absen',
                    'jam_masuk' => $kh ? $kh->jam_masuk : null,
                    'keterangan' => $kh ? $kh->keterangan : null,
                ];
            }

            $kelasDetail[] = [
                'kelas' => $kelas,
                'siswa' => $dataSiswa,
                'total' => count($siswaDiKelas),
                'hadir' => collect($dataSiswa)->whereIn('status', ['hadir', 'terlambat'])->count(),
            ];
        }

        return view('pages.absensi.guru-dashboard', compact(
            'guru', 'totalSiswa', 'hadirHariIni', 'izinSakitHariIni', 'alphaHariIni', 'kelasDetail'
        ));
    }
}
