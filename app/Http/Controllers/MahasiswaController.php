<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        return view('mahasiswa.dashboard');
    }

    public function profil()
    {
        return view('mahasiswa.profil');
    }

    public function dokumen()
    {
        return view('mahasiswa.dokumen');
    }

    public function jadwal()
    {
        return view('mahasiswa.jadwal');
    }

    public function notifikasi()
    {
        return view('mahasiswa.notifikasi');
    }

    public function announcement()
    {
        return view('mahasiswa.announcement');
    }

    public function announcementShow($id)
    {
        return view('mahasiswa.announcement-show', compact('id'));
    }

    public function analytics()
    {
        return view('mahasiswa.analytics');
    }
}
