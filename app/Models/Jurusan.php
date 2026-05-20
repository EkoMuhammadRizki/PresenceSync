<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    protected $table = 'jurusans';

    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
    ];

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }
}
