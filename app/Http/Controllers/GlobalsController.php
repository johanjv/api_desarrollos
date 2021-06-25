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
<<<<<<< HEAD
    }    

    public function insertDesarrollo(Request $request)
    {
        $insert = Desarrollos::create([
            "nomb_desarrollo" => $request['nomb_des']
        ]);

        $desarrollos = Desarrollos::all();
        
        return response()->json([
            "desarrollos" =>  $desarrollos
        ],200);
    }   
    
    public function consultaDesarrollo(Request $request)
    {
        $desarrollos = Desarrollos::all();
        return response()->json(["desarrollos" => $desarrollos, "status" => "ok"]);
    }

=======
    }

    public function getAllUsers(Request $request)
    {
        $users = User::all();
        return response()->json(["users" => $users],200);
    }

    public function saveEditUser(Request $request)
    {
        $data = $request->all();
        $user = User::where('id', $data['id'])->update([
            'name' =>  $data['name']
        ]);

        $usersRefresh = User::all();
        return response()->json(["usersRefresh" => $usersRefresh], 200); 
        
    }
    
    
    public function getRoles(Request $request)
    {
        $roles = Roles::all();
        return response()->json(["roles" => $roles],200);
    }
    
>>>>>>> f53b80a4c2e297119deb6da6d0451f0adf1e8a2f
}
