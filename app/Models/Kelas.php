<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'guru_id',
        'nama',
        'tingkat',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($kelas) {
            $kelas->siswas()->update(['kelas_id' => null]);
        });
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

    public function jadwalPelajarans(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    /**
     * Nama lengkap kelas, misal: "X RPL 1"
     */
    public function kehadiranMataPelajarans(): HasMany
    {
        return $this->hasMany(KehadiranMataPelajaran::class);
    }

    public function getNamaLengkapAttribute(): string
    {
        return $this->tingkat . ' ' . $this->nama;
    }
}
