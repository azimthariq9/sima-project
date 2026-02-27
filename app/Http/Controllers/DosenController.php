<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function dashboard()
    {
        return view('dosen.dashboard');
    }

    public function profil()
    {
        return view('dosen.profil');
    }

    public function jadwal()
    {
        return view('dosen.jadwal');
    }

    public function announcement()
    {
        return view('dosen.announcement');
    }

    public function notifikasi()
    {
        return view('dosen.notifikasi');
    }

    public function analytics()
    {
        return view('dosen.analytics');
    }
}