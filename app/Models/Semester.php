<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    protected $table = 'semesters';

    protected $fillable = [
        'tahun_ajaran_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function jadwalPelajarans(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function kehadirans(): HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }

    /**
     * Label tampilan gabungan, misal: "2025/2026 - Ganjil"
     */
    public function getLabelAttribute(): string
    {
        return $this->tahunAjaran->nama . ' - ' . ucfirst($this->jenis);
    }
}
