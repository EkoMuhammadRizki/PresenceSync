<?php

namespace App\Services;

use App\Models\AturanJam;
use App\Models\FingerprintDevice;
use App\Models\FingerprintSyncLog;
use App\Models\Kehadiran;
use App\Models\Semester;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * FingerprintService
 *
 * Mengimplementasikan komunikasi SOAP/HTTP ke mesin fingerprint Solution X100-C
 * menggunakan protokol resmi dari SDK bawaan device (port 80, endpoint /iWsService).
 *
 * Referensi SDK: SDK X100C Solution/PHP-soap-baru/
 */
class FingerprintService
{
    /**
     * Timeout koneksi ke device (detik) - Dipercepat agar tidak memblokir web request
     */
    private int $timeout = 1;

    // =========================================================================
    // CORE: Komunikasi SOAP ke Device
    // =========================================================================

    /**
     * Kirim raw SOAP request ke device dan kembalikan response buffer string.
     * Sesuai cara SDK resmi: fsockopen → POST /iWsService → baca response.
     */
    public function sendSoapRequest(FingerprintDevice $device, string $xmlBody): array
    {
        $connect = @fsockopen($device->ip_address, $device->port, $errno, $errstr, $this->timeout);

        if (!$connect) {
            $reason = "Perangkat tidak merespons atau tidak terjangkau di jaringan LAN dari komputer ini. Silakan periksa IP mesin di menu Comm Opt mesin fisik atau pastikan IP di sistem sesuai.";
            if (!empty($errstr) && (str_contains(strtolower($errstr), 'refused') || $errno == 10061)) {
                $reason = "Koneksi ditolak oleh perangkat di IP {$device->ip_address}:{$device->port}. Pastikan port SOAP/HTTP (80) pada mesin aktif.";
            }
            return [
                'success' => false,
                'error'   => "Gagal terhubung ke {$device->ip_address}:{$device->port}. {$reason}",
                'buffer'  => '',
            ];
        }

        // Batasi waktu baca/tulis agar fgets tidak hang jika device lambat merespons
        stream_set_timeout($connect, $this->timeout);

        $newLine = "\r\n";
        fwrite($connect, "POST /iWsService HTTP/1.0" . $newLine);
        fwrite($connect, "Content-Type: text/xml" . $newLine);
        fwrite($connect, "Content-Length: " . strlen($xmlBody) . $newLine . $newLine);
        fwrite($connect, $xmlBody . $newLine);

        $buffer = '';
        while ($response = fgets($connect, 4096)) {
            // Cek apakah stream sudah timeout
            $meta = stream_get_meta_data($connect);
            if ($meta['timed_out']) {
                break;
            }
            $buffer .= $response;
        }
        fclose($connect);

        return [
            'success' => true,
            'buffer'  => $buffer,
            'error'   => '',
        ];
    }

    /**
     * Utility: ekstrak konten antara dua tag XML (sesuai parse.php dari SDK resmi)
     */
    public function parseXml(string $data, string $openTag, string $closeTag): string
    {
        $data   = ' ' . $data;
        $result = '';
        $start  = strpos($data, $openTag);

        if ($start !== false) {
            $sub  = substr($data, $start);
            $end  = strpos($sub, $closeTag);
            if ($end !== false) {
                $result = substr($sub, strlen($openTag), $end - strlen($openTag));
            }
        }

        return trim($result);
    }

    // =========================================================================
    // FITUR 1: Tarik Log Absensi (GetAttLog)
    // =========================================================================

    /**
     * Ambil semua log absensi dari device.
     * Mengembalikan array of ['PIN', 'DateTime', 'Verified', 'Status']
     */
    public function getAttendanceLogs(FingerprintDevice $device): array
    {
        $xml = "<GetAttLog>"
            . "<ArgComKey xsi:type=\"xsd:integer\">{$device->com_key}</ArgComKey>"
            . "<Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg>"
            . "</GetAttLog>";

        $response = $this->sendSoapRequest($device, $xml);

        if (!$response['success']) {
            return ['success' => false, 'error' => $response['error'], 'logs' => []];
        }

        $rawLogs = $this->parseXml($response['buffer'], '<GetAttLogResponse>', '</GetAttLogResponse>');
        $rows    = explode("\r\n", $rawLogs);
        $logs    = [];

        foreach ($rows as $row) {
            $data = $this->parseXml($row, '<Row>', '</Row>');
            if (empty($data)) continue;

            $logs[] = [
                'PIN'      => $this->parseXml($data, '<PIN>', '</PIN>'),
                'DateTime' => $this->parseXml($data, '<DateTime>', '</DateTime>'),
                'Verified' => $this->parseXml($data, '<Verified>', '</Verified>'),
                'Status'   => $this->parseXml($data, '<Status>', '</Status>'),
            ];
        }

        return ['success' => true, 'error' => '', 'logs' => $logs];
    }

    // =========================================================================
    // FITUR 2: Hapus Log Absensi di Device (ClearData)
    // =========================================================================

    /**
     * Hapus semua log absensi dari device setelah berhasil di-sync.
     * Value=3 sesuai spesifikasi SDK (clear attendance log).
     */
    public function clearAttendanceLogs(FingerprintDevice $device): array
    {
        $xml = "<ClearData>"
            . "<ArgComKey xsi:type=\"xsd:integer\">{$device->com_key}</ArgComKey>"
            . "<Arg><Value xsi:type=\"xsd:integer\">3</Value></Arg>"
            . "</ClearData>";

        $response = $this->sendSoapRequest($device, $xml);

        if (!$response['success']) {
            return ['success' => false, 'error' => $response['error']];
        }

        $info = $this->parseXml($response['buffer'], '<Information>', '</Information>');

        return ['success' => true, 'info' => $info];
    }

    // =========================================================================
    // FITUR 3: Upload Nama User ke Device (SetUserInfo)
    // =========================================================================

    /**
     * Upload nama siswa ke device agar ditampilkan di layar device saat scan.
     * PIN = fingerprint_id siswa, Name = nama siswa.
     */
    public function uploadUserName(FingerprintDevice $device, string $pin, string $name): array
    {
        $xml = "<SetUserInfo>"
            . "<ArgComKey Xsi:type=\"xsd:integer\">{$device->com_key}</ArgComKey>"
            . "<Arg><PIN>{$pin}</PIN><Name>{$name}</Name></Arg>"
            . "</SetUserInfo>";

        $response = $this->sendSoapRequest($device, $xml);

        if (!$response['success']) {
            return ['success' => false, 'error' => $response['error']];
        }

        $info = $this->parseXml($response['buffer'], '<Information>', '</Information>');

        return ['success' => true, 'info' => $info];
    }

    // =========================================================================
    // FITUR 4: Sinkronisasi Waktu Device (SetDate)
    // =========================================================================

    /**
     * Sinkronkan jam device dengan waktu server sekarang.
     */
    public function syncTime(FingerprintDevice $device): array
    {
        $now = Carbon::now();
        $xml = "<SetDate>"
            . "<ArgComKey xsi:type=\"xsd:integer\">{$device->com_key}</ArgComKey>"
            . "<Arg>"
            . "<Date xsi:type=\"xsd:string\">{$now->format('Y-m-d')}</Date>"
            . "<Time xsi:type=\"xsd:string\">{$now->format('H:i:s')}</Time>"
            . "</Arg>"
            . "</SetDate>";

        $response = $this->sendSoapRequest($device, $xml);

        if (!$response['success']) {
            return ['success' => false, 'error' => $response['error']];
        }

        $info = $this->parseXml($response['buffer'], '<Information>', '</Information>');

        return ['success' => true, 'info' => $info];
    }

    // =========================================================================
    // FITUR 5: Download Template Sidik Jari (GetUserTemplate)
    // =========================================================================

    /**
     * Download template sidik jari user dari device.
     * FingerID: 0-9 (indeks jari, 0=jari telunjuk kanan dst)
     */
    public function downloadFingerprintTemplate(FingerprintDevice $device, string $pin, int $fingerIndex = 0): array
    {
        $xml = "<GetUserTemplate>"
            . "<ArgComKey xsi:type=\"xsd:integer\">{$device->com_key}</ArgComKey>"
            . "<Arg>"
            . "<PIN xsi:type=\"xsd:integer\">{$pin}</PIN>"
            . "<FingerID xsi:type=\"xsd:integer\">{$fingerIndex}</FingerID>"
            . "</Arg>"
            . "</GetUserTemplate>";

        $response = $this->sendSoapRequest($device, $xml);

        if (!$response['success']) {
            return ['success' => false, 'error' => $response['error'], 'template' => null];
        }

        $rawData = $this->parseXml($response['buffer'], '<GetUserTemplateResponse>', '</GetUserTemplateResponse>');
        $rows    = explode("\r\n", $rawData);
        $templates = [];

        foreach ($rows as $row) {
            $data = $this->parseXml($row, '<Row>', '</Row>');
            if (empty($data)) continue;

            $templates[] = [
                'PIN'      => $this->parseXml($data, '<PIN>', '</PIN>'),
                'FingerID' => $this->parseXml($data, '<FingerID>', '</FingerID>'),
                'Size'     => $this->parseXml($data, '<Size>', '</Size>'),
                'Valid'    => $this->parseXml($data, '<Valid>', '</Valid>'),
                'Template' => $this->parseXml($data, '<Template>', '</Template>'),
            ];
        }

        return ['success' => true, 'error' => '', 'templates' => $templates];
    }

    // =========================================================================
    // FITUR 6: Upload Template Sidik Jari (SetUserTemplate + RefreshDB)
    // =========================================================================

    /**
     * Upload template sidik jari ke device dan refresh DB device.
     */
    public function uploadFingerprintTemplate(FingerprintDevice $device, string $pin, int $fingerIndex, string $template): array
    {
        $xml = "<SetUserTemplate>"
            . "<ArgComKey xsi:type=\"xsd:integer\">{$device->com_key}</ArgComKey>"
            . "<Arg>"
            . "<PIN xsi:type=\"xsd:integer\">{$pin}</PIN>"
            . "<FingerID xsi:type=\"xsd:integer\">{$fingerIndex}</FingerID>"
            . "<Size>" . strlen($template) . "</Size>"
            . "<Valid>1</Valid>"
            . "<Template>{$template}</Template>"
            . "</Arg>"
            . "</SetUserTemplate>";

        $response = $this->sendSoapRequest($device, $xml);

        if (!$response['success']) {
            return ['success' => false, 'error' => $response['error']];
        }

        $info = $this->parseXml($response['buffer'], '<Information>', '</Information>');

        // RefreshDB setelah upload agar device reload data
        $this->refreshDB($device);

        return ['success' => true, 'info' => $info];
    }

    /**
     * Refresh database device (dipanggil setelah upload template sidik jari)
     */
    public function refreshDB(FingerprintDevice $device): void
    {
        $xml = "<RefreshDB>"
            . "<ArgComKey xsi:type=\"xsd:integer\">{$device->com_key}</ArgComKey>"
            . "</RefreshDB>";

        $this->sendSoapRequest($device, $xml);
    }

    // =========================================================================
    // FITUR 7: Hapus User dari Device (DeleteUser)
    // =========================================================================

    /**
     * Hapus user dari device berdasarkan PIN (fingerprint_id siswa).
     */
    public function deleteUser(FingerprintDevice $device, string $pin): array
    {
        $xml = "<DeleteUser>"
            . "<ArgComKey xsi:type=\"xsd:integer\">{$device->com_key}</ArgComKey>"
            . "<Arg><PIN xsi:type=\"xsd:integer\">{$pin}</PIN></Arg>"
            . "</DeleteUser>";

        $response = $this->sendSoapRequest($device, $xml);

        if (!$response['success']) {
            return ['success' => false, 'error' => $response['error']];
        }

        $raw  = $this->parseXml($response['buffer'], '<DeleteUserResponse>', '</DeleteUserResponse>');
        $info = $this->parseXml($raw, '<Information>', '</Information>');

        return ['success' => true, 'info' => $info];
    }

    // =========================================================================
    // CORE LOGIC: Test Koneksi Device
    // =========================================================================

    /**
     * Tes koneksi ke device. Gunakan GetAttLog dengan timeout singkat.
     * Return: ['connected' => bool, 'latency_ms' => int, 'error' => string]
     */
    public function testConnection(FingerprintDevice $device): array
    {
        $start = microtime(true);

        $connect = @fsockopen($device->ip_address, $device->port, $errno, $errstr, 3);

        if (!$connect) {
            $reason = "Perangkat tidak merespons atau tidak terjangkau di jaringan LAN dari komputer ini. Silakan periksa IP mesin di menu Comm Opt mesin fisik atau pastikan IP di sistem sesuai.";
            if (!empty($errstr) && (str_contains(strtolower($errstr), 'refused') || $errno == 10061)) {
                $reason = "Koneksi ditolak oleh perangkat di IP {$device->ip_address}:{$device->port}. Pastikan port SOAP/HTTP (80) pada mesin aktif.";
            }
            return [
                'connected'  => false,
                'latency_ms' => 0,
                'error'      => "Gagal terhubung ke {$device->ip_address}:{$device->port}. {$reason}",
            ];
        }

        fclose($connect);

        $latency = (int) round((microtime(true) - $start) * 1000);

        return [
            'connected'  => true,
            'latency_ms' => $latency,
            'error'      => '',
        ];
    }

    // =========================================================================
    // CORE LOGIC: Sync + Proses Log ke Kehadiran
    // =========================================================================

    /**
     * Sync penuh: tarik log dari device → simpan ke fingerprint_sync_logs → proses jadi kehadiran (Batch High-Performance).
     * Return: array stats ['fetched', 'new', 'processed', 'skipped', 'errors']
     */
    public function syncAndProcess(FingerprintDevice $device, bool $clearAfterSync = false): array
    {
        $stats = ['fetched' => 0, 'new' => 0, 'processed' => 0, 'skipped' => 0, 'errors' => 0];

        // 1. Tarik log dari device
        $result = $this->getAttendanceLogs($device);
        if (!$result['success']) {
            return array_merge($stats, ['error_message' => $result['error']]);
        }

        $logs = $result['logs'];
        $stats['fetched'] = count($logs);

        if (empty($logs)) {
            return array_merge($stats, ['error_message' => '']);
        }

        // 2. Parse semua log di memori
        $parsedLogs = [];
        $pins = [];

        foreach ($logs as $log) {
            if (empty($log['PIN']) || empty($log['DateTime'])) continue;

            try {
                $scanTime = Carbon::createFromFormat('Y-m-d H:i:s', $log['DateTime']);
            } catch (\Throwable $e) {
                try {
                    $scanTime = Carbon::parse($log['DateTime']);
                } catch (\Throwable $e2) {
                    $stats['errors']++;
                    continue;
                }
            }

            $pin = (string) $log['PIN'];
            $parsedLogs[] = [
                'pin'          => $pin,
                'scan_time'    => $scanTime,
                'scan_date'    => $scanTime->toDateString(),
                'scan_his'     => $scanTime->format('H:i:s'),
                'hari'         => strtolower($scanTime->locale('id')->isoFormat('dddd')),
                'verified'     => (int) ($log['Verified'] ?? 1),
                'status'       => (int) ($log['Status'] ?? 0),
            ];
            $pins[] = $pin;
        }

        if (empty($parsedLogs)) {
            return array_merge($stats, ['error_message' => '']);
        }

        $uniquePins = array_unique($pins);

        // 3. Preload master data dalam 1 batch (Case-Insensitive Hari Lookup)
        $semester = Semester::where('status', 'aktif')->first();
        $aturanJams = AturanJam::where('is_aktif', true)->get()->keyBy(fn($a) => strtolower(trim($a->hari)));

        $siswas = Siswa::whereIn('fingerprint_id', $uniquePins)
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

        if (!empty($unenrolledSiswaIds)) {
            Siswa::whereIn('id', array_unique($unenrolledSiswaIds))->update(['is_enrolled' => true]);
        }

        // Preload existing logs
        $minTime = min(array_column($parsedLogs, 'scan_time'));
        $maxTime = max(array_column($parsedLogs, 'scan_time'));

        $existingSyncLogs = FingerprintSyncLog::where('fingerprint_device_id', $device->id)
            ->whereIn('fingerprint_uid', $uniquePins)
            ->whereBetween('scan_time', [$minTime, $maxTime])
            ->get()
            ->keyBy(fn($item) => $item->fingerprint_uid . '_' . $item->scan_time->toDateTimeString());

        // Preload existing kehadirans
        $dates = array_unique(array_column($parsedLogs, 'scan_date'));
        $siswaIds = $siswas->pluck('id')->toArray();
        $existingKehadirans = Kehadiran::whereIn('siswa_id', $siswaIds)
            ->whereIn('tanggal', $dates)
            ->get()
            ->keyBy(fn($k) => $k->siswa_id . '_' . ($k->tanggal instanceof \Carbon\Carbon ? $k->tanggal->format('Y-m-d') : substr((string)$k->tanggal, 0, 10)));

        DB::beginTransaction();
        try {
            foreach ($parsedLogs as $logItem) {
                $pin = $logItem['pin'];
                $scanTimeStr = $logItem['scan_time']->toDateTimeString();
                $logKey = $pin . '_' . $scanTimeStr;

                $syncLog = $existingSyncLogs->get($logKey);

                if (!$syncLog) {
                    $syncLog = new FingerprintSyncLog([
                        'fingerprint_device_id' => $device->id,
                        'fingerprint_uid'       => $pin,
                        'scan_time'             => $logItem['scan_time'],
                        'verified'              => $logItem['verified'],
                        'status'                => $logItem['status'],
                        'is_processed'          => false,
                    ]);
                    $syncLog->save();
                    $existingSyncLogs->put($logKey, $syncLog);
                    $stats['new']++;
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
                    $stats['skipped']++;
                    continue;
                }

                if (!$siswa->kelas_id) {
                    $syncLog->update([
                        'is_processed' => true,
                        'error_note'   => "Siswa belum memiliki kelas",
                    ]);
                    $stats['skipped']++;
                    continue;
                }

                if (!$semester) {
                    $syncLog->update([
                        'is_processed' => true,
                        'error_note'   => "Tidak ada semester aktif",
                    ]);
                    $stats['skipped']++;
                    continue;
                }

                $scanDate = $logItem['scan_date'];
                $scanTimeHis = $logItem['scan_his'];
                $hari = strtolower(trim($logItem['hari']));
                $aturanJam = $aturanJams->get($hari);

                $status = 'hadir';
                if ($aturanJam && !empty($aturanJam->jam_masuk)) {
                    $jamMasukAturan = strlen($aturanJam->jam_masuk) === 5 ? ($aturanJam->jam_masuk . ':00') : $aturanJam->jam_masuk;
                    $jamScan = strlen($scanTimeHis) === 5 ? ($scanTimeHis . ':00') : $scanTimeHis;
                    if ($jamScan > $jamMasukAturan) {
                        $status = 'terlambat';
                    }
                }

                $kehadiranKey = $siswa->id . '_' . $scanDate;
                $kehadiran = $existingKehadirans->get($kehadiranKey);

                if (!$kehadiran) {
                    $kehadiran = Kehadiran::create([
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
                    $stats['processed']++;
                } else {
                    if ($scanTimeHis > $kehadiran->jam_masuk) {
                        $batasAwalPulang = $aturanJam ? ($aturanJam->batas_awal_pulang ?? 0) : 0;
                        try {
                            $jamMasukLog = Carbon::parse($kehadiran->jam_masuk);
                            $jamScan = Carbon::parse($scanTimeHis);
                            $jamBatasPulang = $jamMasukLog->copy()->addMinutes($batasAwalPulang);

                            if ($jamScan->gte($jamBatasPulang)) {
                                $kehadiran->update(['jam_pulang' => $scanTimeHis]);
                            }
                        } catch (\Throwable $te) {
                            $kehadiran->update(['jam_pulang' => $scanTimeHis]);
                        }
                    }
                    $stats['processed']++;
                }

                $syncLog->update([
                    'is_processed' => true,
                    'kehadiran_id' => $kehadiran->id,
                    'error_note'   => null,
                ]);
            }

            $device->update([
                'last_synced_at'    => now(),
                'total_synced_logs' => $device->total_synced_logs + $stats['new'],
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("SOAP Batch Ingestion Error: " . $e->getMessage());
            $stats['errors']++;
        }

        // 5. Opsional: hapus log di device setelah sync berhasil
        if ($clearAfterSync && $stats['new'] > 0) {
            $this->clearAttendanceLogs($device);
        }

        return array_merge($stats, ['error_message' => '']);
    }

    /**
     * Proses satu FingerprintSyncLog menjadi record Kehadiran.
     * Return: 'processed' | 'skipped' | 'error'
     */
    public function processSyncLog(FingerprintSyncLog $syncLog): string
    {
        try {
            // Cari siswa berdasarkan fingerprint_id
            $siswa = Siswa::where('fingerprint_id', $syncLog->fingerprint_uid)
                ->orWhere('id', $syncLog->fingerprint_uid)
                ->first();

            if (!$siswa) {
                $syncLog->update([
                    'is_processed' => true,
                    'error_note'   => "Siswa dengan fingerprint_id={$syncLog->fingerprint_uid} tidak ditemukan",
                ]);
                return 'skipped';
            }

            // Mark student as enrolled when hardware scan log is detected
            if (!$siswa->is_enrolled) {
                $siswa->update(['is_enrolled' => true]);
            }

            $scanDate = $syncLog->scan_time->toDateString();
            $scanTime = $syncLog->scan_time->format('H:i:s');

            // Cari semester aktif yang mencakup tanggal scan
            $semester = Semester::where('status', 'aktif')->first();

            if (!$siswa->kelas_id) {
                $syncLog->update(['is_processed' => true, 'error_note' => 'Siswa belum memiliki kelas']);
                return 'skipped';
            }

            if (!$semester) {
                $syncLog->update(['is_processed' => true, 'error_note' => 'Tidak ada semester aktif']);
                return 'skipped';
            }

            // Cari aturan jam berdasarkan hari scan (Case-Insensitive)
            $hariScan = strtolower(trim($syncLog->scan_time->locale('id')->isoFormat('dddd')));
            $aturanJam = AturanJam::where('is_aktif', true)
                ->whereRaw('LOWER(TRIM(hari)) = ?', [$hariScan])
                ->first();

            // Tentukan status: hadir atau terlambat
            $status = 'hadir';
            if ($aturanJam && !empty($aturanJam->jam_masuk)) {
                $jamMasukAturan = strlen($aturanJam->jam_masuk) === 5 ? ($aturanJam->jam_masuk . ':00') : $aturanJam->jam_masuk;
                $jamScan        = strlen($scanTime) === 5 ? ($scanTime . ':00') : $scanTime;

                if ($jamScan > $jamMasukAturan) {
                    $status = 'terlambat';
                }
            }

            // Buat atau update kehadiran (firstOrCreate berdasarkan siswa + tanggal)
            $kehadiran = Kehadiran::firstOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'tanggal'  => $scanDate,
                ],
                [
                    'semester_id'       => $semester->id,
                    'aturan_jam_id'     => $aturanJam?->id,
                    'jam_masuk'         => $scanTime,
                    'status'            => $status,
                    'source'            => 'fingerprint',
                    'fingerprint_log_id' => $syncLog->id,
                ]
            );

            // Jika absensi hari ini baru dibuat (scan presensi masuk)
            if ($kehadiran->wasRecentlyCreated) {
                if ($siswa->user) {
                    activity()->causedBy($siswa->user)->log("Melakukan presensi masuk (fingerprint)");
                } else {
                    activity()->log("Melakukan presensi masuk (fingerprint): {$siswa->nama}");
                }
            } elseif ($scanTime > $kehadiran->jam_masuk) {
                $batasAwalPulang = $aturanJam ? ($aturanJam->batas_awal_pulang ?? 0) : 0;
                $jamMasukLog = Carbon::createFromFormat('H:i:s', $kehadiran->jam_masuk);
                $jamScan = Carbon::createFromFormat('H:i:s', $scanTime);
                $jamBatasPulang = $jamMasukLog->copy()->addMinutes($batasAwalPulang);

                if ($jamScan->gte($jamBatasPulang)) {
                    $kehadiran->update(['jam_pulang' => $scanTime]);
                }
            }

            $syncLog->update([
                'is_processed' => true,
                'kehadiran_id' => $kehadiran->id,
                'error_note'   => null,
            ]);

            return 'processed';
        } catch (\Throwable $e) {
            Log::error('FingerprintService::processSyncLog error', [
                'sync_log_id' => $syncLog->id,
                'error'       => $e->getMessage(),
            ]);

            $syncLog->update([
                'error_note' => substr($e->getMessage(), 0, 255),
            ]);

            return 'error';
        }
    }
}
