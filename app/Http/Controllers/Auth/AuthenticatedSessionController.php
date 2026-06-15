<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    $user = auth()->user();
    $role = strtolower((string) $user->role);

    if ($role === 'mahasiswa') {
        if (!$user->profile_completed) {
            return redirect()->route('mahasiswa.complete-profile');
        }
        return redirect()->route('mahasiswa.dashboard');
    }

    if ($role === 'dosen') {
        return redirect()->route('dosen.dashboard');
    }

    if ($role === 'kln') {
        return redirect()->route('kln.dashboard');
    }

    if ($role === 'jurusan') {
        return redirect()->route('jurusan.dashboard');
    }

    if ($role === 'bipa') {
        return redirect()->route('bipa.dashboard');
    }

    return redirect('/dashboard');
}
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
