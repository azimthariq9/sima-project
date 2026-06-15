<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect user ke dashboard sesuai role-nya.
     * Route /dashboard (name: dashboard) tidak langsung render view —
     * cukup redirect ke route role-specific yang sudah punya data lengkap.
     */
    public function index()
    {
        $user = Auth::user();

        $role = strtolower(
            $user->role instanceof \App\Enums\Role
                ? $user->role->value
                : $user->role
        );

        return match($role) {
            'mahasiswa'  => redirect()->route('mahasiswa.dashboard'),
            'dosen'      => redirect()->route('dosen.dashboard'),
            'kln'        => redirect()->route('kln.dashboard'),
            'jurusan'    => redirect()->route('jurusan.dashboard'),
            'bipa'       => redirect()->route('bipa.dashboard'),
            'gunadarma'  => redirect()->route('gunadarma.dashboard'),
            default      => redirect()->route('mahasiswa.dashboard'),
        };
    }
}
