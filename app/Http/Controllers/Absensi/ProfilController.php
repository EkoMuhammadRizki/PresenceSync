<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    /**
     * Halaman Profil Siswa (read).
     */
    public function showSiswa(Request $request)
    {
        $id = $request->id;
        if (!$id) {
            $siswa = Siswa::with(['kelas.jurusan', 'kelas.guru', 'user'])->where('user_id', auth()->id())->first();
        } else {
            $siswa = Siswa::with(['kelas.jurusan', 'kelas.guru', 'user'])->find($id);
        }

        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $kelas = Kelas::where('status', 'aktif')->orderBy('tingkat')->get();

        // Determine current user role
        $currentUser = auth()->user();
        $userRole = $this->getUserRole($currentUser);

        return view('pages.absensi.profil-siswa', compact('siswa', 'kelas', 'userRole'));
    }

    /**
     * Update Profil Siswa – role-based field restrictions.
     *
     * - Siswa: kelas_id, nama_orang_tua, no_hp, no_hp_orang_tua, alamat
     * - Guru / Kesiswaan: semua field
     */
    public function updateSiswa(Request $request, Siswa $siswa)
    {
        $currentUser = auth()->user();
        $userRole = $this->getUserRole($currentUser);

        // Validasi semua field terlebih dahulu
        $rules = [
            'kelas_id'        => 'nullable|exists:kelas,id',
            'nama_orang_tua'  => 'nullable|string|max:150',
            'no_hp'           => 'nullable|string|max:20',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'alamat'          => 'nullable|string',
        ];

        $messages = [
            'kelas_id.exists' => 'Kelas tidak valid.',
        ];

        // Guru & Kesiswaan bisa update semua field
        if ($userRole === 'guru' || $userRole === 'kesiswaan' || $userRole === 'admin') {
            $rules = array_merge($rules, [
                'nama'           => 'required|string|max:150',
                'nisn'           => 'nullable|string|max:20|unique:siswas,nisn,' . $siswa->id,
                'nis'            => 'nullable|string|max:20|unique:siswas,nis,' . $siswa->id,
                'jenis_kelamin'  => 'required|in:L,P',
                'tempat_lahir'   => 'nullable|string|max:100',
                'tanggal_lahir'  => 'nullable|date',
                'status'         => 'nullable|string|max:20',
                'fingerprint_id' => 'nullable|string|max:50|unique:siswas,fingerprint_id,' . $siswa->id,
            ]);

            $messages = array_merge($messages, [
                'nama.required'          => 'Nama siswa wajib diisi.',
                'nisn.unique'            => 'NISN sudah terdaftar.',
                'nis.unique'             => 'NIS sudah terdaftar.',
                'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
                'fingerprint_id.unique'  => 'ID fingerprint sudah terdaftar pada siswa lain.',
            ]);
        }

        $request->validate($rules, $messages);

        // Siswa: hanya field terbatas
        if ($userRole === 'siswa' || $userRole === 'orang_tua') {
            $siswa->update($request->only(
                'kelas_id', 'nama_orang_tua', 'no_hp', 'no_hp_orang_tua', 'alamat'
            ));
        } else {
            // Guru / Kesiswaan / Admin: semua field
            $siswa->update($request->only(
                'nama', 'nisn', 'nis', 'kelas_id', 'jenis_kelamin', 'tempat_lahir',
                'tanggal_lahir', 'alamat', 'no_hp', 'no_hp_orang_tua', 'nama_orang_tua',
                'status', 'fingerprint_id'
            ));

            // Update nama user terkait
            if ($siswa->user && $request->has('nama')) {
                $nameParts = explode(' ', trim($request->nama), 2);
                $firstName = $nameParts[0];
                $lastName  = $nameParts[1] ?? $nameParts[0];
                $siswa->user->update([
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Profil siswa berhasil diperbarui.');
    }

    /**
     * Halaman Profil Guru (read).
     */
    public function showGuru(Request $request)
    {
        $id = $request->id;
        if (!$id) {
            $guru = Guru::withCount(['kelas', 'mataPelajarans'])->where('user_id', auth()->id())->first();
        } else {
            $guru = Guru::withCount(['kelas', 'mataPelajarans'])->find($id);
        }

        if (!$guru) {
            abort(404, 'Data guru tidak ditemukan.');
        }

        $currentUser = auth()->user();
        $userRole = $this->getUserRole($currentUser);

        return view('pages.absensi.profil-guru', compact('guru', 'userRole'));
    }

    /**
     * Update Profil Guru – Guru & Kesiswaan bisa update semua.
     */
    public function updateGuru(Request $request, Guru $guru)
    {
        $currentUser = auth()->user();
        $userRole = $this->getUserRole($currentUser);

        // Siswa / Orang Tua tidak boleh update profil guru
        if ($userRole === 'siswa' || $userRole === 'orang_tua') {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah profil guru.');
        }

        $request->validate([
            'nama'   => 'required|string|max:150',
            'nip'    => 'nullable|string|max:30|unique:gurus,nip,' . $guru->id,
            'email'  => 'nullable|email|max:150|unique:gurus,email,' . $guru->id . '|unique:users,email,' . ($guru->user_id ?? 0),
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'nip.unique'    => 'NIP sudah terdaftar.',
            'email.unique'  => 'Email sudah terdaftar.',
            'email.email'   => 'Format email tidak valid.',
        ]);

        $guru->update($request->only('nama', 'nip', 'email', 'no_hp', 'alamat'));

        // Update User terkait
        if ($guru->user) {
            $nameParts = explode(' ', trim($request->nama), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? $nameParts[0];

            $userData = [
                'first_name' => $firstName,
                'last_name'  => $lastName,
            ];

            if ($request->email) {
                $userData['email'] = $request->email;
            }

            $guru->user->update($userData);
        }

        return redirect()->back()->with('success', 'Profil guru berhasil diperbarui.');
    }

    /**
     * Detect user role based on relationships and Spatie roles.
     */
    private function getUserRole($user): string
    {
        if (!$user) {
            return 'guest';
        }

        // Check Spatie roles first
        if ($user->hasRole('admin')) {
            return 'admin';
        }

        // Check by data relationships
        $isSiswa = Siswa::where('user_id', $user->id)->exists();
        if ($isSiswa) {
            return 'siswa';
        }

        $isGuru = Guru::where('user_id', $user->id)->exists();
        if ($isGuru) {
            // Check if guru has kesiswaan role (we use the Spatie role check)
            if ($user->hasRole('kesiswaan')) {
                return 'kesiswaan';
            }
            return 'guru';
        }

        // Default fallback – admin users that are not siswa/guru are admin/kesiswaan
        // For now, treat as admin if they have an account and reach here
        return 'admin';
    }
}
