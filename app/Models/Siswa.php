<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $table = 'siswas';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'kelas_id',
        'nama',
        'nisn',
        'nis',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'no_hp_orang_tua',
        'nama_orang_tua',
        'orang_tua_user_id',
        'status',
        'fingerprint_id',
    ];

    protected static function booted()
    {
        static::saving(function ($siswa) {
            if ($siswa->user_id) {
                $siswa->id = $siswa->user_id;
                $siswa->fingerprint_id = (string) $siswa->user_id;
            }
        });
    }

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

    public function orangTua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'orang_tua_user_id');
    }
}
