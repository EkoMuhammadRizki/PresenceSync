<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas    = Kelas::with(['guru'])->withCount('siswas')->latest()->get();
        $gurus    = Guru::orderBy('nama')->get();

        return view('pages.absensi.kelas-data', compact('kelas', 'gurus'));
    }

    public function show(Kelas $kelas)
    {
        $kelas->load(['guru', 'siswas', 'jadwalPelajarans.kelas']);
        $kelas->loadCount('siswas');

        return view('pages.absensi.profil-kelas', compact('kelas'));
    }

    public function store(Request $request)
    {
        // Validasi: guru harus sudah ada di database
        if (Guru::count() === 0) {
            return redirect()->route('kelas.index')
                ->with('error', 'Belum ada data guru. Tambahkan data guru terlebih dahulu sebelum membuat kelas.');
        }

        $request->validate([
            'guru_id'    => 'nullable|exists:gurus,id',
            'nama'       => 'required|string|max:50',
            'tingkat'    => 'required|in:10,11,12',
            'status'     => 'required|in:aktif,nonaktif',
        ], [
            'nama.required'       => 'Nama kelas wajib diisi.',
            'tingkat.required'    => 'Tingkat kelas wajib dipilih.',
            'tingkat.in'          => 'Tingkat kelas harus 10, 11, atau 12.',
        ]);

        if ($request->guru_id) {
            $alreadyAssigned = Kelas::where('guru_id', $request->guru_id)->exists();
            if ($alreadyAssigned) {
                return redirect()->back()->withInput()->withErrors(['guru_id' => 'Guru tersebut sudah menjadi wali kelas di kelas lain.']);
            }
        }

        Kelas::create($request->only('guru_id', 'nama', 'tingkat', 'status'));

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'guru_id'    => 'nullable|exists:gurus,id',
            'nama'       => 'required|string|max:50',
            'tingkat'    => 'required|in:10,11,12',
            'status'     => 'required|in:aktif,nonaktif',
        ]);

        if ($request->guru_id) {
            $alreadyAssigned = Kelas::where('guru_id', $request->guru_id)
                ->where('id', '!=', $kelas->id)
                ->exists();
            if ($alreadyAssigned) {
                return redirect()->back()->withInput()->withErrors(['guru_id' => 'Guru tersebut sudah menjadi wali kelas di kelas lain.']);
            }
        }

        $kelas->update($request->only('guru_id', 'nama', 'tingkat', 'status'));

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        $relations = [];
        if ($kelas->siswas()->exists()) {
            $relations[] = 'data siswa';
        }
        if ($kelas->jadwalPelajarans()->exists()) {
            $relations[] = 'jadwal pelajaran';
        }
        if (!empty($relations)) {
            $relationsStr = implode(' dan ', $relations);
            $directions = [];
            if ($kelas->siswas()->exists()) {
                $directions[] = '<a href="' . route('siswa.index') . '" class="text-primary fw-bold text-decoration-underline">Data Siswa</a>';
            }
            if ($kelas->jadwalPelajarans()->exists()) {
                $directions[] = '<a href="' . route('jadwal-pelajaran.index') . '" class="text-primary fw-bold text-decoration-underline">Jadwal Pelajaran</a>';
            }
            $msg = "Kelas ini tidak dapat dihapus karena masih terkait dengan {$relationsStr}. Silakan hapus keterkaitan data tersebut terlebih dahulu di halaman " . implode(' dan ', $directions) . ".";
            
            return redirect()->route('kelas.index')
                ->with('error', $msg);
        }

        $kelas->delete();
        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
