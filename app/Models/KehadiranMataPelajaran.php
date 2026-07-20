<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KehadiranMataPelajaran extends Model
{
    protected $table = 'kehadiran_mata_pelajarans';

    protected $fillable = [
        'kelas_id',
        'semester_id',
        'mata_pelajaran_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'created_by',
        'is_guru_hadir',
        'ada_konfirmasi_guru',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(KehadiranMataPelajaranDetail::class);
    }
}
