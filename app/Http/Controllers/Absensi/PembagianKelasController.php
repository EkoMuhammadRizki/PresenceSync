<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PembagianKelasController extends Controller
{
    /**
     * Halaman Pembagian Kelas – tabel Nama Kelas & Jumlah Siswa.
     */
    public function index()
    {
        $kelas = Kelas::with(['jurusan', 'guru'])
            ->withCount('siswas')
            ->where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();

        return view('pages.absensi.pembagian-kelas', compact('kelas'));
    }

    /**
     * Detail Kelas – menampilkan daftar siswa di kelas tertentu.
     */
    public function show(Kelas $pembagian)
    {
        $kelas = $pembagian;
        $kelas->load(['jurusan', 'guru', 'siswas']);
        $kelas->loadCount('siswas');

        // Siswa yang belum punya kelas (untuk modal tambah siswa)
        $availableSiswas = Siswa::whereNull('kelas_id')
            ->orWhere('kelas_id', 0)
            ->orderBy('nama')
            ->get();

        return view('pages.absensi.pembagian-kelas-detail', compact('kelas', 'availableSiswas'));
    }

    /**
     * Tambah siswa ke kelas (multiple select).
     */
    public function addSiswa(Request $request, Kelas $kelas)
    {
        $request->validate([
            'siswa_ids'   => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswas,id',
        ], [
            'siswa_ids.required' => 'Pilih minimal satu siswa.',
            'siswa_ids.min'      => 'Pilih minimal satu siswa.',
        ]);

        Siswa::whereIn('id', $request->siswa_ids)
            ->update(['kelas_id' => $kelas->id]);

        return redirect()->route('pembagian-kelas.show', $kelas->id)
            ->with('success', count($request->siswa_ids) . ' siswa berhasil ditambahkan ke kelas ' . $kelas->nama . '.');
    }

    /**
     * Hapus siswa dari kelas (set kelas_id ke null).
     */
    public function removeSiswa(Request $request, Kelas $kelas, Siswa $siswa)
    {
        if ($siswa->kelas_id !== $kelas->id) {
            return redirect()->route('pembagian-kelas.show', $kelas->id)
                ->with('error', 'Siswa tidak terdaftar di kelas ini.');
        }

        $siswa->update(['kelas_id' => null]);

        return redirect()->route('pembagian-kelas.show', $kelas->id)
            ->with('success', 'Siswa ' . $siswa->nama . ' berhasil dikeluarkan dari kelas.');
    }
}
