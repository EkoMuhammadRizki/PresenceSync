<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    public function index()
    {
        $jadwals        = JadwalPelajaran::with(['kelas.jurusan', 'mataPelajaran.guru', 'semester.tahunAjaran'])
                            ->latest()->get();
        $kelas          = Kelas::with('jurusan')->where('status', 'aktif')->orderBy('tingkat')->get();
        $mataPelajarans = MataPelajaran::with('guru')->orderBy('nama')->get();
        $semesters      = Semester::with('tahunAjaran')->where('status', 'aktif')->get();

        return view('pages.absensi.jadwal-pelajaran', compact('jadwals', 'kelas', 'mataPelajarans', 'semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'          => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'semester_id'       => 'required|exists:semesters,id',
            'hari'              => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai'         => 'required|date_format:H:i',
            'jam_selesai'       => 'required|date_format:H:i|after:jam_mulai',
        ], [
            'kelas_id.required'          => 'Kelas wajib dipilih.',
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'semester_id.required'       => 'Semester wajib dipilih.',
            'hari.required'              => 'Hari wajib dipilih.',
            'jam_mulai.required'         => 'Jam mulai wajib diisi.',
            'jam_selesai.after'          => 'Jam selesai harus setelah jam mulai.',
        ]);

        JadwalPelajaran::create($request->only(
            'kelas_id', 'mata_pelajaran_id', 'semester_id', 'hari', 'jam_mulai', 'jam_selesai'
        ));

        return redirect()->route('jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $request->validate([
            'kelas_id'          => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'semester_id'       => 'required|exists:semesters,id',
            'hari'              => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai'         => 'required|date_format:H:i',
            'jam_selesai'       => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwalPelajaran->update($request->only(
            'kelas_id', 'mata_pelajaran_id', 'semester_id', 'hari', 'jam_mulai', 'jam_selesai'
        ));

        return redirect()->route('jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->delete();
        return redirect()->route('jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
