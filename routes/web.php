<?php

use App\Http\Controllers\Account\SettingsController;
use App\Http\Controllers\Auth\SocialiteLoginController;
use App\Http\Controllers\Documentation\ReferencesController;
use App\Http\Controllers\Logs\AuditLogsController;
use App\Http\Controllers\Logs\SystemLogsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // If not logged in, go to login page for prototype
    if (!auth()->check()) {
        return redirect('/login');
    }
    return app(PagesController::class)->index();
});

Route::get('/prototype-login', function () {
    if (app()->environment('local')) {
        $user = \App\Models\User::where('email', 'admin@demo.com')->first() ?? \App\Models\User::first();
        if ($user) {
            auth()->login($user);
        }
    }
    return redirect('absensi/dashboard');
});

$menu = theme()->getMenu();
array_walk($menu, function ($val) {
    if (isset($val['path'])) {
        // Special handling for logout path
        if ($val['path'] === 'logout') {
            // Redirect GET logout to login page (for prototype mode)
            Route::get('logout', function () {
                auth()->logout();
                return redirect('/login');
            });
        } else {
            $route = Route::get($val['path'], [PagesController::class, 'index']);

            // Exclude documentation from auth middleware
            // if (!Str::contains($val['path'], 'documentation')) {
            //    $route->middleware('auth');
            // }
        }
    }
});

// Documentations pages
Route::prefix('documentation')->group(function () {
    Route::get('getting-started/references', [ReferencesController::class, 'index']);
    Route::get('getting-started/changelog', [PagesController::class, 'index']);
});

// Absensi profile pages (not in menu, linked via action buttons)
Route::get('absensi/profil-siswa', [PagesController::class, 'index']);
Route::get('absensi/profil-guru', [PagesController::class, 'index']);
Route::get('absensi/profil-kelas', [PagesController::class, 'index']);


Route::group([], function () {
    // Account pages
    Route::prefix('account')->group(function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::put('settings/email', [SettingsController::class, 'changeEmail'])->name('settings.changeEmail');
        Route::put('settings/password', [SettingsController::class, 'changePassword'])->name('settings.changePassword');
    });

    // Logs pages
    Route::prefix('log')->name('log.')->group(function () {
        Route::resource('system', SystemLogsController::class)->only(['index', 'destroy']);
        Route::resource('audit', AuditLogsController::class)->only(['index', 'destroy']);
    });
});

Route::resource('users', UsersController::class);

/**
 * Socialite login using Google service
 * https://laravel.com/docs/8.x/socialite
 */
Route::get('/auth/redirect/{provider}', [SocialiteLoginController::class, 'redirect']);

require __DIR__.'/auth.php';
