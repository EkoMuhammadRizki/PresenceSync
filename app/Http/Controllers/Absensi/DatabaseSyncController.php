<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DatabaseSyncController extends Controller
{
    /**
     * Kirim database lokal ke hosting via HTTP.
     */
    public function sendToHosting(Request $request)
    {
        $mysqldumpPath = env('MYSQLDUMP_PATH', 'C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump.exe');
        $dbHost        = env('DB_HOST', '127.0.0.1');
        $dbPort        = env('DB_PORT', '3306');
        $dbName        = env('DB_DATABASE', 'presencesync');
        $dbUser        = env('DB_USERNAME', 'root');
        $dbPass        = env('DB_PASSWORD', '');
        $hostingUrl    = config('services.hosting_sync.url', env('HOSTING_SYNC_URL', 'https://siap-sman1ciparay.com/sync/receive-database'));
        $syncSecret    = config('services.hosting_sync.secret', env('HOSTING_SYNC_SECRET', '80666dc99520035d6bb10d85eeee90e89f839dfbc51bbf3f'));

        if (!$hostingUrl || !$syncSecret) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi HOSTING_SYNC_URL atau HOSTING_SYNC_SECRET belum diisi.',
            ], 500);
        }

        // Buat file SQL sementara di storage/app
        $sqlPath = storage_path('app/db_sync_export.sql');

        // Build perintah mysqldump
        $passOption = $dbPass ? "-p{$dbPass}" : '';
        $cmd = "\"{$mysqldumpPath}\" -h {$dbHost} -P {$dbPort} -u {$dbUser} {$passOption} --single-transaction --no-tablespaces {$dbName}";

        // Jalankan mysqldump
        $output = [];
        $returnCode = 0;
        exec("{$cmd} > \"{$sqlPath}\" 2>&1", $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($sqlPath) || filesize($sqlPath) < 100) {
            $errorMsg = implode("\n", $output);
            Log::error("DB Sync: mysqldump gagal. Code: {$returnCode}. Output: {$errorMsg}");
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor database lokal. Pastikan mysqldump tersedia dan konfigurasi DB sudah benar.',
                'detail'  => $errorMsg,
            ], 500);
        }

        // Kirim file SQL ke hosting
        try {
            $response = Http::timeout(120)
                ->withOptions([
                    'verify' => false,
                    'curl'   => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => 0,
                    ],
                ])
                ->withHeaders(['X-Sync-Secret' => $syncSecret])
                ->attach('sql_file', file_get_contents($sqlPath), 'db_sync.sql')
                ->post($hostingUrl);

            // Hapus file sementara
            @unlink($sqlPath);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => $data['message'] ?? 'Database berhasil disinkronkan ke hosting!',
                ]);
            } else {
                Log::error("DB Sync: Hosting menolak request. Status: {$response->status()}. Body: {$response->body()}");
                return response()->json([
                    'success' => false,
                    'message' => 'Hosting menolak sinkronisasi. Status: ' . $response->status(),
                    'detail'  => $response->body(),
                ], 500);
            }
        } catch (\Exception $e) {
            @unlink($sqlPath);
            Log::error("DB Sync: Exception - " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke hosting: ' . $e->getMessage(),
            ], 500);
        }
    }
}
