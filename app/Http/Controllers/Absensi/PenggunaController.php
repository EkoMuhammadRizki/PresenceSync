<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PenggunaController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with(['siswa', 'guru', 'roles'])->orderBy('created_at', 'desc')->get();
        return view('pages.absensi.pengguna-data', compact('users'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,kesiswaan',
        ], [
            'username.required' => 'Username wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 6 karakter.',
            'role.required'     => 'Peran wajib dipilih.',
            'role.in'           => 'Peran tidak valid.',
        ]);

        $nameParts = explode('.', trim($request->username), 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? $nameParts[0];

        $user = User::create([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        // Assign Spatie role
        $role = Role::firstOrCreate(['name' => $request->role]);
        $user->assignRole($role);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log("Menambah data user baru: {$user->email}");

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $pengguna)
    {
        $rules = [
            'username' => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email,' . $pengguna->id,
            'password' => 'nullable|string|min:6',
        ];

        // Only allow changing roles if they are not Siswa or Guru
        if (!$pengguna->siswa && !$pengguna->guru) {
            $rules['role'] = 'required|in:admin,kesiswaan';
        }

        $request->validate($rules, [
            'username.required' => 'Username wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.min'      => 'Password minimal harus 6 karakter.',
            'role.required'     => 'Peran wajib dipilih.',
        ]);

        $username = trim($request->username);
        if ($pengguna->siswa || $pengguna->guru) {
            $usernameSpace = str_replace('.', ' ', $username);
            $nameParts = explode(' ', $usernameSpace, 2);
        } else {
            $nameParts = explode('.', $username, 2);
        }
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? $nameParts[0];

        $userData = [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $pengguna->update($userData);

        // Update corresponding Siswa or Guru details if they exist
        if ($pengguna->siswa) {
            $pengguna->siswa->update([
                'nama' => str_replace('.', ' ', $request->username),
                'status' => $request->status ?? 'aktif',
            ]);
        }

        if ($pengguna->guru) {
            $pengguna->guru->update([
                'nama' => str_replace('.', ' ', $request->username),
                'email' => $request->email,
            ]);
        }

        // Sync Spatie role if applicable
        if (!$pengguna->siswa && !$pengguna->guru && $request->has('role')) {
            $role = Role::firstOrCreate(['name' => $request->role]);
            $pengguna->syncRoles([$role]);
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($pengguna)
            ->log("Mengubah data user: {$pengguna->email}");

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return redirect()->route('pengguna.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userEmail = $pengguna->email;

        // Delete Guru if exists
        if ($pengguna->guru) {
            $pengguna->guru->delete();
        }

        // Delete Siswa if exists
        if ($pengguna->siswa) {
            $pengguna->siswa->delete();
        }

        $pengguna->delete();

        activity()
            ->causedBy(auth()->user())
            ->log("Menghapus data user: {$userEmail}");

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
