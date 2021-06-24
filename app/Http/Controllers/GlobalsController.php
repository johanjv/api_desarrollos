<?php

namespace App\Http\Controllers;

use App\User;
use App\Desarrollos;
use App\Roles;
use Illuminate\Http\Request;

class GlobalsController extends Controller
{
    public function getCountDash(Request $request)
    {
        $countUser          = User::count();
        $countDesarrollos   = Desarrollos::count();
        $countRoles         = Roles::count();
        
        return response()->json([
            "countUser"         => $countUser,
            "countDesarrollos"  => $countDesarrollos,
            "countRoles"        => $countRoles
        ],200);
    }    
}
