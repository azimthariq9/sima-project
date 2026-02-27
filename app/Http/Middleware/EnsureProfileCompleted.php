<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
public function handle($request, Closure $next)
{
    if (auth()->check()) {

        $user = auth()->user();

        if ($user->role->value === 'mahasiswa' && !$user->profile_completed) {
            if (!$request->routeIs('mahasiswa.complete-profile')) {
                return redirect()->route('mahasiswa.complete-profile');
            }
        }
    }

    return $next($request);
}
}
