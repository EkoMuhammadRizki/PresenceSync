<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\FingerprintDevice;
use App\Models\FingerprintSyncLog;
use App\Models\Siswa;
use App\Services\FingerprintService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FingerprintController extends Controller
{
    public function __construct(protected FingerprintService $service)
    {
    }

    /**
     * Halaman manajemen device fingerprint
     */
    public function index()
    {
        $devices = FingerprintDevice::withCount('syncLogs')
            ->orderByDesc('is_aktif')
            ->orderBy('nama')
            ->get();

        // Log sync terbaru (50 data terakhir dari semua device)
        $recentLogs = FingerprintSyncLog::with(['device'])
            ->orderByDesc('scan_time')
            ->limit(50)
            ->get();

        // Siswa yang sudah enrolled (sudah daftar scan sidik jari / terdeteksi hardware)
        // Hanya siswa aktif (status='aktif' atau null/kosong) yang ditampilkan
        $siswaWithFingerprint = Siswa::where(function ($q) {
                $q->where('status', 'aktif')
                  ->orWhereNull('status')
                  ->orWhere('status', '');
            })
            ->where(function ($q) {
                $q->where('is_enrolled', true)
                  ->orWhereHas('syncLogs');
            })
            ->with('kelas')
            ->orderBy('nama')
            ->get();

        // Siswa yang belum enrolled (siswa yang baru dibuat saja datanya di tabel siswa)
        // Hanya siswa aktif (status='aktif' atau null/kosong) yang ditampilkan
        $siswaTanpaFingerprint = Siswa::where(function ($q) {
                $q->where('status', 'aktif')
                  ->orWhereNull('status')
                  ->orWhere('status', '');
            })
            ->where('is_enrolled', false)
            ->whereDoesntHave('syncLogs')
            ->with('kelas')
            ->orderBy('nama')
            ->get();

        // Stats ringkasan
        $stats = [
            'total_devices'   => $devices->count(),
            'active_devices'  => $devices->filter(fn($d) => $d->isConnected())->count(),
            'total_logs'      => FingerprintSyncLog::count(),
            'processed_logs'  => FingerprintSyncLog::where('is_processed', true)->count(),
            'pending_logs'    => FingerprintSyncLog::where('is_processed', false)->count(),
            'siswa_enrolled'  => $siswaWithFingerprint->count(),
        ];

        $kelases = \App\Models\Kelas::orderBy('nama')->get();

        return view('pages.absensi.fingerprint', compact(
            'devices', 'recentLogs', 'siswaTanpaFingerprint', 'siswaWithFingerprint', 'stats', 'kelases'
        ));
    }

    /**
     * Tambah device baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:100',
            'ip_address'    => 'required|ip',
            'port'          => 'required|integer|min:1|max:65535',
            'com_key'       => 'nullable|integer|min:0',
            'serial_number' => 'nullable|string|max:50',
        ]);

        $validated['com_key'] = $validated['com_key'] ?? 0;

        FingerprintDevice::create($validated);

        return redirect()->route('fingerprint.index')
            ->with('success', "Device {$validated['nama']} berhasil ditambahkan.");
    }

    /**
     * Update device
     */
    public function update(Request $request, FingerprintDevice $device)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:100',
            'ip_address'    => 'required|ip',
            'port'          => 'required|integer|min:1|max:65535',
            'com_key'       => 'nullable|integer|min:0',
            'serial_number' => 'nullable|string|max:50',
            'is_aktif'      => 'boolean',
        ]);

        $validated['com_key'] = $validated['com_key'] ?? 0;

        $device->update($validated);

        return redirect()->route('fingerprint.index')
            ->with('success', "Device {$device->nama} berhasil diperbarui.");
    }

    /**
     * Nonaktifkan/hapus device
     */
    public function destroy(FingerprintDevice $device)
    {
        $nama = $device->nama;
        $device->update(['is_aktif' => false]);

        return redirect()->route('fingerprint.index')
            ->with('success', "Device {$nama} berhasil dinonaktifkan.");
    }

    /**
     * AJAX: Test koneksi ke device
     */
    public function testConnection(FingerprintDevice $device)
    {
        $result = $this->service->testConnection($device);

        return response()->json($result);
    }

    /**
     * AJAX: Trigger sync manual dari browser
     */
    public function triggerSync(Request $request, FingerprintDevice $device)
    {
        $clearAfterSync = $request->boolean('clear_after_sync', false);
        $stats = $this->service->syncAndProcess($device, $clearAfterSync);

        if (!empty($stats['error_message'])) {
            return response()->json([
                'success' => false,
                'message' => $stats['error_message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Sync selesai! Diambil: {$stats['fetched']} | Baru: {$stats['new']} | Diproses: {$stats['processed']}",
            'stats'   => $stats,
        ]);
    }

    /**
     * AJAX: Sync waktu device dengan server
     */
    public function syncTime(FingerprintDevice $device)
    {
        $result = $this->service->syncTime($device);

        return response()->json($result);
    }

    /**
     * AJAX: Upload nama siswa ke device
     */
    public function uploadNames(Request $request, FingerprintDevice $device)
    {
        $siswaList = Siswa::where('status', 'aktif')
            ->whereNotNull('fingerprint_id')
            ->where('fingerprint_id', '!=', '')
            ->get();

        $success = 0;
        $errors  = 0;

        foreach ($siswaList as $siswa) {
            $result = $this->service->uploadUserName($device, $siswa->fingerprint_id, $siswa->nama);
            if ($result['success']) {
                $success++;
            } else {
                $errors++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Upload selesai! Berhasil: {$success} | Gagal: {$errors}",
            'uploaded' => $success,
            'errors'   => $errors,
        ]);
    }

    /**
     * AJAX: Toggle status aktif device
     */
    public function toggleStatus(FingerprintDevice $device)
    {
        $device->update(['is_aktif' => !$device->is_aktif]);

        return response()->json([
            'success'  => true,
            'is_aktif' => $device->is_aktif,
            'label'    => $device->status_label,
            'badge'    => $device->status_badge,
        ]);
    }

    /**
     * AJAX: Data log sync untuk DataTables (dengan filter device)
     */
    public function logData(Request $request)
    {
        $deviceId = $request->input('device_id');

        $query = FingerprintSyncLog::with('device')
            ->orderByDesc('scan_time');

        if ($deviceId) {
            $query->where('fingerprint_device_id', $deviceId);
        }

        $logs = $query->limit(200)->get()->map(function ($log) {
            return [
                'id'             => $log->id,
                'device'         => $log->device?->nama ?? '-',
                'fingerprint_uid' => $log->fingerprint_uid,
                'scan_time'      => $log->scan_time?->format('d/m/Y H:i:s') ?? '-',
                'is_processed'   => $log->is_processed,
                'status_badge'   => $log->is_processed
                    ? '<span class="badge badge-light-success">Diproses</span>'
                    : '<span class="badge badge-light-warning">Pending</span>',
                'error_note'     => $log->error_note ?? '-',
            ];
        });

        return response()->json(['data' => $logs]);
    }

    /**
     * Halaman Log Scan Fingerprint (Tabel live log absensi)
     */
    public function logsView(Request $request)
    {
        // 1. Tarik log secara cepat dari mesin fisik yang aktif
        try {
            $activeDevices = FingerprintDevice::where('is_aktif', true)->get();
            foreach ($activeDevices as $dev) {
                $this->service->syncAndProcess($dev);
            }
        } catch (\Throwable $e) {}

        // 2. Ambil input filter
        $deviceId  = $request->input('device_id');
        $kelasId   = $request->input('kelas_id');
        $search    = $request->input('search');
        $dateRange = $request->input('date_range');

        // 3. Query log scan dengan index & eager loading optimal
        $query = FingerprintSyncLog::with(['device', 'kehadiran'])
            ->orderByDesc('scan_time');

        if ($deviceId) {
            $query->where('fingerprint_device_id', $deviceId);
        }

        if ($kelasId) {
            $siswaFingerprintIds = Siswa::where('kelas_id', $kelasId)
                ->whereNotNull('fingerprint_id')
                ->where('fingerprint_id', '!=', '')
                ->pluck('fingerprint_id');

            $query->whereIn('fingerprint_uid', $siswaFingerprintIds);
        }

        if ($search) {
            $matchingSiswaUids = Siswa::where('nama', 'LIKE', "%{$search}%")
                ->orWhere('nis', 'LIKE', "%{$search}%")
                ->pluck('fingerprint_id')
                ->filter();

            $query->where(function ($q) use ($search, $matchingSiswaUids) {
                $q->where('fingerprint_uid', 'LIKE', "%{$search}%")
                  ->orWhereIn('fingerprint_uid', $matchingSiswaUids)
                  ->orWhereHas('device', function ($dq) use ($search) {
                      $dq->where('nama', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($dateRange) {
            $dates = preg_split('/\s+(to|s\/d|-)\s+/', $dateRange);
            if (count($dates) == 2) {
                try {
                    $startStr = trim($dates[0]);
                    $endStr   = trim($dates[1]);
                    $start    = Carbon::parse($startStr);
                    $end      = Carbon::parse($endStr);

                    if (strlen($startStr) <= 10) {
                        $start = $start->startOfDay();
                    }
                    if (strlen($endStr) <= 10) {
                        $end = $end->endOfDay();
                    }

                    $query->whereBetween('scan_time', [$start, $end]);
                } catch (\Exception $e) {}
            } else {
                try {
                    $singleStr = trim($dateRange);
                    $single    = Carbon::parse($singleStr);
                    if (strlen($singleStr) <= 10) {
                        $query->whereBetween('scan_time', [$single->copy()->startOfDay(), $single->copy()->endOfDay()]);
                    } else {
                        $query->where('scan_time', '>=', $single);
                    }
                } catch (\Exception $e) {}
            }
        }

        // 4. Paginate data (hanya 15 log per halaman)
        $logs = $query->paginate(15)->withQueryString();
        $devices = FingerprintDevice::orderBy('nama')->get();
        $kelases = \App\Models\Kelas::orderBy('nama')->get();

        // 5. Eager load siswa HANYA untuk 15 baris yang sedang tampil (Super Cepat!)
        $pageUids = $logs->pluck('fingerprint_uid')->filter()->unique()->toArray();
        $siswasMap = collect();
        if (!empty($pageUids)) {
            $siswasMap = Siswa::with('kelas')
                ->whereIn('fingerprint_id', $pageUids)
                ->orWhereIn('id', $pageUids)
                ->get()
                ->keyBy(fn($s) => $s->fingerprint_id ?: $s->id);
        }

        // 6. Statistik cepat
        $stats = [
            'total_logs'     => FingerprintSyncLog::count(),
            'today_logs'     => FingerprintSyncLog::whereDate('scan_time', Carbon::today())->count(),
            'processed_logs' => FingerprintSyncLog::where('is_processed', true)->count(),
            'pending_logs'   => FingerprintSyncLog::where('is_processed', false)->count(),
        ];

        return view('pages.absensi.fingerprint-log-data', compact(
            'logs', 'devices', 'kelases', 'siswasMap', 'stats',
            'deviceId', 'kelasId', 'search', 'dateRange'
        ));
    }

    /**
     * AJAX: Realtime Auto Detection & Pull from active devices
     */
    public function autoSync()
    {
        $totalNew = 0;
        $processed = 0;

        try {
            $devices = FingerprintDevice::where('is_aktif', true)->get();
            foreach ($devices as $device) {
                $stats = $this->service->syncAndProcess($device);
                $totalNew  += $stats['new'] ?? 0;
                $processed += $stats['processed'] ?? 0;
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success'   => true,
            'new_logs'  => $totalNew,
            'processed' => $processed,
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Hapus single log scan fingerprint
     */
    public function destroyLog(FingerprintSyncLog $log)
    {
        \App\Models\Kehadiran::where('fingerprint_log_id', $log->id)
            ->update(['fingerprint_log_id' => null]);

        $log->delete();

        return redirect()->back()
            ->with('success', "Log scan ID {$log->fingerprint_uid} berhasil dihapus.");
    }

    /**
     * Hapus semua log scan fingerprint (Instant Clean)
     */
    public function clearLogs(Request $request)
    {
        $deviceId = $request->input('device_id');

        $query = FingerprintSyncLog::query();
        if ($deviceId) {
            $query->where('fingerprint_device_id', $deviceId);
        }

        $logIds = $query->pluck('id');

        if ($logIds->isNotEmpty()) {
            \App\Models\Kehadiran::whereIn('fingerprint_log_id', $logIds)
                ->update(['fingerprint_log_id' => null]);
            $count = $query->delete();
        } else {
            $count = 0;
        }

        // Antrekan perintah CLEAR LOG ke mesin ADMS secara non-blocking
        \App\Http\Controllers\Absensi\AdmsController::queueCommand('CLEAR LOG');

        // Reset counter device
        if ($deviceId) {
            FingerprintDevice::where('id', $deviceId)->update(['total_synced_logs' => 0]);
        } else {
            FingerprintDevice::query()->update(['total_synced_logs' => 0]);
        }

        return redirect()->back()
            ->with('success', "Sebanyak {$count} log scan fingerprint berhasil dibersihkan dari sistem.");
    }

    /**
     * Toggle status enrollment siswa (Sudah Enrolled <-> Belum Enrolled)
     */
    public function toggleEnrollment(Siswa $siswa)
    {
        $newStatus = !$siswa->is_enrolled;
        $siswa->update(['is_enrolled' => $newStatus]);

        $label = $newStatus ? 'Sudah Enrolled' : 'Belum Enrolled';

        return redirect()->back()
            ->with('success', "Status enrollment siswa \"{$siswa->nama}\" berhasil diubah menjadi {$label}.");
    }

    /**
     * Sinkronkan semua nama & template sidik jari dari mesin satu ke seluruh mesin aktif
     */
    public function syncAllTemplatesToDevices()
    {
        $devices = FingerprintDevice::where('is_aktif', true)->get();

        if ($devices->count() < 1) {
            return redirect()->back()->with('error', 'Tidak ada perangkat fingerprint aktif.');
        }

        $siswas = Siswa::whereNotNull('fingerprint_id')
            ->where('fingerprint_id', '!=', '')
            ->get();

        $uploadedNames = 0;
        $copiedTemplates = 0;

        // 1. Upload Nama Siswa ke semua mesin aktif
        foreach ($devices as $device) {
            foreach ($siswas as $siswa) {
                $res = $this->service->uploadUserName($device, (string) $siswa->fingerprint_id, $siswa->nama);
                if ($res['success']) {
                    $uploadedNames++;
                }
            }
        }

        // 2. Tarik template sidik jari dari mesin yang memiliki pendaftaran jari, lalu salin ke seluruh mesin
        foreach ($siswas as $siswa) {
            $pin = (string) $siswa->fingerprint_id;
            $foundTemplates = [];

            foreach ($devices as $device) {
                for ($fingerIndex = 0; $fingerIndex <= 9; $fingerIndex++) {
                    if (isset($foundTemplates[$fingerIndex])) continue;

                    $tRes = $this->service->downloadFingerprintTemplate($device, $pin, $fingerIndex);
                    if ($tRes['success'] && !empty($tRes['templates'])) {
                        $foundTemplates[$fingerIndex] = $tRes['templates'][0]['Template'];
                    }
                }
            }

            foreach ($foundTemplates as $fingerIndex => $templateStr) {
                foreach ($devices as $targetDevice) {
                    $upRes = $this->service->uploadFingerprintTemplate($targetDevice, $pin, (int) $fingerIndex, $templateStr);
                    if ($upRes['success']) {
                        $copiedTemplates++;
                    }
                }
            }
        }

        return redirect()->back()->with('success', "Sinkronisasi Antar Mesin Selesai! Nama ter-upload: {$uploadedNames} | Template sidik jari ter-salin ke semua mesin: {$copiedTemplates}.");
    }
}
