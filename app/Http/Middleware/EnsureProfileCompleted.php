<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'mahasiswa' && !$user->profile_completed) {
                if (!$request->routeIs('mahasiswa.complete-profile')) {
                    return redirect()->route('mahasiswa.complete-profile');
                }
            }
        }

        return $next($request);
    }
}
