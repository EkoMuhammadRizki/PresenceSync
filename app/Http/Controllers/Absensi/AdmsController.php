<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\FingerprintDevice;
use App\Models\FingerprintSyncLog;
use App\Services\FingerprintService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdmsController extends Controller
{
    public function __construct(protected FingerprintService $fingerprintService)
    {
    }

    /**
     * Handshake & registrasi awal mesin ADMS
     * GET /iclock/cdata
     */
    public function handshake(Request $request)
    {
        $sn = $request->query('SN');
        
        Log::info("ADMS Handshake Request received", [
            'sn' => $sn,
            'params' => $request->all()
        ]);

        if ($sn) {
            $device = FingerprintDevice::where('serial_number', $sn)->first();
            if ($device) {
                $device->update([
                    'is_aktif' => true,
                    'last_synced_at' => now(),
                ]);
            }
        }

        // Response standar ADMS ZKTeco
        return response("OK", 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Menerima Push Log Absensi Realtime dari mesin ADMS (High-Performance Batch Ingestion)
     * POST /iclock/cdata
     */
    public function receiveData(Request $request)
    {
        $sn = $request->query('SN');
        $table = $request->query('table', 'ATTLOG');
        $content = $request->getContent();

        if (empty($content)) {
            return response("OK", 200)->header('Content-Type', 'text/plain');
        }

        // 1. Dapatkan device
        $device = null;
        if ($sn) {
            $device = FingerprintDevice::where('serial_number', $sn)->first();
        }
        if (!$device) {
            $device = FingerprintDevice::first();
        }
        $deviceId = $device?->id ?? 1;

        // 2. Parse semua baris scan dalam memori (Zero DB Query pada tahap parsing)
        $lines = explode("\n", trim($content));
        $parsedLogs = [];
        $pins = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $cols = preg_split('/[\t,]+/', $line);
            if (count($cols) >= 2) {
                $pin = trim($cols[0]);
                $dateTimeStr = trim($cols[1]);
                if (isset($cols[2]) && preg_match('/^\d{2}:\d{2}:\d{2}$/', trim($cols[2]))) {
                    $dateTimeStr .= ' ' . trim($cols[2]);
                }

                try {
                    $scanTime = Carbon::parse($dateTimeStr);
                    $verifiedCode = 1;
                    if (isset($cols[3])) {
                        $verifiedCode = (int) trim($cols[3]);
                    } elseif (isset($cols[2]) && !preg_match('/^\d{2}:\d{2}:\d{2}$/', trim($cols[2]))) {
                        $verifiedCode = (int) trim($cols[2]);
                    }

                    $parsedLogs[] = [
                        'pin'          => $pin,
                        'scan_time'    => $scanTime,
                        'scan_date'    => $scanTime->toDateString(),
                        'scan_his'     => $scanTime->format('H:i:s'),
                        'hari'         => strtolower($scanTime->locale('id')->isoFormat('dddd')),
                        'verified'     => $verifiedCode,
                    ];
                    $pins[] = $pin;
                } catch (\Throwable $e) {
                    // Skip malformed line
                }
            }
        }

        if (empty($parsedLogs)) {
            return response("OK: 0", 200)->header('Content-Type', 'text/plain');
        }

        $uniquePins = array_unique($pins);

        // 3. PRELOAD SEMUA DATA MASTER DALAM 1 BATCH (Super Fast In-Memory Lookup)
        $semester = \App\Models\Semester::where('status', 'aktif')->first();
        $aturanJams = \App\Models\AturanJam::where('is_aktif', true)->get()->keyBy('hari');

        // Preload Siswa yang relevan dalam 1 query
        $siswas = \App\Models\Siswa::whereIn('fingerprint_id', $uniquePins)
            ->orWhereIn('id', $uniquePins)
            ->get();
        
        $siswaMap = [];
        $unenrolledSiswaIds = [];
        foreach ($siswas as $s) {
            if ($s->fingerprint_id) {
                $siswaMap[(string)$s->fingerprint_id] = $s;
            }
            $siswaMap[(string)$s->id] = $s;
            if (!$s->is_enrolled) {
                $unenrolledSiswaIds[] = $s->id;
            }
        }

        // Tandai siswa yang baru scan sebagai is_enrolled secara bulk
        if (!empty($unenrolledSiswaIds)) {
            \App\Models\Siswa::whereIn('id', array_unique($unenrolledSiswaIds))->update(['is_enrolled' => true]);
        }

        // Preload existing sync logs untuk batch waktu terkait
        $minTime = min(array_column($parsedLogs, 'scan_time'));
        $maxTime = max(array_column($parsedLogs, 'scan_time'));
        
        $existingSyncLogs = FingerprintSyncLog::where('fingerprint_device_id', $deviceId)
            ->whereIn('fingerprint_uid', $uniquePins)
            ->whereBetween('scan_time', [$minTime, $maxTime])
            ->get()
            ->keyBy(fn($item) => $item->fingerprint_uid . '_' . $item->scan_time->toDateTimeString());

        // Preload existing kehadirans untuk siswa & tanggal terkait
        $dates = array_unique(array_column($parsedLogs, 'scan_date'));
        $siswaIds = $siswas->pluck('id')->toArray();
        $existingKehadirans = \App\Models\Kehadiran::whereIn('siswa_id', $siswaIds)
            ->whereIn('tanggal', $dates)
            ->get()
            ->keyBy(fn($k) => $k->siswa_id . '_' . $k->tanggal);

        $processedCount = 0;
        $newLogsCount = 0;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($parsedLogs as $logItem) {
                $pin = (string) $logItem['pin'];
                $scanTimeStr = $logItem['scan_time']->toDateTimeString();
                $logKey = $pin . '_' . $scanTimeStr;

                $syncLog = $existingSyncLogs->get($logKey);

                if (!$syncLog) {
                    $syncLog = new FingerprintSyncLog([
                        'fingerprint_device_id' => $deviceId,
                        'fingerprint_uid'       => $pin,
                        'scan_time'             => $logItem['scan_time'],
                        'verified'              => 1,
                        'status'                => 0,
                        'is_processed'          => false,
                    ]);
                    $syncLog->save();
                    $existingSyncLogs->put($logKey, $syncLog);
                    $newLogsCount++;
                }

                if ($syncLog->is_processed) {
                    continue;
                }

                $siswa = $siswaMap[$pin] ?? null;
                if (!$siswa) {
                    $syncLog->update([
                        'is_processed' => true,
                        'error_note'   => "Siswa dengan fingerprint_id={$pin} tidak ditemukan",
                    ]);
                    continue;
                }

                if (!$siswa->kelas_id) {
                    $syncLog->update([
                        'is_processed' => true,
                        'error_note'   => "Siswa belum memiliki kelas",
                    ]);
                    continue;
                }

                if (!$semester) {
                    $syncLog->update([
                        'is_processed' => true,
                        'error_note'   => "Tidak ada semester aktif",
                    ]);
                    continue;
                }

                $scanDate = $logItem['scan_date'];
                $scanTimeHis = $logItem['scan_his'];
                $hari = $logItem['hari'];
                $aturanJam = $aturanJams->get($hari);

                $status = 'hadir';
                if ($aturanJam) {
                    $jamMasukAturan = Carbon::createFromFormat('H:i:s', $aturanJam->jam_masuk);
                    $jamMasukDevice = Carbon::createFromFormat('H:i:s', $scanTimeHis);
                    if ($jamMasukDevice->gt($jamMasukAturan)) {
                        $status = 'terlambat';
                    }
                }

                $kehadiranKey = $siswa->id . '_' . $scanDate;
                $kehadiran = $existingKehadirans->get($kehadiranKey);

                if (!$kehadiran) {
                    $kehadiran = \App\Models\Kehadiran::create([
                        'siswa_id'           => $siswa->id,
                        'semester_id'        => $semester->id,
                        'aturan_jam_id'      => $aturanJam?->id,
                        'tanggal'            => $scanDate,
                        'jam_masuk'          => $scanTimeHis,
                        'status'             => $status,
                        'source'             => 'fingerprint',
                        'fingerprint_log_id' => $syncLog->id,
                    ]);
                    $existingKehadirans->put($kehadiranKey, $kehadiran);
                    $processedCount++;
                } else {
                    // Update jam pulang jika memenuhi batas awal pulang
                    if ($scanTimeHis > $kehadiran->jam_masuk) {
                        $batasAwalPulang = $aturanJam ? ($aturanJam->batas_awal_pulang ?? 0) : 0;
                        $jamMasukLog = Carbon::createFromFormat('H:i:s', $kehadiran->jam_masuk);
                        $jamScan = Carbon::createFromFormat('H:i:s', $scanTimeHis);
                        $jamBatasPulang = $jamMasukLog->copy()->addMinutes($batasAwalPulang);

                        if ($jamScan->gte($jamBatasPulang)) {
                            $kehadiran->update(['jam_pulang' => $scanTimeHis]);
                        }
                    }
                    $processedCount++;
                }

                $syncLog->update([
                    'is_processed' => true,
                    'kehadiran_id' => $kehadiran->id,
                    'error_note'   => null,
                ]);
            }

            if ($device) {
                $device->update([
                    'last_synced_at'    => now(),
                    'total_synced_logs' => $device->total_synced_logs + $newLogsCount,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error("ADMS Batch Ingestion Error: " . $e->getMessage());
        }

        return response("OK: {$processedCount}", 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Tambahkan perintah ADMS ke antrean (Queue) agar diambil oleh mesin fisik saat polling
     */
    public static function queueCommand(string $commandText)
    {
        $queue = \Illuminate\Support\Facades\Cache::get('adms_cmd_queue', []);
        $nextId = (int) \Illuminate\Support\Facades\Cache::get('adms_cmd_last_id', 100) + 1;
        \Illuminate\Support\Facades\Cache::put('adms_cmd_last_id', $nextId);

        $queue[] = [
            'id'      => $nextId,
            'command' => "C:{$nextId}:{$commandText}",
        ];

        \Illuminate\Support\Facades\Cache::put('adms_cmd_queue', $queue, now()->addDays(7));
        Log::info("ADMS Command Queued: C:{$nextId}:{$commandText}");
    }

    /**
     * Menerima polling perintah dari mesin ADMS
     * GET /iclock/getrequest
     */
    public function getRequest(Request $request)
    {
        $sn = $request->query('SN');
        if ($sn) {
            $device = FingerprintDevice::where('serial_number', $sn)->first();
            if ($device) {
                $device->update([
                    'is_aktif'       => true,
                    'last_synced_at' => now(),
                ]);
            }
        }

        // Cek apakah ada antrean perintah ADMS dari cloud hosting (seperti upload/delete user)
        $queue = \Illuminate\Support\Facades\Cache::get('adms_cmd_queue', []);
        if (!empty($queue)) {
            $item = array_shift($queue);
            \Illuminate\Support\Facades\Cache::put('adms_cmd_queue', $queue, now()->addDays(7));

            Log::info("ADMS sending command to device {$sn}", ['cmd' => $item['command']]);

            return response($item['command'], 200)
                ->header('Content-Type', 'text/plain');
        }

        return response("OK", 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Menerima konfirmasi hasil perintah mesin ADMS
     * POST /iclock/devicecmd
     */
    public function deviceCmd(Request $request)
    {
        $content = $request->getContent();
        Log::info("ADMS DeviceCmd Response received", ['body' => $content]);

        return response("OK", 200)
            ->header('Content-Type', 'text/plain');
    }
}
