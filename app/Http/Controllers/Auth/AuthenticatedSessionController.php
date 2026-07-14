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
    public function create()
    {
        return view('auth.login');
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

        // Save email and password in cookies for login form population on logout
        cookie()->queue('logged_in_email', $request->email, 60 * 24 * 30);
        cookie()->queue('logged_in_password', $request->password, 60 * 24 * 30);

        // Tentukan target redirect berdasarkan role
        $redirectUrl = RouteServiceProvider::HOME;
        if (\App\Models\Siswa::where('user_id', $user->id)->exists()) {
            $redirectUrl = '/absensi/siswa/dashboard';
        } elseif ($user->hasRole('orang_tua')) {
            $redirectUrl = '/absensi/orangtua/dashboard';
        } elseif ($user->hasRole('kesiswaan')) {
            $redirectUrl = '/absensi/kesiswaan/dashboard';
        } elseif (\App\Models\Guru::where('user_id', $user->id)->exists()) {
            $redirectUrl = '/absensi/guru/dashboard';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => redirect()->intended($redirectUrl)->getTargetUrl()
            ]);
        }

        return redirect()->intended($redirectUrl);
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
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect']
            ]);
        }

        $user = User::where('email', $request->email)->first();
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
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
