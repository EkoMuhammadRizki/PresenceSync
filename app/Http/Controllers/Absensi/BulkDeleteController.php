<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\AturanJam;
use App\Models\Kehadiran;
use App\Models\User;

class BulkDeleteController extends Controller
{
    public function destroy(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'required|integer',
        ]);

        $type = $request->input('type');
        $ids = $request->input('ids');

        $modelClass = match($type) {
            'siswa' => Siswa::class,
            'guru' => Guru::class,
            'kelas' => Kelas::class,
            'tahun-ajaran' => TahunAjaran::class,
            'semester' => Semester::class,
            'mata-pelajaran' => MataPelajaran::class,
            'jadwal-pelajaran' => JadwalPelajaran::class,
            'aturan-jam' => AturanJam::class,
            'kehadiran' => Kehadiran::class,
            'pengguna' => User::class,
            default => null,
        };

        if (!$modelClass) {
            return response()->json(['success' => false, 'message' => 'Tipe data tidak valid.'], 400);
        }

        try {
            if ($type === 'siswa') {
                $siswas = Siswa::whereIn('id', $ids)->with('user')->get();
                foreach ($siswas as $siswa) {
                    $user = $siswa->user;
                    $siswa->delete();
                    if ($user) {
                        $user->delete();
                    }
                }
            } elseif ($type === 'pengguna') {
                $users = User::whereIn('id', $ids)->with(['siswa', 'guru'])->get();
                foreach ($users as $user) {
                    if ($user->id === auth()->id()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Gagal menghapus data: Anda tidak dapat menghapus akun Anda sendiri.'
                        ], 400);
                    }
                    if ($user->guru) {
                        $relations = [];
                        if ($user->guru->kelas()->exists()) {
                            $relations[] = 'data kelas';
                        }
                        if ($user->guru->mataPelajarans()->exists()) {
                            $relations[] = 'mata pelajaran';
                        }
                        if (!empty($relations)) {
                            $relationsStr = implode(' dan ', $relations);
                            $directions = [];
                            if ($user->guru->kelas()->exists()) {
                                $directions[] = '<a href="' . route('kelas.index') . '" class="text-primary fw-bold text-decoration-underline">Data Kelas</a>';
                            }
                            if ($user->guru->mataPelajarans()->exists()) {
                                $directions[] = '<a href="' . route('mata-pelajaran.index') . '" class="text-primary fw-bold text-decoration-underline">Mata Pelajaran</a>';
                            }
                            $msg = "User Guru '{$user->username}' tidak dapat dihapus karena masih terkait dengan {$relationsStr}. Silakan hapus keterkaitan data tersebut terlebih dahulu di halaman " . implode(' dan ', $directions) . ".";
                            return response()->json([
                                'success' => false,
                                'message' => $msg
                            ], 400);
                        }
                        $user->guru->delete();
                    }
                    if ($user->siswa) {
                        $user->siswa->delete();
                    }
                    $user->delete();
                }
            } elseif ($type === 'guru') {
                $gurus = Guru::whereIn('id', $ids)->get();
                foreach ($gurus as $guru) {
                    $relations = [];
                    if ($guru->kelas()->exists()) {
                        $relations[] = 'data kelas';
                    }
                    if ($guru->mataPelajarans()->exists()) {
                        $relations[] = 'mata pelajaran';
                    }
                    if (!empty($relations)) {
                        $relationsStr = implode(' dan ', $relations);
                        $directions = [];
                        if ($guru->kelas()->exists()) {
                            $directions[] = '<a href="' . route('kelas.index') . '" class="text-primary fw-bold text-decoration-underline">Data Kelas</a>';
                        }
                        if ($guru->mataPelajarans()->exists()) {
                            $directions[] = '<a href="' . route('mata-pelajaran.index') . '" class="text-primary fw-bold text-decoration-underline">Mata Pelajaran</a>';
                        }
                        $msg = "Guru '{$guru->nama}' tidak dapat dihapus karena masih terkait dengan {$relationsStr}. Silakan hapus keterkaitan data tersebut terlebih dahulu di halaman " . implode(' dan ', $directions) . ".";
                        return response()->json([
                            'success' => false,
                            'message' => $msg
                        ], 400);
                    }
                    $user = $guru->user;
                    $guru->delete();
                    if ($user) {
                        $user->delete();
                    }
                }
            } elseif ($type === 'kelas') {
                $classes = Kelas::whereIn('id', $ids)->get();
                foreach ($classes as $kelas) {
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
                        $msg = "Kelas '{$kelas->nama}' tidak dapat dihapus karena masih terkait dengan {$relationsStr}. Silakan hapus keterkaitan data tersebut terlebih dahulu di halaman " . implode(' dan ', $directions) . ".";
                        return response()->json([
                            'success' => false,
                            'message' => $msg
                        ], 400);
                    }
                    $kelas->delete();
                }
            } elseif ($type === 'mata-pelajaran') {
                $subjects = MataPelajaran::whereIn('id', $ids)->get();
                foreach ($subjects as $mp) {
                    if ($mp->jadwalPelajarans()->exists()) {
                        $msg = "Mata pelajaran '{$mp->nama}' tidak dapat dihapus karena masih terkait dengan jadwal pelajaran. Silakan hapus keterkaitan data tersebut terlebih dahulu di halaman <a href=\"" . route('jadwal-pelajaran.index') . "\" class=\"text-primary fw-bold text-decoration-underline\">Jadwal Pelajaran</a>.";
                        return response()->json([
                            'success' => false,
                            'message' => $msg
                        ], 400);
                    }
                    $mp->delete();
                }
            } else {
                $modelClass::whereIn('id', $ids)->delete();
            }

            return response()->json(['success' => true, 'message' => 'Data terpilih berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
