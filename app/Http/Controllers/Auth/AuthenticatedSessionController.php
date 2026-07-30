<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $lastIdentifier = $request->cookie('last_login_identifier', session('last_login_identifier', ''));
        $lastPassword   = $request->cookie('last_login_password', session('last_login_password', 'demo'));
        return view('auth.login', compact('lastIdentifier', 'lastPassword'));
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Hapus penanda modal panduan singkat agar selalu muncul setelah login baru
        $request->session()->forget('panduan_singkat_shown');

        $user = auth()->user();
        $identifier = $request->input('identifier');
        $password   = $request->input('password', 'demo');

        // Clear any previous intended URL to prevent wrong dashboard redirects
        $request->session()->forget('url.intended');

        // Tentukan target redirect berdasarkan role
        $redirectUrl = RouteServiceProvider::HOME;
        if (\App\Models\Siswa::where('user_id', $user->id)->exists()) {
            $redirectUrl = '/absensi/siswa/dashboard';
        } elseif ($user->hasRole('kesiswaan')) {
            $redirectUrl = '/absensi/kesiswaan/dashboard';
        } elseif (\App\Models\Guru::where('user_id', $user->id)->exists()) {
            $redirectUrl = '/absensi/guru/dashboard';
        }

        $cookieId   = cookie('last_login_identifier', $identifier, 60 * 24 * 30);
        $cookiePass = cookie('last_login_password', $password, 60 * 24 * 30);

        if ($request->wantsJson()) {
            return response()->json([
                'redirectUrl' => $redirectUrl,
                'redirect'    => $redirectUrl,
                'message'     => 'Berhasil Masuk!'
            ])->withCookie($cookieId)->withCookie($cookiePass);
        }

        return redirect($redirectUrl)->withCookie($cookieId)->withCookie($cookiePass);
    }

    /**
     * Handle an incoming api authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function apiStore(LoginRequest $request)
    {
        $request->authenticate();
        $user = auth()->user();
        return response($user);
    }

    /**
     * Verifies user token.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function apiVerifyToken(Request $request)
    {
        $request->validate([
            'api_token' => 'required'
        ]);

        $user = User::where('api_token', $request->api_token)->first();

        if(!$user){
            throw ValidationException::withMessages([
                'token' => ['Invalid token']
            ]);
        }
        return response($user);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $user = auth()->user();
        $lastIdentifier = '';

        if ($user) {
            $siswa = \App\Models\Siswa::where('user_id', $user->id)->first();
            $guru  = \App\Models\Guru::where('user_id', $user->id)->first();

            if ($siswa) {
                $lastIdentifier = $siswa->nis;
            } elseif ($guru) {
                $lastIdentifier = $guru->nip;
            } else {
                $lastIdentifier = $user->email;
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $cookieId   = cookie('last_login_identifier', $lastIdentifier, 60 * 24 * 30);
        $cookiePass = cookie('last_login_password', 'demo', 60 * 24 * 30);

        return redirect('/login')
            ->withCookie($cookieId)
            ->withCookie($cookiePass)
            ->with('last_login_identifier', $lastIdentifier)
            ->with('last_login_password', 'demo');
    }
}
