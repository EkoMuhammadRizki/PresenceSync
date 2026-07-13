<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class RestriksiKelasController extends Controller
{
    /**
     * Display the restriction settings page.
     */
    public function index()
    {
        // Enforce admin permission check
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        // Fetch settings
        $restriksiKelas = Setting::where('key', 'restriksi_kelas')->value('value') ?? 'off';

        return view('pages.absensi.pengaturan-restriksi-kelas', compact('restriksiKelas'));
    }

    /**
     * Update the restriction settings.
     */
    public function update(Request $request)
    {
        // Enforce admin permission check
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        // The request value will be 'on' if the checkbox is checked, otherwise it won't be sent or we default it to 'off'
        $restriksiKelasVal = $request->has('restriksi_kelas') ? 'on' : 'off';

        Setting::updateOrCreate(
            ['key' => 'restriksi_kelas'],
            ['value' => $restriksiKelasVal]
        );

        return redirect()->back()->with('success', 'Pengaturan restriksi kelas berhasil diperbarui.');
    }
}
