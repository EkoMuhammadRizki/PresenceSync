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

        $deletedCount = 0;
        $deletedItems = [];

        try {
            if ($type === 'siswa') {
                $siswas = Siswa::whereIn('id', $ids)->with('user')->get();
                $deletedCount = $siswas->count();
                // Kumpulkan fingerprint IDs sebelum delete
                $fingerprintIds = $siswas->pluck('fingerprint_id')->filter()->values()->toArray();

                foreach ($siswas as $siswa) {
                    if (count($deletedItems) < 100) {
                        $deletedItems[] = [
                            'nama' => $siswa->nama,
                            'code' => 'NIS: ' . ($siswa->nis ?: '-'),
                        ];
                    }
                    $user = $siswa->user;
                    $siswa->delete();
                    if ($user) {
                        $user->delete();
                    }
                }

                // Hapus dari semua perangkat mesin fingerprint yang aktif & queue ADMS delete
                if (!empty($fingerprintIds)) {
                    foreach ($fingerprintIds as $fpId) {
                        $admsCmd = "DATA DELETE USER PIN={$fpId}";
                        \App\Http\Controllers\Absensi\AdmsController::queueCommand($admsCmd);
                    }
                    try {
                        $service = app(\App\Services\FingerprintService::class);
                        $activeDevices = \App\Models\FingerprintDevice::where('is_aktif', true)->get();
                        foreach ($activeDevices as $dev) {
                            foreach ($fingerprintIds as $fpId) {
                                $service->deleteUser($dev, (string) $fpId);
                            }
                            $service->refreshDB($dev);
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Bulk delete fingerprint error: " . $e->getMessage());
                    }
                }
            } elseif ($type === 'pengguna') {
                $users = User::whereIn('id', $ids)->with(['siswa', 'guru'])->get();
                $deletedCount = $users->count();
                foreach ($users as $user) {
                    if ($user->id === auth()->id()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Gagal menghapus data: Anda tidak dapat menghapus akun Anda sendiri.'
                        ], 400);
                    }
                    if (count($deletedItems) < 100) {
                        $deletedItems[] = [
                            'nama' => $user->first_name . ' ' . $user->last_name,
                            'code' => $user->email,
                        ];
                    }
                    if ($user->guru) {
                        $user->guru->delete();
                    }
                    if ($user->siswa) {
                        $user->siswa->delete();
                    }
                    $user->delete();
                }
            } elseif ($type === 'guru') {
                $gurus = Guru::whereIn('id', $ids)->get();
                $deletedCount = $gurus->count();
                foreach ($gurus as $guru) {
                    if (count($deletedItems) < 100) {
                        $deletedItems[] = [
                            'nama' => $guru->nama,
                            'code' => 'NIP: ' . ($guru->nip ?: '-'),
                        ];
                    }
                    $user = $guru->user;
                    $guru->delete();
                    if ($user) {
                        $user->delete();
                    }
                }
            } elseif ($type === 'kelas') {
                $classes = Kelas::whereIn('id', $ids)->get();
                $deletedCount = $classes->count();
                foreach ($classes as $kelas) {
                    if (count($deletedItems) < 100) {
                        $deletedItems[] = [
                            'nama' => $kelas->nama,
                            'code' => 'Tingkat ' . $kelas->tingkat,
                        ];
                    }
                    $kelas->delete();
                }
            } elseif ($type === 'mata-pelajaran') {
                $subjects = MataPelajaran::whereIn('id', $ids)->get();
                $deletedCount = $subjects->count();
                foreach ($subjects as $mp) {
                    if (count($deletedItems) < 100) {
                        $deletedItems[] = [
                            'nama' => $mp->nama,
                            'code' => 'Kode: ' . ($mp->kode ?? '-'),
                        ];
                    }
                    $mp->delete();
                }
            } else {
                $deletedCount = count($ids);
                $modelClass::whereIn('id', $ids)->delete();
            }

            session()->flash('delete_success', [
                'type'  => $type,
                'count' => $deletedCount,
                'items' => $deletedItems,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Sebanyak {$deletedCount} data berhasil dihapus.",
                'deleted_count' => $deletedCount,
                'deleted_items' => $deletedItems,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
