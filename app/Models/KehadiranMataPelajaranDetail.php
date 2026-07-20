<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KehadiranMataPelajaranDetail extends Model
{
    protected $table = 'kehadiran_mata_pelajaran_details';

    protected $fillable = [
        'kehadiran_mata_pelajaran_id',
        'siswa_id',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function kehadiranMataPelajaran(): BelongsTo
    {
        return $this->belongsTo(KehadiranMataPelajaran::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
