<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\AturanJam;
use App\Models\Kehadiran;

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
            'jurusan' => Jurusan::class,
            'kelas' => Kelas::class,
            'tahun-ajaran' => TahunAjaran::class,
            'semester' => Semester::class,
            'mata-pelajaran' => MataPelajaran::class,
            'jadwal-pelajaran' => JadwalPelajaran::class,
            'aturan-jam' => AturanJam::class,
            'kehadiran' => Kehadiran::class,
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
