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

}
