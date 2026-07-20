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
        'pekerjaan_ayah',
        'ket_pekerjaan_ayah',
        'pendidikan_ayah',
        'alamat_ayah',
        'no_hp_ayah',
        'penghasilan_ayah',

        // Ibu
        'nik_ibu',
        'nama_ibu',
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
}
