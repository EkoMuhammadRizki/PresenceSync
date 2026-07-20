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
        'email',
        'no_hp',
        'alamat',
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
