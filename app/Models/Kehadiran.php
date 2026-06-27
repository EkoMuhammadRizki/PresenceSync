<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kehadiran extends Model
{
    protected $table = 'kehadirans';

    protected $fillable = [
        'siswa_id',
        'semester_id',
        'aturan_jam_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'keterangan',
        'foto',
        'koordinat',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function aturanJam(): BelongsTo
    {
        return $this->belongsTo(AturanJam::class);
    }

    /**
     * Badge warna berdasarkan status kehadiran
     */
    public function getBadgeClassAttribute(): string
    {
        return match($this->status) {
            'hadir'     => 'badge-light-success',
            'terlambat' => 'badge-light-warning',
            'sakit'     => 'badge-light-info',
            'izin'      => 'badge-light-primary',
            'alpha'     => 'badge-light-danger',
            default     => 'badge-light-secondary',
        };
    }
}
