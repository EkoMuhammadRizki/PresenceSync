<?php

namespace App\Console\Commands;

use App\Models\FingerprintDevice;
use App\Services\FingerprintService;
use Illuminate\Console\Command;

class SyncFingerprintAttendance extends Command
{
    protected $signature = 'fingerprint:sync
                            {--device= : ID device tertentu (opsional, default semua device aktif)}
                            {--clear   : Hapus log di device setelah sync berhasil}';

    protected $description = 'Sync log absensi dari semua device fingerprint aktif ke database';

    public function __construct(protected FingerprintService $service)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $deviceId = $this->option('device');
        $clearAfterSync = $this->option('clear');

        $query = FingerprintDevice::where('is_aktif', true);
        if ($deviceId) {
            $query->where('id', $deviceId);
        }

        $devices = $query->get();

        if ($devices->isEmpty()) {
            $this->warn('Tidak ada device fingerprint aktif yang ditemukan.');
            return self::SUCCESS;
        }

        $this->info("Mulai sync " . $devices->count() . " device fingerprint...");
        $this->newLine();

        $totalProcessed = 0;
        $totalErrors    = 0;

        foreach ($devices as $device) {
            $this->info("📡 [{$device->nama}] {$device->ip_address}:{$device->port}");

            $stats = $this->service->syncAndProcess($device, $clearAfterSync);

            if (!empty($stats['error_message'])) {
                $this->error("   ❌ Gagal: {$stats['error_message']}");
                $totalErrors++;
                continue;
            }

            $this->line("   ✅ Diambil: {$stats['fetched']} | Baru: {$stats['new']} | Diproses: {$stats['processed']} | Skip: {$stats['skipped']} | Error: {$stats['errors']}");

            $totalProcessed += $stats['processed'];
            $totalErrors    += $stats['errors'];
        }

        $this->newLine();
        $this->info("Selesai. Total diproses: {$totalProcessed} | Total error: {$totalErrors}");

        return $totalErrors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
