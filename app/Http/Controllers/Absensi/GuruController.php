<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::withCount(['kelas', 'mataPelajarans'])->latest()->get();
        return view('pages.absensi.guru', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:150',
            'nip'    => 'nullable|string|max:30|unique:gurus,nip',
            'email'  => 'nullable|email|max:150|unique:gurus,email',
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'nip.unique'    => 'NIP sudah terdaftar.',
            'email.unique'  => 'Email sudah terdaftar.',
            'email.email'   => 'Format email tidak valid.',
        ]);

        Guru::create($request->only('nama', 'nip', 'email', 'no_hp', 'alamat'));

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama'   => 'required|string|max:150',
            'nip'    => 'nullable|string|max:30|unique:gurus,nip,' . $guru->id,
            'email'  => 'nullable|email|max:150|unique:gurus,email,' . $guru->id,
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'nip.unique'    => 'NIP sudah terdaftar.',
            'email.unique'  => 'Email sudah terdaftar.',
        ]);

        $guru->update($request->only('nama', 'nip', 'email', 'no_hp', 'alamat'));

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        if ($guru->kelas()->exists() || $guru->mataPelajarans()->exists()) {
            return redirect()->route('guru.index')
                ->with('error', 'Guru tidak dapat dihapus karena masih memiliki data kelas atau mata pelajaran.');
        }

        $guru->delete();
        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil dihapus.');
    }
}
