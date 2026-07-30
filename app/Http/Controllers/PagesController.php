<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Get view file location from menu config
        $view = theme()->getOption('page', 'view');

        // Automatic Role Dashboard Dispatcher & Hard Redirect
        if (auth()->check()) {
            $user = auth()->user();
            $isSiswa = \App\Models\Siswa::where('user_id', $user->id)->exists();
            $isGuru  = \App\Models\Guru::where('user_id', $user->id)->exists();

            if ($isSiswa) {
                if ($view === 'absensi/dashboard' || request()->is('absensi/dashboard') || request()->is('/')) {
                    return redirect('/absensi/siswa/dashboard');
                }
                if (in_array($view, ['absensi/siswa-dashboard', 'absensi/siswa/dashboard', 'absensi/siswa/kehadiran'])) {
                    return app(\App\Http\Controllers\Absensi\SiswaDashboardController::class)->index(request());
                }
                if (in_array($view, ['absensi/siswa-profil', 'absensi/siswa/profil'])) {
                    return app(\App\Http\Controllers\Absensi\SiswaDashboardController::class)->profil();
                }
            } elseif ($isGuru) {
                if ($view === 'absensi/dashboard' || request()->is('absensi/dashboard') || request()->is('/')) {
                    return redirect('/absensi/guru/dashboard');
                }
                if (in_array($view, ['absensi/guru-dashboard', 'absensi/guru/dashboard'])) {
                    return app(\App\Http\Controllers\Absensi\GuruDashboardController::class)->index();
                }
            }
        }

        if ($view === 'absensi/master/fingerprint/data') {
            return redirect('/absensi/fingerprint');
        }
        if ($view === 'absensi/master/fingerprint/log') {
            return redirect('/absensi/fingerprint/logs-view');
        }

        // Check if the page view file exist
        if (view()->exists('pages.'.$view)) {
            if ($view === 'absensi/dashboard') {
                if (auth()->check()) {
                    $user = auth()->user();
                    if (\App\Models\Siswa::where('user_id', $user->id)->exists()) {
                        return redirect('/absensi/siswa/dashboard');
                    }
                    if (\App\Models\Guru::where('user_id', $user->id)->exists()) {
                        return redirect('/absensi/guru/dashboard');
                    }
                    if ($user->hasRole('orang_tua')) {
                        return redirect('/absensi/orangtua/dashboard');
                    }
                }
                return app(\App\Http\Controllers\Absensi\AdminDashboardController::class)->index();
            }
            if (in_array($view, ['absensi/siswa-dashboard', 'absensi/siswa/dashboard', 'absensi/siswa/kehadiran'])) {
                return app(\App\Http\Controllers\Absensi\SiswaDashboardController::class)->index(request());
            }
            if (in_array($view, ['absensi/siswa-profil', 'absensi/siswa/profil'])) {
                return app(\App\Http\Controllers\Absensi\SiswaDashboardController::class)->profil();
            }
            if (in_array($view, ['absensi/guru-dashboard', 'absensi/guru/dashboard'])) {
                return app(\App\Http\Controllers\Absensi\GuruDashboardController::class)->index();
            }
            if (in_array($view, ['absensi/log-aktivitas', 'absensi/log-aktivitas/index'])) {
                $activities = \Spatie\Activitylog\Models\Activity::with('causer')->latest()->get();
                return view('pages.'.$view, compact('activities'));
            }
            return view('pages.'.$view);
        }

        // Get the default inner page
        return redirect('/');
    }

    /**
     * Temporary function to replace icon duotone
     */
    public function replaceIcons()
    {
        $fileContent = file_get_contents(public_path('icon_replacement.txt'));
        $lines       = explode("\n", $fileContent);

        $patterns     = [];
        $replacements = [];
        foreach ($lines as $line) {
            $el = explode(' - ', $line);
            if (empty($line)) {
                continue;
            }
            $patterns[]     = $el[0];
            $replacements[] = $el[1];
        }

        $files = File::allFiles(resource_path('views'));

        foreach ($files as $file) {
            $content = file_get_contents($file->getPathname());
            $content = str_replace($patterns, $replacements, $content);
            file_put_contents($file->getPathname(), $content);
        }
    }
}
