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
        // Redirect student to student dashboard
        if (auth()->check()) {
            $user = auth()->user();
            if (\App\Models\Siswa::where('user_id', $user->id)->exists()) {
                return redirect('/absensi/siswa/dashboard');
            }
            if (request()->is('absensi/dashboard')) {
                if ($user->hasRole('kesiswaan')) {
                    return redirect('/absensi/kesiswaan/dashboard');
                }
                if ($user->hasRole('orang_tua')) {
                    return redirect('/absensi/orangtua/dashboard');
                }
                if (\App\Models\Guru::where('user_id', $user->id)->exists()) {
                    return redirect('/absensi/guru/dashboard');
                }
            }
        }

        // Get view file location from menu config
        $view = theme()->getOption('page', 'view');

        // Check if the page view file exist
        if (view()->exists('pages.'.$view)) {
            if ($view === 'absensi/dashboard') {
                return app(\App\Http\Controllers\Absensi\AdminDashboardController::class)->index();
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
            $patterns[]     = trim($el[0]);
            $replacements[] = trim($el[1]);
        }

        $files    = File::allFiles(resource_path());
        $filtered = array_filter($files, function ($str) {
            return strpos($str, ".php") !== false;
        });

        foreach ($filtered as $file) {
            $bladeFileContent = file_get_contents($file->getPathname());

            $bladeFileContent = str_replace($patterns, $replacements, $bladeFileContent);

            file_put_contents($file->getPathname(), $bladeFileContent);
        }
    }
}
