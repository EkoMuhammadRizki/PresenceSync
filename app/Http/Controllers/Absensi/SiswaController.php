<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::with(['kelas.jurusan', 'user'])->latest()->get();
        $kelas  = Kelas::with('jurusan')->where('status', 'aktif')->orderBy('tingkat')->get();
        return view('pages.absensi.siswa', compact('siswas', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:150',
            'nisn'          => 'nullable|string|max:20|unique:siswas,nisn',
            'nis'           => 'nullable|string|max:20|unique:siswas,nis',
            'kelas_id'      => 'nullable|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string',
        ], [
            'nama.required'          => 'Nama siswa wajib diisi.',
            'nisn.unique'            => 'NISN sudah terdaftar.',
            'nis.unique'             => 'NIS sudah terdaftar.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        // Buat akun user otomatis untuk siswa
        $email    = Str::slug($request->nama, '.') . '@siswa.presencesync.sch.id';
        $password = $request->nisn ?? $request->nis ?? 'password123';

        $nameParts = explode(' ', trim($request->nama), 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? $nameParts[0];

        $user = User::create([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'password'   => Hash::make($password),
        ]);

        Siswa::create(array_merge(
            $request->only('nama', 'nisn', 'nis', 'kelas_id', 'jenis_kelamin', 'tanggal_lahir', 'alamat'),
            ['user_id' => $user->id]
        ));

        return redirect()->route('siswa.index')
            ->with('success', "Siswa berhasil ditambahkan. Akun login: {$email} | Password: {$password}");
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama'          => 'required|string|max:150',
            'nisn'          => 'nullable|string|max:20|unique:siswas,nisn,' . $siswa->id,
            'nis'           => 'nullable|string|max:20|unique:siswas,nis,' . $siswa->id,
            'kelas_id'      => 'nullable|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string',
            'fingerprint_id'=> 'nullable|string|max:50|unique:siswas,fingerprint_id,' . $siswa->id,
        ], [
            'nisn.unique'           => 'NISN sudah terdaftar.',
            'nis.unique'            => 'NIS sudah terdaftar.',
            'fingerprint_id.unique' => 'ID fingerprint sudah terdaftar pada siswa lain.',
        ]);

        $siswa->update($request->only(
            'nama', 'nisn', 'nis', 'kelas_id', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'fingerprint_id'
        ));

        // Update nama user terkait
        if ($siswa->user) {
            $nameParts = explode(' ', trim($request->nama), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? $nameParts[0];
            $siswa->user->update([
                'first_name' => $firstName,
                'last_name'  => $lastName,
            ]);
        }

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        // Hapus user terkait juga
        if ($siswa->user) {
            $siswa->user->delete();
        }

        $siswa->delete();
        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
