<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Enums\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $role = $user->role;

        if ($role instanceof Role) {
            $role = $role->value;
        }

        $role = strtolower(trim($role)); // <-- MAGIC FIX

        switch ($role) {

            case 'kln':
                return view('kln.dashboard');

            case 'mahasiswa':
                return view('mahasiswa.dashboard');

            case 'jurusan':
                return view('jurusan.dashboard');

            case 'bipa':
                return view('bipa.dashboard');

            case 'dosen':
                return view('dosen.dashboard');

            default:
                abort(403, 'Role tidak dikenali: ' . $role);
        }
    }
}