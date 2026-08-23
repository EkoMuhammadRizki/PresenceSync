<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajarans';

    protected $fillable = [
        'guru_id',
        'nama',
        'kode',
        'tingkat',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function jadwalPelajarans(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }
}
