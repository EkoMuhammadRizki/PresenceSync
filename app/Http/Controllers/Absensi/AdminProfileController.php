<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminProfileController extends Controller
{
    /**
     * Show Admin profile dashboard.
     */
    public function show()
    {
        $user = auth()->user();
        $info = $user->info;
        
        $stats = [
            'total_users' => User::count(),
            'total_gurus' => Guru::count(),
            'total_siswas' => Siswa::count(),
        ];

        $userRole = 'admin';

        // Calculate profile completion rate based on user details
        $completionRate = $this->calculateCompletionRate($user, $info);

        return view('profile.admin', compact('user', 'info', 'stats', 'userRole', 'completionRate'));
    }

    /**
     * Update Admin profile details.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $info = $user->info ?? new UserInfo(['user_id' => $user->id]);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update User Model
        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
        ]);

        // Save UserInfo details
        $info->user()->associate($user);
        $info->phone = $request->phone;
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $oldAvatar = $info->avatar;
            $identifier = 'admin_' . $user->id;
            $info->avatar = \App\Services\ImageCompressionService::compressAndSaveNamedAvatar(
                $request->file('avatar'),
                'admin',
                $identifier,
                $user->name,
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

        return redirect()->back()->with('success', 'Profil Admin berhasil diperbarui.');
    }

    /**
     * Change Admin password.
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
     * Calculate profile completion rate.
     */
    private function calculateCompletionRate($user, $info)
    {
        $fields = [
            $user->first_name,
            $user->last_name,
            $user->email,
            $info->avatar ?? null,
            $info->phone ?? null,
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
