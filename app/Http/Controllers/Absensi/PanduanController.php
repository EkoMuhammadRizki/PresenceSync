<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;

class PanduanController extends Controller
{
    public function index()
    {
        return view('pages.absensi.panduan');
    }
}
