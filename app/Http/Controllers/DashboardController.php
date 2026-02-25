<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        if ($userRole instanceof Role) {
                $userRole = $userRole->value; // Ambil string value-nya
        }
        
            // Sekarang $userRole pasti string
        $userRole = trim($userRole);

        switch ($userRole) {
            case 'kln':
                return view('kln.dashboard');

            case 'mahasiswa':
                return view('mahasiswa.dashboard');

            case 'Jurusan':
                return view('jurusan.dashboard');

            case 'bipa':
                return view('bipa.dashboard');

            default:
                abort(403);
        }
    }
}