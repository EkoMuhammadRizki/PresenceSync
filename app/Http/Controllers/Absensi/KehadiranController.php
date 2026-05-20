<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\AturanJam;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KehadiranController extends Controller
{
    public function index(Request $request)
    {
        $query = Kehadiran::with(['siswa.user', 'siswa.kelas.jurusan', 'semester']);

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        } else {
            // Default to today or latest dates
            $query->orderBy('tanggal', 'desc');
        }

        $kehadirans = $query->latest()->get();
        $kelas = Kelas::with('jurusan')->where('status', 'aktif')->orderBy('tingkat')->get();
        $siswas = Siswa::with('user')->orderBy('nama')->get();
        
        // Get active semester or latest
        $activeSemester = Semester::where('status', 'aktif')->first() ?? Semester::latest()->first();
        $semesters = Semester::with('tahunAjaran')->latest()->get();
        $aturanJams = AturanJam::aktif()->get();

        return view('pages.absensi.kehadiran', compact(
            'kehadirans', 
            'kelas', 
            'siswas', 
            'semesters', 
            'activeSemester',
            'aturanJams'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id'    => 'required|exists:siswas,id',
            'semester_id' => 'required|exists:semesters,id',
            'tanggal'     => 'required|date',
            'status'      => 'required|in:hadir,terlambat,sakit,izin,alpha',
            'jam_masuk'   => 'nullable',
            'jam_pulang'  => 'nullable',
            'keterangan'  => 'nullable|string',
        ], [
            'siswa_id.required'    => 'Siswa wajib dipilih.',
            'semester_id.required' => 'Semester wajib dipilih.',
            'tanggal.required'     => 'Tanggal wajib diisi.',
            'status.required'      => 'Status kehadiran wajib diisi.',
        ]);

        // Cek duplikasi
        $exists = Kehadiran::where('siswa_id', $request->siswa_id)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($exists) {
            return redirect()->route('kehadiran.index')
                ->with('error', 'Data kehadiran siswa ini pada tanggal tersebut sudah ada.');
        }

        // Dapatkan aturan jam aktif
        $aturanJam = AturanJam::where('is_aktif', true)->first();

        Kehadiran::create([
            'siswa_id'      => $request->siswa_id,
            'semester_id'   => $request->semester_id,
            'aturan_jam_id' => $aturanJam ? $aturanJam->id : null,
            'tanggal'       => $request->tanggal,
            'jam_masuk'     => $request->jam_masuk,
            'jam_pulang'    => $request->jam_pulang,
            'status'        => $request->status,
            'keterangan'    => $request->keterangan,
        ]);

        return redirect()->route('kehadiran.index')
            ->with('success', 'Data kehadiran berhasil ditambahkan.');
    }

    public function update(Request $request, Kehadiran $kehadiran)
    {
        $request->validate([
            'status'     => 'required|in:hadir,terlambat,sakit,izin,alpha',
            'jam_masuk'  => 'nullable',
            'jam_pulang' => 'nullable',
            'keterangan' => 'nullable|string',
        ]);

        $kehadiran->update($request->only('status', 'jam_masuk', 'jam_pulang', 'keterangan'));

        return redirect()->route('kehadiran.index')
            ->with('success', 'Data kehadiran berhasil diperbarui.');
    }

    public function destroy(Kehadiran $kehadiran)
    {
        $kehadiran->delete();
        return redirect()->route('kehadiran.index')
            ->with('success', 'Data kehadiran berhasil dihapus.');
    }
}
