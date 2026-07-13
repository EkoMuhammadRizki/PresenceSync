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

    $user = auth()->user();

    // Check if logged in user is a student
    if (\App\Models\Siswa::where('user_id', $user->id)->exists()) {
        return redirect('/absensi/siswa/dashboard');
    }

    // Check if logged in user is orang tua
    if ($user->hasRole('orang_tua')) {
        return redirect('/absensi/orangtua/dashboard');
    }

    // Check if logged in user is kesiswaan
    if ($user->hasRole('kesiswaan')) {
        return redirect('/absensi/kesiswaan/dashboard');
    }

    // Check if logged in user is a guru
    if (\App\Models\Guru::where('user_id', $user->id)->exists()) {
        return redirect('/absensi/guru/dashboard');
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
    Route::resource('absensi/pengguna/data', \App\Http\Controllers\Absensi\PenggunaController::class)->names('pengguna')->parameters(['data' => 'pengguna']);
    Route::resource('absensi/master/tahun-ajaran', \App\Http\Controllers\Absensi\TahunAjaranController::class)->names('tahun-ajaran');
    Route::post('absensi/master/semester', [\App\Http\Controllers\Absensi\SemesterController::class, 'store'])->name('semester.store');
    Route::put('absensi/master/semester/{semester}', [\App\Http\Controllers\Absensi\SemesterController::class, 'update'])->name('semester.update');
    Route::delete('absensi/master/semester/{semester}', [\App\Http\Controllers\Absensi\SemesterController::class, 'destroy'])->name('semester.destroy');
    Route::get('absensi/master/guru/download-template', [\App\Http\Controllers\Absensi\GuruController::class, 'downloadTemplate'])->name('guru.download-template');
    Route::post('absensi/master/guru/import', [\App\Http\Controllers\Absensi\GuruController::class, 'import'])->name('guru.import');
    Route::resource('absensi/master/guru', \App\Http\Controllers\Absensi\GuruController::class)->names('guru');
    Route::resource('absensi/master/kelas/data', \App\Http\Controllers\Absensi\KelasController::class)->names('kelas')->parameters(['data' => 'kelas']);
    Route::resource('absensi/master/kelas/pembagian', \App\Http\Controllers\Absensi\PembagianKelasController::class)->names('pembagian-kelas')->parameters(['pembagian' => 'pembagian'])->only(['index', 'show']);
    Route::post('absensi/master/kelas/pembagian/{kelas}/add-siswa', [\App\Http\Controllers\Absensi\PembagianKelasController::class, 'addSiswa'])->name('pembagian-kelas.add-siswa');
    Route::delete('absensi/master/kelas/pembagian/{kelas}/remove-siswa/{siswa}', [\App\Http\Controllers\Absensi\PembagianKelasController::class, 'removeSiswa'])->name('pembagian-kelas.remove-siswa');
    Route::post('absensi/master/kelas/pembagian/{kelas}/set-sekretaris/{siswa}', [\App\Http\Controllers\Absensi\PembagianKelasController::class, 'setSekretaris'])->name('pembagian-kelas.set-sekretaris');
    Route::get('absensi/master/siswa/download-template', [\App\Http\Controllers\Absensi\SiswaController::class, 'downloadTemplate'])->name('siswa.download-template');
    Route::post('absensi/master/siswa/import', [\App\Http\Controllers\Absensi\SiswaController::class, 'import'])->name('siswa.import');
    Route::post('absensi/bulk-delete', [\App\Http\Controllers\Absensi\BulkDeleteController::class, 'destroy'])->name('bulk-delete');
    Route::resource('absensi/master/siswa', \App\Http\Controllers\Absensi\SiswaController::class)->names('siswa');
    Route::resource('absensi/master/mata-pelajaran', \App\Http\Controllers\Absensi\MataPelajaranController::class)->names('mata-pelajaran');
    Route::resource('absensi/master/jadwal-pelajaran', \App\Http\Controllers\Absensi\JadwalPelajaranController::class)->names('jadwal-pelajaran');
    Route::resource('absensi/master/aturan-jam', \App\Http\Controllers\Absensi\AturanJamController::class)->names('aturan-jam');
    Route::get('absensi/kehadiran/export', [\App\Http\Controllers\Absensi\KehadiranController::class, 'export'])->name('kehadiran.export');
    Route::resource('absensi/kehadiran', \App\Http\Controllers\Absensi\KehadiranController::class)->names('kehadiran');

    Route::get('absensi/profil-siswa', [\App\Http\Controllers\Absensi\SiswaProfileController::class, 'show'])->name('profil-siswa.show');
    Route::put('absensi/profil-siswa/{siswa}', [\App\Http\Controllers\Absensi\SiswaProfileController::class, 'update'])->name('profil-siswa.update');
    Route::put('absensi/profil-siswa/password', [\App\Http\Controllers\Absensi\SiswaProfileController::class, 'changePassword'])->name('profil-siswa.changePassword');
    
    Route::get('absensi/profil-guru', [\App\Http\Controllers\Absensi\GuruProfileController::class, 'show'])->name('profil-guru.show');
    Route::put('absensi/profil-guru/{guru}', [\App\Http\Controllers\Absensi\GuruProfileController::class, 'update'])->name('profil-guru.update');
    Route::put('absensi/profil-guru/password', [\App\Http\Controllers\Absensi\GuruProfileController::class, 'changePassword'])->name('profil-guru.changePassword');

    Route::get('absensi/profil-admin', [\App\Http\Controllers\Absensi\AdminProfileController::class, 'show'])->name('profil-admin.show');
    Route::put('absensi/profil-admin', [\App\Http\Controllers\Absensi\AdminProfileController::class, 'update'])->name('profil-admin.update');
    Route::put('absensi/profil-admin/password', [\App\Http\Controllers\Absensi\AdminProfileController::class, 'changePassword'])->name('profil-admin.changePassword');
    
    Route::get('absensi/profil-kelas', [PagesController::class, 'index']);
    Route::get('absensi/panduan', [\App\Http\Controllers\Absensi\PanduanController::class, 'index'])->name('panduan.index');
    Route::get('absensi/guru/kelas-wali', [\App\Http\Controllers\Absensi\GuruKelasController::class, 'index'])->name('guru.kelas-wali');

    // Pengaturan Restriksi Halaman & Simpan
    Route::get('absensi/pengaturan-restriksi/kelas', [\App\Http\Controllers\Absensi\RestriksiKelasController::class, 'index'])->name('pengaturan-restriksi.kelas.index');
    Route::post('absensi/pengaturan-restriksi/kelas', [\App\Http\Controllers\Absensi\RestriksiKelasController::class, 'update'])->name('pengaturan-restriksi.kelas.update');

    // Student Dashboard Routes
    Route::get('absensi/siswa/dashboard', [\App\Http\Controllers\Absensi\SiswaDashboardController::class, 'index'])->name('siswa.dashboard');
    Route::get('absensi/siswa/dashboard/export', [\App\Http\Controllers\Absensi\SiswaDashboardController::class, 'export'])->name('siswa.dashboard.export');
    Route::post('absensi/siswa/presensi', [\App\Http\Controllers\Absensi\SiswaDashboardController::class, 'presensi'])->name('siswa.presensi');
    Route::post('absensi/siswa/izin', [\App\Http\Controllers\Absensi\SiswaDashboardController::class, 'izin'])->name('siswa.izin');

    // Guru Dashboard Routes
    Route::get('absensi/guru/dashboard', [\App\Http\Controllers\Absensi\GuruDashboardController::class, 'index'])->name('guru.dashboard');


    // Kesiswaan Dashboard Routes
    Route::get('absensi/kesiswaan/dashboard', [\App\Http\Controllers\Absensi\KesiswaanDashboardController::class, 'index'])->name('kesiswaan.dashboard');

    // Orang Tua Dashboard Routes
    Route::get('absensi/orangtua/dashboard', [\App\Http\Controllers\Absensi\OrangTuaDashboardController::class, 'index'])->name('orangtua.dashboard');
});



Route::middleware('auth')->group(function () {
    // Account pages
    Route::prefix('account')->group(function () {
        Route::get('settings', [\App\Http\Controllers\Absensi\ProfileController::class, 'index'])->name('settings.index');
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
