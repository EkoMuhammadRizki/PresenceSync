<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $table = 'siswas';

    protected $fillable = [
        'user_id',
        'kelas_id',
        'nama',
        'nisn',
        'nis',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'fingerprint_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function kehadirans(): HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }
}
