<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\Role;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Dapatkan user dan role
        $user = Auth::user();
        $userRole = $user->role;
        
        // Konversi ke string value
        if ($userRole instanceof Role) {
            $userRoleValue = $userRole->value;
            $userRoleName = $userRole->name;
        } else {
            $userRoleValue = trim($userRole);
            // Cari nama enum dari value
            $userRoleName = null;
            foreach (Role::cases() as $case) {
                if ($case->value === $userRoleValue) {
                    $userRoleName = $case->name;
                    break;
                }
            }
        }

        // Log untuk debugging
        Log::info('RoleMiddleware Check', [
            'user_id' => $user->id,
            'user_role_value' => $userRoleValue,
            'user_role_name' => $userRoleName,
            'required_roles' => $roles,
            'url' => $request->fullUrl()
        ]);

        // Jika tidak ada parameter roles, berarti route ini bisa diakses semua role
        if (empty($roles)) {
            return $next($request);
        }

        // Cek apakah role user cocok dengan salah satu yang diizinkan
        foreach ($roles as $allowedRole) {
            // Bandingkan dengan value (untuk parameter seperti 'kln', 'mahasiswa')
            if ($userRoleValue === $allowedRole) {
                return $next($request);
            }
            
            // Bandingkan dengan name (untuk parameter seperti 'KLN', 'MAHASISWA')
            if ($userRoleName && strtoupper($userRoleName) === strtoupper($allowedRole)) {
                return $next($request);
            }
        }

        // Jika tidak cocok, tolak akses
        Log::warning('Access denied by RoleMiddleware', [
            'user_id' => $user->id,
            'user_role' => $userRoleValue,
            'required_roles' => $roles
        ]);

        abort(403, 'Unauthorized - Required role: ' . implode(', ', $roles));
    }
}