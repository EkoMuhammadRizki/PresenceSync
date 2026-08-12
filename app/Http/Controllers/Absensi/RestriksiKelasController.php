<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class RestriksiKelasController extends Controller
{
    /**
     * Helper to check admin access
     */
    private function checkAdminAccess()
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        $isAdmin = ($user->role === 'admin') 
            || ($user->role === 'superadmin')
            || (method_exists($user, 'hasRole') && $user->hasRole('admin'))
            || (!\App\Models\Siswa::where('user_id', $user->id)->exists() && !\App\Models\Guru::where('user_id', $user->id)->exists());

        if (!$isAdmin) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }
    }

    /**
     * Display the restriction settings page.
     */
    public function index()
    {
        $this->checkAdminAccess();

        // Fetch settings
        $restriksiKelas = Setting::where('key', 'restriksi_kelas')->value('value') ?? 'off';

        return view('pages.absensi.pengaturan-restriksi-kelas', compact('restriksiKelas'));
    }

    /**
     * Update the restriction settings.
     */
    public function update(Request $request)
    {
        $this->checkAdminAccess();

        $restriksiKelasVal = ($request->input('restriksi_kelas') === 'on' || $request->has('restriksi_kelas')) ? 'on' : 'off';

        Setting::updateOrCreate(
            ['key' => 'restriksi_kelas'],
            ['value' => $restriksiKelasVal]
        );

        $statusText = $restriksiKelasVal === 'on' ? 'AKTIF (Siswa Bisa Edit)' : 'NONAKTIF (Read-Only)';
        if (auth()->check()) {
            activity()
                ->causedBy(auth()->user())
                ->log("Mengubah pengaturan restriksi kelas siswa menjadi: {$statusText}");
        }

        return redirect()->back()->with('success', 'Pengaturan restriksi kelas berhasil diperbarui.');
    }
}
