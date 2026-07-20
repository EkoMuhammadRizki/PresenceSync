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

    public function show(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->load('guru', 'jadwalPelajarans.kelas');
        return view('pages.absensi.profil-mata-pelajaran', compact('mataPelajaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:100',
            'kode'          => 'required|string|max:20|unique:mata_pelajarans,kode',
            'guru_id'       => 'nullable|exists:gurus,id',
        ], [
            'nama.required'          => 'Nama mata pelajaran wajib diisi.',
            'kode.required'          => 'Kode mata pelajaran wajib diisi.',
            'kode.unique'            => 'Kode mata pelajaran sudah digunakan.',
        ]);

        MataPelajaran::create($request->only('nama', 'kode', 'guru_id'));

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'nama'          => 'required|string|max:100',
            'kode'          => 'required|string|max:20|unique:mata_pelajarans,kode,' . $mataPelajaran->id,
            'guru_id'       => 'nullable|exists:gurus,id',
        ], [
            'kode.unique' => 'Kode mata pelajaran sudah digunakan.',
        ]);

        $mataPelajaran->update($request->only('nama', 'kode', 'guru_id'));

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();
        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
