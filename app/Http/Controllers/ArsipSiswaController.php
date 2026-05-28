<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArsipSiswaController extends Controller
{
    public function showArsipSiswa()
    {
        return view('pages.arsip_siswa');
    }
}
