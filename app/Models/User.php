<?php

namespace App\Models;

use App\Core\Traits\SpatieLogsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    use SpatieLogsActivity;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'api_token',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get a fullname combination of first_name and last_name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->info) {
            return $this->info->avatar_url;
        }

        return asset(theme()->getMediaUrlPath().'avatars/blank.png');
    }

    /**
     * User relation to info model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function info()
    {
        return $this->hasOne(UserInfo::class);
    }

    /**
     * User relation to Siswa model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'user_id');
    }

    /**
     * User relation to Guru model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function guru()
    {
        return $this->hasOne(Guru::class, 'user_id');
    }

    /**
     * User relation to Siswa model (as Orang Tua)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orangTuaSiswas()
    {
        return $this->hasMany(Siswa::class, 'orang_tua_user_id');
    }

    /**
     * Get dynamic user role
     *
     * @return string
     */
    public function getRoleAttribute()
    {
        if ($this->siswa()->exists()) {
            return 'Siswa';
        }
        if ($this->guru()->exists()) {
            if ($this->hasRole('kesiswaan')) {
                return 'Kesiswaan';
            }
            return 'Guru';
        }
        if ($this->hasRole('admin')) {
            return 'Admin';
        }
        if ($this->hasRole('kesiswaan')) {
            return 'Kesiswaan';
        }
        if ($this->hasRole('orang_tua')) {
            return 'Orang Tua';
        }

        // Fallback to Spatie roles if any
        $firstRole = $this->roles->first();
        if ($firstRole) {
            return ucwords(str_replace(['-', '_'], ' ', $firstRole->name));
        }

        return 'Admin'; // Default fallback
    }

    /**
     * Get username derived from email
     *
     * @return string
     */
    public function getUsernameAttribute()
    {
        if ($this->siswa) {
            return $this->siswa->nama;
        }
        if ($this->guru) {
            return $this->guru->nama;
        }
        return strtolower(\Illuminate\Support\Str::before($this->email, '@'));
    }
}

