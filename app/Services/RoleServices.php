<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Http\Request;

class RoleServices
{
    public function trimRole($userRole)
    {
        if ($userRole instanceof Role) {
                $userRole = $userRole->value; // Ambil string value-nya
        }
        
            // Sekarang $userRole pasti string
        return trim($userRole);
    }
   
}