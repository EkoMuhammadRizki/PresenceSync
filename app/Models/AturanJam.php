<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AturanJam extends Model
{
    protected $table = 'aturan_jams';

    protected $fillable = [
        'hari',
        'jam_masuk',
        'batas_awal_pulang',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function kehadirans(): HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }

    /**
     * Scope untuk hanya aturan jam yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
