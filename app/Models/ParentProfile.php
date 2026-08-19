<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentProfile extends Model
{
    protected $table = 'parent_profiles';

    protected $fillable = [
        'parent_user_id',
        
        // Ayah
        'nik_ayah',
        'nama_ayah',
        'tahun_lahir_ayah',
        'pekerjaan_ayah',
        'ket_pekerjaan_ayah',
        'pendidikan_ayah',
        'alamat_ayah',
        'no_hp_ayah',
        'penghasilan_ayah',

        // Ibu
        'nik_ibu',
        'nama_ibu',
        'tahun_lahir_ibu',
        'pekerjaan_ibu',
        'ket_pekerjaan_ibu',
        'pendidikan_ibu',
        'alamat_ibu',
        'no_hp_ibu',
        'penghasilan_ibu',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function getPenghasilanAyahFormattedAttribute(): string
    {
        if ($this->penghasilan_ayah === null || trim((string)$this->penghasilan_ayah) === '') {
            return '';
        }
        $clean = trim((string)$this->penghasilan_ayah);
        if (str_starts_with(strtoupper($clean), 'RP')) {
            return $clean;
        }
        $digits = preg_replace('/[^0-9]/', '', $clean);
        if ($digits !== '') {
            return 'Rp ' . number_format((float)$digits, 0, ',', '.');
        }
        return $clean;
    }

    public function getPenghasilanIbuFormattedAttribute(): string
    {
        if ($this->penghasilan_ibu === null || trim((string)$this->penghasilan_ibu) === '') {
            return '';
        }
        $clean = trim((string)$this->penghasilan_ibu);
        if (str_starts_with(strtoupper($clean), 'RP')) {
            return $clean;
        }
        $digits = preg_replace('/[^0-9]/', '', $clean);
        if ($digits !== '') {
            return 'Rp ' . number_format((float)$digits, 0, ',', '.');
        }
        return $clean;
    }
}
