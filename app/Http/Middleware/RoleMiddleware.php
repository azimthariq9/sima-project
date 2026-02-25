<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rules\Enum;
use App\Enums\Role;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

         // Jika tidak ada parameter roles yang diberikan, ambil semua role dari enum
        if (empty($roles)) {
            $roles = array_map(function($case) {
                return $case->name;
            }, Role::cases());
        }
        // Ambil role user - bisa berupa string atau objek enum
        $userRole = Auth::user()->role;
    
             // Jika role adalah objek enum, ambil value-nya
        if ($userRole instanceof Role) {
                $userRole = $userRole->value; // Ambil string value-nya
        }
        
            // Sekarang $userRole pasti string
        $userRole = trim($userRole);
        $isValidRole = false;

        foreach ($roles as $roleParam) {
            // Cari enum case berdasarkan nama parameter (misal 'KLN')
            foreach (Role::cases() as $case) {
                if ($case->name === $roleParam && $userRole === $case->value) {
                    $isValidRole = true;
                    break 2; // Keluar dari kedua loop
                }
            }
        }

        if (!$isValidRole) {
            print_r($roles);
            print_r($userRole);
            abort(403, 'Unauthorized');
            sleep(1);
            return redirect('/logout');
        }

        return $next($request);
    }
}