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

            // Protect all non-documentation pages with auth
            if (!str_contains($val['path'], 'documentation')) {
                $route->middleware('auth');
            }
        }
    }
});

// Documentations pages
Route::prefix('documentation')->group(function () {
    Route::get('getting-started/references', [ReferencesController::class, 'index']);
    Route::get('getting-started/changelog', [PagesController::class, 'index']);
});

// Absensi profile pages (not in menu, linked via action buttons)
Route::middleware('auth')->group(function () {
    // Absensi Modules Resource Routes
    Route::resource('absensi/master/tahun-ajaran', \App\Http\Controllers\Absensi\TahunAjaranController::class)->names('tahun-ajaran');
    Route::post('absensi/master/semester', [\App\Http\Controllers\Absensi\SemesterController::class, 'store'])->name('semester.store');
    Route::put('absensi/master/semester/{semester}', [\App\Http\Controllers\Absensi\SemesterController::class, 'update'])->name('semester.update');
    Route::delete('absensi/master/semester/{semester}', [\App\Http\Controllers\Absensi\SemesterController::class, 'destroy'])->name('semester.destroy');
    
    Route::resource('absensi/master/jurusan', \App\Http\Controllers\Absensi\JurusanController::class)->names('jurusan');
    Route::get('absensi/master/guru/download-template', [\App\Http\Controllers\Absensi\GuruController::class, 'downloadTemplate'])->name('guru.download-template');
    Route::post('absensi/master/guru/import', [\App\Http\Controllers\Absensi\GuruController::class, 'import'])->name('guru.import');
    Route::resource('absensi/master/guru', \App\Http\Controllers\Absensi\GuruController::class)->names('guru');
    Route::resource('absensi/master/kelas/data', \App\Http\Controllers\Absensi\KelasController::class)->names('kelas')->parameters(['data' => 'kelas']);
    Route::get('absensi/master/siswa/download-template', [\App\Http\Controllers\Absensi\SiswaController::class, 'downloadTemplate'])->name('siswa.download-template');
    Route::post('absensi/master/siswa/import', [\App\Http\Controllers\Absensi\SiswaController::class, 'import'])->name('siswa.import');
    Route::post('absensi/bulk-delete', [\App\Http\Controllers\Absensi\BulkDeleteController::class, 'destroy'])->name('bulk-delete');
    Route::resource('absensi/master/siswa', \App\Http\Controllers\Absensi\SiswaController::class)->names('siswa');
    Route::resource('absensi/master/mata-pelajaran', \App\Http\Controllers\Absensi\MataPelajaranController::class)->names('mata-pelajaran');
    Route::resource('absensi/master/jadwal-pelajaran', \App\Http\Controllers\Absensi\JadwalPelajaranController::class)->names('jadwal-pelajaran');
    Route::resource('absensi/master/aturan-jam', \App\Http\Controllers\Absensi\AturanJamController::class)->names('aturan-jam');
    Route::resource('absensi/kehadiran', \App\Http\Controllers\Absensi\KehadiranController::class)->names('kehadiran');

    Route::get('absensi/profil-siswa', [PagesController::class, 'index']);
    Route::get('absensi/profil-guru', [PagesController::class, 'index']);
    Route::get('absensi/profil-kelas', [PagesController::class, 'index']);
});



Route::middleware('auth')->group(function () {
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

Route::middleware('auth')->resource('users', UsersController::class);

/**
 * Socialite login using Google service
 * https://laravel.com/docs/8.x/socialite
 */
Route::get('/auth/redirect/{provider}', [SocialiteLoginController::class, 'redirect']);

require __DIR__.'/auth.php';
