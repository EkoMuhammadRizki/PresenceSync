<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajarans';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'semester_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
