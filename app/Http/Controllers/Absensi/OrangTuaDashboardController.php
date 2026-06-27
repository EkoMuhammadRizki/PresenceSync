<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Siswa;
use Illuminate\Support\Carbon;

class OrangTuaDashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Orang Tua.
     */
    public function index()
    {
        $user = auth()->user();

        // Cari anak-anak yang terhubung ke user orang tua ini
        $anakList = Siswa::where('orang_tua_user_id', $user->id)->with('kelas')->get();

        if ($anakList->isEmpty()) {
            abort(403, 'Tidak ada data siswa yang terhubung ke akun Anda.');
        }

        $today = Carbon::today()->toDateString();

        // Kumpulkan data kehadiran per anak
        $dataAnak = [];
        foreach ($anakList as $anak) {
            $kehadirans = Kehadiran::with('aturanJam')
                ->where('siswa_id', $anak->id)
                ->orderBy('tanggal', 'desc')
                ->get();

            $kehadiranHariIni = Kehadiran::where('siswa_id', $anak->id)
                ->whereDate('tanggal', $today)
                ->first();

            $dataAnak[] = [
                'siswa'            => $anak,
                'kehadirans'       => $kehadirans,
                'kehadiranHariIni' => $kehadiranHariIni,
                'totalHadir'       => $kehadirans->whereIn('status', ['hadir', 'terlambat'])->count(),
                'totalAbsen'       => $kehadirans->whereIn('status', ['sakit', 'izin', 'alpha'])->count(),
            ];
        }

        return view('pages.absensi.orangtua-dashboard', compact('user', 'dataAnak'));
    }
}
