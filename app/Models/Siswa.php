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
        'is_sekretaris',
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
        'is_enrolled',
        'is_pushed',
    ];

    protected static function booted()
    {
        static::saving(function ($siswa) {
            if ($siswa->user_id && empty($siswa->id)) {
                $siswa->id = $siswa->user_id;
            }
            if (empty($siswa->fingerprint_id) && $siswa->id) {
                $siswa->fingerprint_id = (string) $siswa->id;
            }
        });
    }

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_enrolled'   => 'boolean',
        'is_pushed'     => 'boolean',
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

    public function syncLogs(): HasMany
    {
        return $this->hasMany(FingerprintSyncLog::class, 'fingerprint_uid', 'fingerprint_id');
    }

    public function orangTua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'orang_tua_user_id');
    }

    public function kehadiranMataPelajarans(): HasMany
    {
        return $this->hasMany(KehadiranMataPelajaran::class, 'created_by');
    }

    public function kehadiranMataPelajaranDetails(): HasMany
    {
        return $this->hasMany(KehadiranMataPelajaranDetail::class);
    }
}
