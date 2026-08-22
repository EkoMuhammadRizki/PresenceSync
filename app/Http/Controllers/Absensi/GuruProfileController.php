<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GuruProfileController extends Controller
{
    /**
     * Show Guru profile dashboard.
     */
    public function show(Request $request)
    {
        $id = $request->id;
        if (!$id) {
            $guru = Guru::where('user_id', auth()->id())->first();
        } else {
            $guru = Guru::find($id);
        }

        if (!$guru) {
            abort(404, 'Data guru tidak ditemukan.');
        }

        $user = $guru->user;
        $info = $user ? $user->info : null;

        // Statistics
        $stats = [
            'total_kelas' => Kelas::where('guru_id', $guru->id)->count(),
            'total_mapel' => MataPelajaran::where('guru_id', $guru->id)->count(),
            'weekly_hours' => 0,
        ];

        // Wali Kelas Class Data
        $kelasDiwali = Kelas::withCount('siswas')->where('guru_id', $guru->id)->get();

        // Semesters & Tahun Ajarans for filter dropdown
        $semesters = Semester::with('tahunAjaran')->get();
        $tahunAjarans = TahunAjaran::get();

        // Teaching Schedules with dynamic filtering
        $scheduleQuery = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'semester.tahunAjaran'])
            ->whereHas('mataPelajaran', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            });

        if ($request->filled('semester_id')) {
            $scheduleQuery->where('semester_id', $request->semester_id);
        }
        $schedules = $scheduleQuery->get();

        // Role mapping
        $currentUser = auth()->user();
        $userRole = $this->getUserRole($currentUser);

        $completionRate = $this->calculateCompletionRate($guru, $user);

        return view('profile.guru', compact(
            'guru', 'user', 'info', 'stats', 'kelasDiwali', 
            'semesters', 'tahunAjarans', 'schedules', 'userRole', 'completionRate'
        ));
    }

    /**
     * Update Guru profile.
     */
    public function update(Request $request, Guru $guru)
    {
        $currentUser = auth()->user();
        $userRole = $this->getUserRole($currentUser);

        // Siswa / Orang Tua cannot edit guru profile
        if ($userRole === 'siswa' || $userRole === 'orang_tua') {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah profil guru.');
        }

        // Restrict own profile editing for guru/kesiswaan (only nama, no_hp, alamat)
        $isOwnProfile = ($guru->user_id === auth()->id()) && ($userRole === 'guru' || $userRole === 'kesiswaan');

        // Normalize phone number: strip non-digit characters
        if ($request->filled('no_hp')) {
            $request->merge(['no_hp' => preg_replace('/[^0-9]/', '', $request->no_hp)]);
        } elseif ($request->has('no_hp')) {
            $request->merge(['no_hp' => null]);
        }

        if ($isOwnProfile) {
            $rules = [
                'nama'   => 'required|string|max:150',
                'no_hp'  => 'nullable|regex:/^[0-9]{8,15}$/',
                'alamat' => 'nullable|string',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
            ];
        } else {
            $rules = [
                'nama'   => 'required|string|max:150',
                'nip'    => 'nullable|string|max:30|unique:gurus,nip,' . $guru->id,
                'email'  => 'nullable|email|max:150|unique:gurus,email,' . $guru->id . '|unique:users,email,' . ($guru->user_id ?? 0),
                'no_hp'  => 'nullable|regex:/^[0-9]{8,15}$/',
                'alamat' => 'nullable|string',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
            ];
        }

        $messages = [
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka (8-15 digit).',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($isOwnProfile) {
            $guru->update($request->only('nama', 'no_hp', 'alamat'));
        } else {
            $guru->update($request->only('nama', 'nip', 'email', 'no_hp', 'alamat'));
        }

        // Update User model
        if ($guru->user) {
            $nameParts = explode(' ', trim($request->nama), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? $nameParts[0];

            $userData = [
                'first_name' => $firstName,
                'last_name'  => $lastName,
            ];

            if (!$isOwnProfile && $request->email) {
                $userData['email'] = $request->email;
            }

            $guru->user->update($userData);

            // Update UserInfo details (avatar)
            $info = $guru->user->info ?? new UserInfo(['user_id' => $guru->user_id]);
            $info->user()->associate($guru->user);
            $info->phone = $request->no_hp;

            if ($request->hasFile('avatar')) {
                $oldAvatar = $info->avatar;
                $identifier = $guru->nip ?: 'guru_' . $guru->id;
                $info->avatar = \App\Services\ImageCompressionService::compressAndSaveNamedAvatar(
                    $request->file('avatar'),
                    'guru',
                    $identifier,
                    $guru->nama,
                    $oldAvatar
                );
            }

            if ($request->boolean('avatar_remove')) {
                if ($info->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($info->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($info->avatar);
                }
                $info->avatar = null;
            }

            $info->save();
        }

        return redirect()->back()->with('success', 'Profil guru berhasil diperbarui.');
    }

    /**
     * Change Guru account password.
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Detect user role.
     */
    private function getUserRole($user): string
    {
        if (!$user) return 'guest';
        if ($user->hasRole('admin')) return 'admin';
        
        $isSiswa = \App\Models\Siswa::where('user_id', $user->id)->exists();
        if ($isSiswa) return 'siswa';

        $isGuru = Guru::where('user_id', $user->id)->exists();
        if ($isGuru) {
            if ($user->hasRole('kesiswaan')) {
                return 'kesiswaan';
            }
            return 'guru';
        }

        return 'admin';
    }

    /**
     * Calculate profile completion rate.
     */
    private function calculateCompletionRate($guru, $user)
    {
        $fields = [
            $guru->nama,
            $guru->nip,
            $guru->email,
            $guru->no_hp,
            $guru->alamat,
            $user->info->avatar ?? null,
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($field)) {
                $filled++;
            }
        }

        return round(($filled / count($fields)) * 100);
    }
}
