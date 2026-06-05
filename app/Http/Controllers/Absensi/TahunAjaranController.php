<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjarans = TahunAjaran::withCount('semesters')->latest()->get();
        $semesters    = Semester::with('tahunAjaran')->latest()->get();
        $hasAktif     = TahunAjaran::where('status', 'aktif')->exists();
        return view('pages.absensi.tahun-ajaran', compact('tahunAjarans', 'semesters', 'hasAktif'));
    }

    public function store(Request $request)
    {
        // Cegah penambahan jika masih ada tahun ajaran yang aktif
        if (TahunAjaran::where('status', 'aktif')->exists()) {
            return redirect()->route('tahun-ajaran.index')
                ->with('error', 'Tidak dapat menambahkan tahun ajaran baru. Ubah status tahun ajaran yang aktif menjadi "Selesai" terlebih dahulu.');
        }

        $request->validate([
            'nama'          => 'required|string|max:20|unique:tahun_ajarans,nama',
            'bulan_mulai'   => 'required|date',
            'bulan_selesai' => 'required|date|after:bulan_mulai',
        ], [
            'nama.required'          => 'Nama tahun ajaran wajib diisi.',
            'nama.unique'            => 'Tahun ajaran sudah terdaftar.',
            'bulan_mulai.required'   => 'Bulan mulai wajib diisi.',
            'bulan_selesai.required' => 'Bulan selesai wajib diisi.',
            'bulan_selesai.after'    => 'Bulan selesai harus setelah bulan mulai.',
        ]);

        TahunAjaran::create($request->only('nama', 'bulan_mulai', 'bulan_selesai'));

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'nama'          => 'required|string|max:20|unique:tahun_ajarans,nama,' . $tahunAjaran->id,
            'bulan_mulai'   => 'required|date',
            'bulan_selesai' => 'required|date|after:bulan_mulai',
            'status'        => 'required|in:aktif,selesai',
        ], [
            'nama.unique'         => 'Tahun ajaran sudah terdaftar.',
            'bulan_selesai.after' => 'Bulan selesai harus setelah bulan mulai.',
        ]);

        // Jika set aktif, nonaktifkan tahun ajaran lain
        if ($request->status === 'aktif') {
            TahunAjaran::where('id', '!=', $tahunAjaran->id)
                ->where('status', 'aktif')
                ->update(['status' => 'selesai']);
        }

        $tahunAjaran->update($request->only('nama', 'bulan_mulai', 'bulan_selesai', 'status'));

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->semesters()->exists()) {
            return redirect()->route('tahun-ajaran.index')
                ->with('error', 'Tahun ajaran tidak dapat dihapus karena masih memiliki data semester.');
        }

        $tahunAjaran->delete();
        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
