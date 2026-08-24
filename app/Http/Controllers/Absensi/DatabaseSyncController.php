<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DatabaseSyncController extends Controller
{
    /**
     * Tabel-tabel yang disinkronkan ke hosting
     */
    protected array $syncTables = [
        'settings',
        'semesters',
        'tahun_ajarans',
        'roles',
        'permissions',
        'kelas',
        'mata_pelajarans',
        'aturan_jams',
        'fingerprint_devices',
        'users',
        'user_infos',
        'model_has_roles',
        'gurus',
        'siswas',
        'parent_profiles',
        'jadwal_pelajarans',
        'fingerprint_sync_logs',
        'kehadirans',
        'kehadiran_mata_pelajarans',
        'kehadiran_mata_pelajaran_details',
        'pengaduans',
    ];

    /**
     * Kirim dataset database lokal ke hosting via HTTP (Smart Merge Payload).
     */
    public function sendToHosting(Request $request)
    {
        $hostingUrl = config('services.hosting_sync.url', env('HOSTING_SYNC_URL', 'https://siap-sman1ciparay.com/sync/receive-database'));
        $syncSecret = config('services.hosting_sync.secret', env('HOSTING_SYNC_SECRET', '80666dc99520035d6bb10d85eeee90e89f839dfbc51bbf3f'));

        if (!$hostingUrl || !$syncSecret) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi HOSTING_SYNC_URL atau HOSTING_SYNC_SECRET belum diisi.',
            ], 500);
        }

        try {
            // 1. Kumpulkan data dari tabel-tabel lokal
            $exportData = [];
            $totalRecords = 0;

            foreach ($this->syncTables as $table) {
                try {
                    $rows = DB::table($table)->get()->map(function ($row) {
                        return (array) $row;
                    })->toArray();

                    $exportData[$table] = $rows;
                    $totalRecords += count($rows);
                } catch (\Throwable $e) {
                    $exportData[$table] = [];
                }
            }

            // 2. Encode JSON dan kompresi dengan gzip
            $jsonPayload = json_encode([
                'version'       => '2.0',
                'source'        => config('app.url', 'http://127.0.0.1:8000'),
                'exported_at'   => now()->toDateTimeString(),
                'total_records' => $totalRecords,
                'data'          => $exportData,
            ], JSON_UNESCAPED_UNICODE);

            $compressed = gzencode($jsonPayload, 9);

            // 3. Kirim ke hosting via HTTP Multipart
            $response = Http::timeout(180)
                ->withOptions([
                    'verify' => false,
                    'curl'   => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => 0,
                    ],
                ])
                ->withHeaders(['X-Sync-Secret' => $syncSecret])
                ->attach('sync_payload', $compressed, 'dataset.json.gz')
                ->post($hostingUrl);

            if ($response->successful()) {
                $resData = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => $resData['message'] ?? 'Database berhasil disinkronkan ke hosting!',
                    'summary' => $resData['summary'] ?? null,
                ]);
            } else {
                Log::error("DB Sync: Hosting menolak request. Status: {$response->status()}. Body: {$response->body()}");
                $errJson = $response->json();
                return response()->json([
                    'success' => false,
                    'message' => $errJson['message'] ?? ('Hosting menolak sinkronisasi. Status: ' . $response->status()),
                    'detail'  => $response->body(),
                ], 500);
            }
        } catch (\Throwable $e) {
            Log::error("DB Sync Exception: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke hosting: ' . $e->getMessage(),
            ], 500);
        }
    }
}
