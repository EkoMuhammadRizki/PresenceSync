<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    protected $table = 'gurus';

    protected $fillable = [
        'nama',
        'nip',
        'email',
        'no_hp',
        'alamat',
    ];

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    public function mataPelajarans(): HasMany
    {
        return $this->hasMany(MataPelajaran::class);
    }
}
