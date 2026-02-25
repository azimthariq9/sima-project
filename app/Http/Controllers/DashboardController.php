<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'admin':
                return view('kln.dashboard');

            case 'mahasiswa':
                return view('mahasiswa.dashboard');

            case 'jurusan':
                return view('jurusan.dashboard');

            case 'bipa':
                return view('bipa.dashboard');

            default:
                abort(403);
        }
    }
}