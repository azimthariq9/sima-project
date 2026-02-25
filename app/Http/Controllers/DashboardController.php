<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Http\Request;
use App\Services\RoleServices;

class DashboardController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;
        $roleServices = new RoleServices($userRole);
        
        $userRole = $roleServices->trimRole($userRole);
        print_r($userRole);
        switch ($userRole) {
            case 'kln':
                return view('kln.dashboard');

            case 'mahasiswa':
                return view('mahasiswa.dashboard');

            case 'Jurusan':
                return view('jurusan.dashboard');

            case 'Bipa':
                return view('bipa.dashboard');

            default:
                abort(403);
        }
    }
}