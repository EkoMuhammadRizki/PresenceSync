<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Redirect user to their respective profile page based on role.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('profil-admin.show');
        }

        if (Siswa::where('user_id', $user->id)->exists()) {
            return redirect()->route('profil-siswa.show');
        }

        if (Guru::where('user_id', $user->id)->exists()) {
            return redirect()->route('profil-guru.show');
        }

        // Fallback for admin or general accounts
        return redirect()->route('profil-admin.show');
    }
}
