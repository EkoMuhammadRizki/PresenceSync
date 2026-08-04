<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\Kehadiran;
use Illuminate\Support\Facades\DB;

class ArsipController extends Controller
{
    /**
     * Tampilkan daftar semester yang sudah selesai (arsip).
     */
    public function index()
    {
        // Ambil semua semester beserta tahun ajaran yang statusnya selesai
        $arsipSemesters = Semester::with('tahunAjaran')
            ->where('status', 'selesai')
            ->orderByDesc('tanggal_selesai')
            ->get()
            ->map(function ($semester) {
                // Hitung jumlah siswa yang memiliki kehadiran di semester ini
                $semester->jumlah_siswa = Kehadiran::where('semester_id', $semester->id)
                    ->distinct('siswa_id')
                    ->count('siswa_id');

                // Total hari kehadiran unik di semester ini
                $semester->total_hari = Kehadiran::where('semester_id', $semester->id)
                    ->distinct('tanggal')
                    ->count('tanggal');

                return $semester;
            });

        return view('pages.absensi.arsip', compact('arsipSemesters'));
    }

    /**
     * Tampilkan profiling kehadiran siswa untuk satu semester arsip.
     */
    public function profiling(Semester $semester)
    {
        // Pastikan semester ini sudah selesai
        if ($semester->status !== 'selesai') {
            return redirect()->route('arsip.index')
                ->with('error', 'Semester ini belum selesai dan tidak dapat diarsipkan.');
        }

        $semester->load('tahunAjaran');

        // Total hari unik di semester ini
        $totalHari = Kehadiran::where('semester_id', $semester->id)
            ->distinct('tanggal')
            ->count('tanggal');

        // Ambil semua siswa yang pernah hadir di semester ini
        $siswaIds = Kehadiran::where('semester_id', $semester->id)
            ->distinct('siswa_id')
            ->pluck('siswa_id');

        // Agregasi kehadiran per siswa
        $kehadiranPerSiswa = Kehadiran::where('semester_id', $semester->id)
            ->select(
                'siswa_id',
                DB::raw("COUNT(CASE WHEN status = 'hadir' THEN 1 END) as total_hadir"),
                DB::raw("COUNT(CASE WHEN status = 'terlambat' THEN 1 END) as total_terlambat"),
                DB::raw("COUNT(CASE WHEN status = 'sakit' THEN 1 END) as total_sakit"),
                DB::raw("COUNT(CASE WHEN status = 'izin' THEN 1 END) as total_izin"),
                DB::raw("COUNT(CASE WHEN status = 'alpha' THEN 1 END) as total_alpha"),
                DB::raw("COUNT(*) as total_kehadiran")
            )
            ->groupBy('siswa_id')
            ->get()
            ->keyBy('siswa_id');

        // Ambil data siswa (dengan kelas)
        $siswas = Siswa::with('kelas')
            ->whereIn('id', $siswaIds)
            ->orderBy('nama')
            ->get()
            ->map(function ($siswa) use ($kehadiranPerSiswa, $totalHari) {
                $stat = $kehadiranPerSiswa->get($siswa->id);
                $siswa->total_hadir     = $stat ? $stat->total_hadir : 0;
                $siswa->total_terlambat = $stat ? $stat->total_terlambat : 0;
                $siswa->total_sakit     = $stat ? $stat->total_sakit : 0;
                $siswa->total_izin      = $stat ? $stat->total_izin : 0;
                $siswa->total_alpha     = $stat ? $stat->total_alpha : 0;
                $hadirEfektif = $siswa->total_hadir + $siswa->total_terlambat;
                $siswa->persen_hadir    = $totalHari > 0
                    ? round(($hadirEfektif / $totalHari) * 100, 1)
                    : 0;
                return $siswa;
            });

        // Summary stats keseluruhan
        $summary = [
            'total_siswa'       => $siswas->count(),
            'total_hari'        => $totalHari,
            'rata_hadir'        => $siswas->count() > 0
                ? round($siswas->avg('persen_hadir'), 1)
                : 0,
            'total_hadir'       => $siswas->sum('total_hadir'),
            'total_terlambat'   => $siswas->sum('total_terlambat'),
            'total_sakit'       => $siswas->sum('total_sakit'),
            'total_izin'        => $siswas->sum('total_izin'),
            'total_alpha'       => $siswas->sum('total_alpha'),
        ];

        return view('pages.absensi.arsip-profiling', compact('semester', 'siswas', 'summary', 'totalHari'));
    }
}
