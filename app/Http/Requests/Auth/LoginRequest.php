<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'identifier' => 'required|string',
            'password'   => 'required|string',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * Priority: NIS (Siswa) → NIP (Guru) → Email (Admin/Kesiswaan fallback)
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = trim($this->input('identifier'));
        $user = null;

        // 1. Cari berdasarkan NIS siswa
        $siswa = \App\Models\Siswa::where('nis', $identifier)->first();
        if ($siswa && $siswa->user_id) {
            $user = \App\Models\User::find($siswa->user_id);
        }

        // 2. Cari berdasarkan NIP guru
        if (!$user) {
            $guru = \App\Models\Guru::where('nip', $identifier)->first();
            if ($guru && $guru->user_id) {
                $user = \App\Models\User::find($guru->user_id);
            }
        }

        // 3. Fallback: cari berdasarkan email (untuk Admin, Kesiswaan, dll.)
        if (!$user) {
            $user = \App\Models\User::where('email', $identifier)->first();
        }

        if (!$user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => ['NIS, NIP, atau Email yang Anda masukkan tidak terdaftar.'],
            ]);
        }

        if ($user->hasRole('orang_tua')) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => ['Akun tidak terdaftar atau tidak memiliki akses ke sistem.'],
            ]);
        }

        if (!\Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'password' => ['Password yang Anda masukkan salah.'],
            ]);
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('identifier')) . '|' . $this->ip();
    }
}

