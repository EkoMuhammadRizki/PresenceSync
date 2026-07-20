<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Carbon;

class KesiswaanDashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Kesiswaan (Rekap Seluruh Sekolah).
     */
    public function index()
    {
        return view('pages.absensi.dashboard');
    }
}
