<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncReceiverController extends Controller
{
    /**
     * Terima dataset dari lokal dan lakukan Smart Non-Destructive Merge (Upsert).
     * Data yang ada di hosting dan tidak ada di lokal TIDAK AKAN TERHAPUS.
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

        // 2. Cek apakah menerima payload Smart Merge (GZIP JSON)
        if ($request->hasFile('sync_payload')) {
            return $this->processSmartMerge($request->file('sync_payload'));
        }

        // Fallback: jika menerima format lama (.sql)
        if ($request->hasFile('sql_file')) {
            return $this->processLegacySql($request->file('sql_file'));
        }

        return response()->json([
            'success' => false,
            'message' => 'Payload sinkronisasi tidak ditemukan dalam request.',
        ], 422);
    }

    /**
     * Proses Smart Non-Destructive Merge dari GZIP JSON Payload.
     */
    private function processSmartMerge($file)
    {
        try {
            $rawContent = file_get_contents($file->getRealPath());
            $jsonString = @gzdecode($rawContent);

            if (!$jsonString) {
                // Coba jika tidak terkompresi
                $jsonString = $rawContent;
            }

            $payload = json_decode($jsonString, true);

            if (!isset($payload['data']) || !is_array($payload['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format payload data sinkronisasi tidak valid.',
                ], 422);
            }

            $data = $payload['data'];

            // Urutan tabel untuk menjaga relasi Foreign Key
            $orderedTables = [
                'settings'                         => ['id'],
                'semesters'                        => ['id'],
                'tahun_ajarans'                    => ['id'],
                'roles'                            => ['id'],
                'permissions'                      => ['id'],
                'kelas'                            => ['id'],
                'mata_pelajarans'                  => ['id'],
                'aturan_jams'                      => ['id'],
                'fingerprint_devices'              => ['id'],
                'users'                            => ['id'],
                'user_infos'                       => ['user_id'],
                'model_has_roles'                  => ['role_id', 'model_type', 'model_id'],
                'gurus'                            => ['id'],
                'siswas'                           => ['id'],
                'parent_profiles'                  => ['id'],
                'jadwal_pelajarans'                => ['id'],
                'fingerprint_sync_logs'            => ['id'],
                'kehadirans'                       => ['id'],
                'kehadiran_mata_pelajarans'        => ['id'],
                'kehadiran_mata_pelajaran_details' => ['id'],
                'pengaduans'                       => ['id'],
            ];

            $totalInserted = 0;
            $totalUpdated  = 0;
            $tableStats    = [];

            // Nonaktifkan Foreign Key Check sementara saat merge
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            foreach ($orderedTables as $table => $uniqueKeys) {
                if (!isset($data[$table]) || empty($data[$table])) {
                    continue;
                }

                $rows = $data[$table];
                $tableInserted = 0;
                $tableUpdated  = 0;

                // Ambil daftar existing ID dari database hosting untuk O(1) matching
                $existingMap = [];
                try {
                    $primaryKey = $uniqueKeys[0];
                    if (count($uniqueKeys) === 1) {
                        $existingList = DB::table($table)->pluck($primaryKey)->toArray();
                        $existingMap = array_flip($existingList);
                    }
                } catch (\Throwable $e) {}

                foreach ($rows as $row) {
                    try {
                        $isExisting = false;

                        if (count($uniqueKeys) === 1) {
                            $val = $row[$uniqueKeys[0]] ?? null;
                            $isExisting = ($val !== null && isset($existingMap[$val]));
                        } else {
                            $query = DB::table($table);
                            foreach ($uniqueKeys as $key) {
                                if (isset($row[$key])) {
                                    $query->where($key, $row[$key]);
                                }
                            }
                            $isExisting = $query->exists();
                        }

                        if ($isExisting) {
                            // Update data yang sudah ada
                            $matchQuery = DB::table($table);
                            foreach ($uniqueKeys as $key) {
                                if (isset($row[$key])) {
                                    $matchQuery->where($key, $row[$key]);
                                }
                            }
                            $matchQuery->update($row);
                            $tableUpdated++;
                        } else {
                            // Insert data baru (data hosting yang lain tetap ada & tidak dihapus!)
                            DB::table($table)->insert($row);
                            $tableInserted++;
                            if (count($uniqueKeys) === 1 && isset($row[$uniqueKeys[0]])) {
                                $existingMap[$row[$uniqueKeys[0]]] = true;
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::warning("Smart Merge row skip on table {$table}: " . $e->getMessage());
                    }
                }

                $totalInserted += $tableInserted;
                $totalUpdated  += $tableUpdated;
                $tableStats[$table] = [
                    'inserted' => $tableInserted,
                    'updated'  => $tableUpdated,
                ];
            }

            // Aktifkan kembali Foreign Key Check
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $summaryText = "{$totalInserted} data baru berhasil ditambahkan, {$totalUpdated} data sudah ada diperbarui.";
            Log::info("Smart Merge Complete: {$summaryText}");

            return response()->json([
                'success' => true,
                'message' => "Sinkronisasi Berhasil! {$summaryText}",
                'summary' => [
                    'new_inserted'     => $totalInserted,
                    'already_existing' => $totalUpdated,
                    'details'          => $summaryText,
                    'table_stats'      => $tableStats,
                ],
            ]);

        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Log::error('Smart Merge Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan sinkronisasi database: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fallback untuk backward compatibility jika menerima file .sql
     */
    private function processLegacySql($file)
    {
        $tempPath = storage_path('app/sync_received.sql');
        $file->move(storage_path('app'), 'sync_received.sql');

        if (!file_exists($tempPath) || filesize($tempPath) < 100) {
            return response()->json([
                'success' => false,
                'message' => 'File SQL yang diterima tidak valid atau kosong.',
            ], 422);
        }

        $sqlContent = file_get_contents($tempPath);
        @unlink($tempPath);

        try {
            $pdo = DB::connection()->getPdo();
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
            $pdo->exec('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";');

            $statements = $this->splitSqlStatements($sqlContent);
            foreach ($statements as $statement) {
                $stmt = trim($statement);
                if (empty($stmt)) continue;
                try {
                    $pdo->exec($stmt);
                } catch (\Exception $e) {}
            }

            $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json([
                'success' => true,
                'message' => 'Database berhasil disinkronkan ke hosting!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal import database: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Pisahkan SQL dump menjadi array statement individual.
     */
    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $delimiter  = ';';
        $lines      = explode("\n", $sql);
        $current    = '';

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (substr($trimmed, 0, 2) === '--' || substr($trimmed, 0, 1) === '#' || empty($trimmed)) {
                continue;
            }
            if (stripos($trimmed, 'DELIMITER') === 0) {
                $delimiter = trim(substr($trimmed, 9));
                continue;
            }
            $current .= $line . "\n";
            if (substr(rtrim($trimmed), -strlen($delimiter)) === $delimiter) {
                if ($delimiter !== ';') {
                    $current = substr(rtrim($current), 0, -strlen($delimiter));
                }
                $statements[] = trim($current);
                $current = '';
            }
        }
        if (!empty(trim($current))) {
            $statements[] = trim($current);
        }
        return $statements;
    }
}
