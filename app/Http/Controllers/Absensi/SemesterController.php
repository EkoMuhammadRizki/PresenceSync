<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'jenis'           => 'required|in:ganjil,genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status'          => 'required|in:aktif,selesai',
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.exists'   => 'Tahun ajaran tidak valid.',
            'jenis.required'           => 'Jenis semester wajib dipilih.',
            'tanggal_mulai.required'   => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.after'    => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        // Cek duplikat (1 TA hanya boleh 1 semester ganjil + 1 genap)
        $exists = Semester::where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->where('jenis', $request->jenis)
            ->exists();

        if ($exists) {
            return redirect()->route('tahun-ajaran.index')
                ->with('error', 'Semester ' . $request->jenis . ' untuk tahun ajaran ini sudah ada.');
        }

        // Jika set aktif, nonaktifkan semester aktif lain
        if ($request->status === 'aktif') {
            Semester::where('status', 'aktif')->update(['status' => 'selesai']);
        }

        Semester::create($request->only(
            'tahun_ajaran_id', 'jenis', 'tanggal_mulai', 'tanggal_selesai', 'status'
        ));

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Semester berhasil ditambahkan.');
    }

    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'jenis'           => 'required|in:ganjil,genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status'          => 'required|in:aktif,selesai',
        ]);

        // Cek duplikat (kecuali diri sendiri)
        $exists = Semester::where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->where('jenis', $request->jenis)
            ->where('id', '!=', $semester->id)
            ->exists();

        if ($exists) {
            return redirect()->route('tahun-ajaran.index')
                ->with('error', 'Semester ' . $request->jenis . ' untuk tahun ajaran ini sudah ada.');
        }

        if ($request->status === 'aktif') {
            Semester::where('id', '!=', $semester->id)
                ->where('status', 'aktif')
                ->update(['status' => 'selesai']);
        }

        $semester->update($request->only(
            'tahun_ajaran_id', 'jenis', 'tanggal_mulai', 'tanggal_selesai', 'status'
        ));

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester)
    {
        if ($semester->kehadirans()->exists() || $semester->jadwalPelajarans()->exists()) {
            return redirect()->route('tahun-ajaran.index')
                ->with('error', 'Semester tidak dapat dihapus karena masih memiliki data jadwal atau kehadiran.');
        }

        $semester->delete();
        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Semester berhasil dihapus.');
    }
}
