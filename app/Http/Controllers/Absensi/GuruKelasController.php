<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GuruKelasController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->first();

        if (!$guru) {
            abort(403, 'Anda tidak terdaftar sebagai guru.');
        }

        $kelas = Kelas::with('siswas')
            ->where('guru_id', $guru->id)
            ->where('status', 'aktif')
            ->first();

        if (!$kelas) {
            return view('pages.absensi.guru-kelas-wali', [
                'guru' => $guru,
                'kelas' => null,
                'siswas' => collect(),
                'kehadirans' => collect(),
                'rekap' => [],
            ]);
        }

        $siswas = $kelas->siswas()->orderBy('nama')->get();

        $kehadirans = Kehadiran::with('siswa')
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'desc')
            ->paginate(50);

        $rekap = [];
        $totalHari = Kehadiran::whereIn('siswa_id', $siswas->pluck('id'))
            ->selectRaw('siswa_id, count(*) as total, sum(case when status in ("hadir","terlambat") then 1 else 0 end) as hadir')
            ->groupBy('siswa_id')
            ->get()
            ->keyBy('siswa_id');

        foreach ($siswas as $siswa) {
            $data = $totalHari->get($siswa->id);
            $hadirCount = $data->hadir ?? 0;
            $totalCount = $data->total ?? 0;
            $persentase = $totalCount > 0 ? round(($hadirCount / $totalCount) * 100) : 0;
            $rekap[$siswa->id] = [
                'total' => $totalCount,
                'hadir' => $hadirCount,
                'persentase' => $persentase,
            ];
        }

        return view('pages.absensi.guru-kelas-wali', compact(
            'guru', 'kelas', 'siswas', 'kehadirans', 'rekap'
        ));
    }
}
