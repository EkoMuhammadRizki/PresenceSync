<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::withCount('kelas')->latest()->get();
        return view('pages.absensi.jurusan', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:20|unique:jurusans,kode',
            'deskripsi' => 'nullable|string',
        ], [
            'nama.required' => 'Nama jurusan wajib diisi.',
            'kode.required' => 'Kode jurusan wajib diisi.',
            'kode.unique'   => 'Kode jurusan sudah digunakan.',
        ]);

        Jurusan::create($request->only('nama', 'kode', 'deskripsi'));

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:20|unique:jurusans,kode,' . $jurusan->id,
            'deskripsi' => 'nullable|string',
        ], [
            'kode.unique' => 'Kode jurusan sudah digunakan.',
        ]);

        $jurusan->update($request->only('nama', 'kode', 'deskripsi'));

        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan)
    {
        if ($jurusan->kelas()->exists()) {
            return redirect()->route('jurusan.index')
                ->with('error', 'Jurusan tidak dapat dihapus karena masih memiliki data kelas.');
        }

        $jurusan->delete();
        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}
