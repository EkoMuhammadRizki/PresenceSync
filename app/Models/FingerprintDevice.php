<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FingerprintDevice extends Model
{
    protected $table = 'fingerprint_devices';

    protected $fillable = [
        'nama',
        'ip_address',
        'port',
        'com_key',
        'serial_number',
        'is_aktif',
        'last_synced_at',
        'total_synced_logs',
    ];

    protected $casts = [
        'is_aktif'       => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function syncLogs(): HasMany
    {
        return $this->hasMany(FingerprintSyncLog::class, 'fingerprint_device_id');
    }

    /**
     * Endpoint SOAP device (port 80, path /iWsService)
     */
    public function getSoapUrlAttribute(): string
    {
        return 'http://' . $this->ip_address . ':' . $this->port . '/iWsService';
    }

    /**
     * Cek apakah IP perangkat dapat dijangkau di jaringan
     */
    public function isConnected(): bool
    {
        if (!$this->is_aktif || empty($this->ip_address)) {
            return false;
        }

        $connect = @fsockopen($this->ip_address, $this->port ?? 80, $errno, $errstr, 1);
        if ($connect) {
            fclose($connect);
            return true;
        }

        return false;
    }

    /**
     * Status badge untuk tampilan UI
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->isConnected() ? 'badge-light-success' : 'badge-light-danger';
    }

    /**
     * Label status
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->isConnected() ? 'Terhubung' : 'Tidak terhubung';
    }
}
