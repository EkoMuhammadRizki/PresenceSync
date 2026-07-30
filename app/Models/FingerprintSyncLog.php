<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FingerprintSyncLog extends Model
{
    protected $table = 'fingerprint_sync_logs';

    protected $fillable = [
        'fingerprint_device_id',
        'fingerprint_uid',
        'scan_time',
        'verified',
        'status',
        'is_processed',
        'kehadiran_id',
        'error_note',
    ];

    protected $casts = [
        'scan_time'    => 'datetime',
        'is_processed' => 'boolean',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(FingerprintDevice::class, 'fingerprint_device_id');
    }

    public function kehadiran(): BelongsTo
    {
        return $this->belongsTo(Kehadiran::class);
    }

    /**
     * Cari siswa yang memiliki fingerprint_id cocok dengan fingerprint_uid ini
     */
    public function siswa(): ?Siswa
    {
        return Siswa::where('fingerprint_id', $this->fingerprint_uid)->first();
    }
}
