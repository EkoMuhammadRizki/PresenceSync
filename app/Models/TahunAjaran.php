<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajarans';

    protected $fillable = [
        'nama',
        'bulan_mulai',
        'bulan_selesai',
        'status',
    ];

    protected $casts = [
        'bulan_mulai'   => 'date',
        'bulan_selesai' => 'date',
    ];

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    public function semesterAktif(): HasMany
    {
        return $this->hasMany(Semester::class)->where('status', 'aktif');
    }
}
