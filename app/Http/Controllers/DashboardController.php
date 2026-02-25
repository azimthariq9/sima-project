<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'kln':
                return view('kln.dashboard');

            case 'mahasiswa':
                return view('mahasiswa.dashboard');

            case 'adminJurusan':
                return view('jurusan.dashboard');

            case 'bipa':
                return view('bipa.dashboard');

            default:
                abort(403);
        }
    }
}