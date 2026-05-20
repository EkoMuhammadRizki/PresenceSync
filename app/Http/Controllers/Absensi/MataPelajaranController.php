<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $mataPelajarans = MataPelajaran::with('guru')->latest()->get();
        $gurus          = Guru::orderBy('nama')->get();
        return view('pages.absensi.mata-pelajaran', compact('mataPelajarans', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:100',
            'kode'          => 'required|string|max:20|unique:mata_pelajarans,kode',
            'guru_id'       => 'nullable|exists:gurus,id',
            'jam_per_minggu'=> 'required|integer|min:1|max:40',
        ], [
            'nama.required'          => 'Nama mata pelajaran wajib diisi.',
            'kode.required'          => 'Kode mata pelajaran wajib diisi.',
            'kode.unique'            => 'Kode mata pelajaran sudah digunakan.',
            'jam_per_minggu.required'=> 'Jumlah jam per minggu wajib diisi.',
            'jam_per_minggu.min'     => 'Jumlah jam minimal 1.',
        ]);

        MataPelajaran::create($request->only('nama', 'kode', 'guru_id', 'jam_per_minggu'));

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'nama'          => 'required|string|max:100',
            'kode'          => 'required|string|max:20|unique:mata_pelajarans,kode,' . $mataPelajaran->id,
            'guru_id'       => 'nullable|exists:gurus,id',
            'jam_per_minggu'=> 'required|integer|min:1|max:40',
        ], [
            'kode.unique' => 'Kode mata pelajaran sudah digunakan.',
        ]);

        $mataPelajaran->update($request->only('nama', 'kode', 'guru_id', 'jam_per_minggu'));

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        if ($mataPelajaran->jadwalPelajarans()->exists()) {
            return redirect()->route('mata-pelajaran.index')
                ->with('error', 'Mata pelajaran tidak dapat dihapus karena masih digunakan dalam jadwal pelajaran.');
        }

        $mataPelajaran->delete();
        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
