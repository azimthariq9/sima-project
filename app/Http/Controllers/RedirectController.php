<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function redirectByRole()
    {
        $role = Auth::user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
            'kln' => redirect()->route('kln.dashboard'),
            'jurusan' => redirect()->route('jurusan.dashboard'),
            'bipa' => redirect()->route('bipa.dashboard'),
            default => abort(403),
        };
    }
}
