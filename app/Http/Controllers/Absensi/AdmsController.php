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
     * Menerima Push Log Absensi Realtime dari mesin ADMS
     * POST /iclock/cdata
     */
    public function receiveData(Request $request)
    {
        $sn = $request->query('SN');
        $table = $request->query('table', 'ATTLOG');
        $content = $request->getContent();

        Log::info("ADMS Push Data received", [
            'sn' => $sn,
            'table' => $table,
            'body_length' => strlen($content)
        ]);

        $device = null;
        if ($sn) {
            $device = FingerprintDevice::where('serial_number', $sn)->first();
        }
        if (!$device) {
            $device = FingerprintDevice::first();
        }

        $processedCount = 0;

        if ($content && ($table === 'ATTLOG' || str_contains($content, "\t"))) {
            $lines = explode("\n", trim($content));
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Format ADMS ATTLOG: PIN \t DateTime \t Status \t Verified
                $cols = preg_split('/\s+/', $line);
                if (count($cols) >= 2) {
                    $pin = $cols[0];
                    $dateTimeStr = $cols[1] . (isset($cols[2]) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $cols[2]) ? ' ' . $cols[2] : '');

                    try {
                        $scanTime = Carbon::parse($dateTimeStr);

                        // Simpan log ke database
                        $log = FingerprintSyncLog::firstOrCreate([
                            'fingerprint_device_id' => $device?->id ?? 1,
                            'fingerprint_uid'       => $pin,
                            'scan_time'             => $scanTime,
                        ], [
                            'raw_payload'  => $line,
                            'is_processed' => false,
                        ]);

                        if ($log->wasRecentlyCreated) {
                            $this->fingerprintService->processSingleLog($log);
                            $processedCount++;
                        }
                    } catch (\Throwable $e) {
                        Log::error("Gagal memproses ADMS line: {$line}", ['error' => $e->getMessage()]);
                    }
                }
            }
        }

        if ($device) {
            $device->update([
                'last_synced_at' => now(),
                'total_synced_logs' => FingerprintSyncLog::where('fingerprint_device_id', $device->id)->count(),
            ]);
        }

        return response("OK: {$processedCount}", 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Menerima polling perintah dari mesin ADMS
     * GET /iclock/getrequest
     */
    public function getRequest(Request $request)
    {
        return response("OK", 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Menerima konfirmasi hasil perintah mesin ADMS
     * POST /iclock/devicecmd
     */
    public function deviceCmd(Request $request)
    {
        return response("OK", 200)
            ->header('Content-Type', 'text/plain');
    }
}
