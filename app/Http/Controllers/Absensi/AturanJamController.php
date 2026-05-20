<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\AturanJam;
use Illuminate\Http\Request;

class AturanJamController extends Controller
{
    public function index()
    {
        $aturanJams = AturanJam::latest()->get();
        return view('pages.absensi.aturan-jam', compact('aturanJams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'                   => 'required|string|max:100',
            'jam_masuk'              => 'required',
            'toleransi_keterlambatan'=> 'required|integer|min:0',
            'jam_pulang'             => 'required',
            'is_aktif'               => 'required|in:0,1',
        ], [
            'nama.required'                   => 'Nama aturan jam wajib diisi.',
            'jam_masuk.required'              => 'Jam masuk wajib diisi.',
            'toleransi_keterlambatan.required' => 'Toleransi keterlambatan wajib diisi.',
            'jam_pulang.required'             => 'Jam pulang wajib diisi.',
        ]);

        $isAktif = (bool) $request->is_aktif;

        if ($isAktif) {
            AturanJam::where('is_aktif', true)->update(['is_aktif' => false]);
        }

        AturanJam::create([
            'nama'                    => $request->nama,
            'jam_masuk'               => $request->jam_masuk,
            'toleransi_keterlambatan' => $request->toleransi_keterlambatan,
            'jam_pulang'              => $request->jam_pulang,
            'is_aktif'                => $isAktif,
        ]);

        return redirect()->route('aturan-jam.index')
            ->with('success', 'Aturan jam berhasil ditambahkan.');
    }

    public function update(Request $request, AturanJam $aturanJam)
    {
        $request->validate([
            'nama'                   => 'required|string|max:100',
            'jam_masuk'              => 'required',
            'toleransi_keterlambatan'=> 'required|integer|min:0',
            'jam_pulang'             => 'required',
            'is_aktif'               => 'required|in:0,1',
        ]);

        $isAktif = (bool) $request->is_aktif;

        if ($isAktif) {
            AturanJam::where('id', '!=', $aturanJam->id)
                ->where('is_aktif', true)
                ->update(['is_aktif' => false]);
        }

        $aturanJam->update([
            'nama'                    => $request->nama,
            'jam_masuk'               => $request->jam_masuk,
            'toleransi_keterlambatan' => $request->toleransi_keterlambatan,
            'jam_pulang'              => $request->jam_pulang,
            'is_aktif'                => $isAktif,
        ]);

        return redirect()->route('aturan-jam.index')
            ->with('success', 'Aturan jam berhasil diperbarui.');
    }

    public function destroy(AturanJam $aturanJam)
    {
        if ($aturanJam->kehadirans()->exists()) {
            return redirect()->route('aturan-jam.index')
                ->with('error', 'Aturan jam tidak dapat dihapus karena sudah digunakan dalam data kehadiran.');
        }

        $aturanJam->delete();
        return redirect()->route('aturan-jam.index')
            ->with('success', 'Aturan jam berhasil dihapus.');
    }
}
