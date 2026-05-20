<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas    = Kelas::with(['jurusan', 'guru'])->withCount('siswas')->latest()->get();
        $jurusans = Jurusan::orderBy('nama')->get();
        $gurus    = Guru::orderBy('nama')->get();

        return view('pages.absensi.kelas-data', compact('kelas', 'jurusans', 'gurus'));
    }

    public function store(Request $request)
    {
        // Validasi: guru harus sudah ada di database
        if (Guru::count() === 0) {
            return redirect()->route('kelas.index')
                ->with('error', 'Belum ada data guru. Tambahkan data guru terlebih dahulu sebelum membuat kelas.');
        }

        $request->validate([
            'jurusan_id' => 'required|exists:jurusans,id',
            'guru_id'    => 'nullable|exists:gurus,id',
            'nama'       => 'required|string|max:50',
            'tingkat'    => 'required|in:10,11,12',
            'status'     => 'required|in:aktif,nonaktif',
        ], [
            'jurusan_id.required' => 'Jurusan wajib dipilih.',
            'jurusan_id.exists'   => 'Jurusan tidak valid.',
            'nama.required'       => 'Nama kelas wajib diisi.',
            'tingkat.required'    => 'Tingkat kelas wajib dipilih.',
            'tingkat.in'          => 'Tingkat kelas harus 10, 11, atau 12.',
        ]);

        Kelas::create($request->only('jurusan_id', 'guru_id', 'nama', 'tingkat', 'status'));

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'jurusan_id' => 'required|exists:jurusans,id',
            'guru_id'    => 'nullable|exists:gurus,id',
            'nama'       => 'required|string|max:50',
            'tingkat'    => 'required|in:10,11,12',
            'status'     => 'required|in:aktif,nonaktif',
        ]);

        $kelas->update($request->only('jurusan_id', 'guru_id', 'nama', 'tingkat', 'status'));

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        if ($kelas->siswas()->exists()) {
            return redirect()->route('kelas.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki data siswa.');
        }

        $kelas->delete();
        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
