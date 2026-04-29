<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Enums\Role;

class DashboardController extends Controller
{
public function index()
{
    $role = Auth::user()->role->value;


    return match(strtolower($role)) {
        'kln' => view('kln.dashboard'),
        'dosen' => view('dosen.dashboard'),
        'bipa' => view('bipa.dashboard'),
        'jurusan' => view('jurusan.dashboard'),
        default => view('mahasiswa.dashboard')
    };
}
}
