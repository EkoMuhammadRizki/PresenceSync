<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncReceiverController extends Controller
{
    /**
     * Terima file SQL dari lokal dan import ke database hosting via PDO.
     * Tidak menggunakan exec() agar kompatibel dengan shared hosting.
     */
    public function receive(Request $request)
    {
        // 1. Verifikasi secret key
        $syncSecret = config('services.hosting_sync.secret', env('HOSTING_SYNC_SECRET', '80666dc99520035d6bb10d85eeee90e89f839dfbc51bbf3f'));
        $sentSecret = $request->header('X-Sync-Secret');

        if (!$syncSecret || $sentSecret !== $syncSecret) {
            Log::warning('DB Sync Receiver: Akses ditolak dari IP: ' . $request->ip());
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Secret key tidak valid.',
            ], 403);
        }

        // 2. Validasi file SQL diterima
        if (!$request->hasFile('sql_file')) {
            return response()->json([
                'success' => false,
                'message' => 'File SQL tidak ditemukan dalam request.',
            ], 422);
        }

        // 3. Simpan file SQL sementara
        $sqlFile  = $request->file('sql_file');
        $tempPath = storage_path('app/sync_received.sql');
        $sqlFile->move(storage_path('app'), 'sync_received.sql');

        if (!file_exists($tempPath) || filesize($tempPath) < 100) {
            return response()->json([
                'success' => false,
                'message' => 'File SQL yang diterima tidak valid atau kosong.',
            ], 422);
        }

        // 4. Baca isi SQL
        $sqlContent = file_get_contents($tempPath);
        @unlink($tempPath);

        if (!$sqlContent) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca isi file SQL.',
            ], 500);
        }

        // 5. Import SQL via PDO statement by statement
        try {
            $pdo = DB::connection()->getPdo();

            // Nonaktifkan foreign key checks sementara
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
            $pdo->exec('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";');

            // Pisahkan SQL menjadi statement-statement individual
            $statements = $this->splitSqlStatements($sqlContent);

            $errors = [];
            foreach ($statements as $statement) {
                $stmt = trim($statement);
                if (empty($stmt)) continue;

                try {
                    $pdo->exec($stmt);
                } catch (\Exception $e) {
                    // Log error tapi lanjutkan (jangan hentikan seluruh proses)
                    $errors[] = substr($e->getMessage(), 0, 100);
                }
            }

            // Aktifkan kembali foreign key checks
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');

            if (count($errors) > 5) {
                Log::warning('DB Sync Receiver: Import selesai dengan ' . count($errors) . ' error dari IP: ' . $request->ip());
                return response()->json([
                    'success' => false,
                    'message' => 'Import selesai tapi banyak error (' . count($errors) . '). Kemungkinan struktur tabel tidak kompatibel.',
                    'detail'  => implode("\n", array_slice($errors, 0, 5)),
                ], 500);
            }

            Log::info('DB Sync Receiver: Import berhasil dari IP: ' . $request->ip() . (count($errors) ? ' dengan ' . count($errors) . ' warning' : ''));

            return response()->json([
                'success' => true,
                'message' => 'Database berhasil disinkronkan ke hosting!' . (count($errors) ? ' (' . count($errors) . ' statement dilewati)' : ''),
            ]);

        } catch (\Exception $e) {
            Log::error('DB Sync Receiver: Exception - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal import database: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Pisahkan SQL dump menjadi array statement individual.
     * Menangani delimiter custom (DELIMITER ;;) untuk stored procedures.
     */
    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $delimiter  = ';';
        $lines      = explode("\n", $sql);
        $current    = '';

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Skip komentar (compatible with PHP 7.x)
            if (substr($trimmed, 0, 2) === '--' || substr($trimmed, 0, 1) === '#' || empty($trimmed)) {
                continue;
            }

            // Handle DELIMITER custom
            if (stripos($trimmed, 'DELIMITER') === 0) {
                $delimiter = trim(substr($trimmed, 9));
                continue;
            }

            $current .= $line . "\n";

            // Cek apakah statement sudah selesai (compatible with PHP 7.x)
            if (substr(rtrim($trimmed), -strlen($delimiter)) === $delimiter) {
                // Hapus delimiter di akhir jika bukan ';'
                if ($delimiter !== ';') {
                    $current = substr(rtrim($current), 0, -strlen($delimiter));
                }
                $statements[] = trim($current);
                $current = '';
            }
        }

        // Tambahkan sisa yang mungkin belum selesai
        if (!empty(trim($current))) {
            $statements[] = trim($current);
        }

        return $statements;
    }
}
