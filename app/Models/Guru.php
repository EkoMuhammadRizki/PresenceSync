<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    protected $table = 'gurus';

    protected $fillable = [
        'user_id',
        'nama',
        'nip',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'alamat',
        'no_hp',
        'email',
        'status',
        'nik',
        'npwp',
        'nuptk',
        'status_kepegawaian',
        'tugas_tambahan',
        'sk_cpns',
        'tanggal_cpns',
        'sk_pengangkatan',
        'tmt_pengangkatan',
        'lembaga_pengangkatan',
        'pangkat_golongan',
    ];

    protected $casts = [
        'tanggal_lahir'    => 'date',
        'tanggal_cpns'     => 'date',
        'tmt_pengangkatan' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($guru) {
            $guru->kelas()->update(['guru_id' => null]);
            $guru->mataPelajarans()->update(['guru_id' => null]);
        });
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    public function mataPelajarans(): HasMany
    {
        return $this->hasMany(MataPelajaran::class);
    }
}
