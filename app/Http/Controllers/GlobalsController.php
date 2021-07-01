<?php

namespace App\Http\Controllers;

use App\User;
use App\Desarrollos;
use App\Roles;
use App\RolUser;
use App\RolUserMod;
use App\Modulos;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

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

    public function getAllUsers(Request $request)
    {
        $users = User::with('roles')->get();
        foreach ($users as $user) {
            $user['newFecha'] = date_format($user['created_at'],"d/m/Y");
            $user['isDirec'] = $user['is_directory'] == 1 ? 'SI' : 'NO';
        }
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

    public function saveRol(Request $request)
    {
        $data = $request->all();
               
        /* Insert array mod */
        foreach ($data['modulos'] as $modulo) {
            $rolUserMod = RolUserMod::create([
                "rol_id"    => $data['rol'],
                "user_id"   => $data['user'],
                "modulo_id" => $modulo
            ]);
        }

        $users = User::all();
        foreach ($users as $user) {
            $user['newFecha'] = date_format($user['created_at'],"d/m/Y");
            $user['isDirec'] = $user['is_directory'] == 1 ? 'SI' : 'NO';
        }
        return response()->json(["users" => $users],200);
    }

    public function getModulosPerDesarrollo(Request $request)
    {
        $data = $request->all();
        $modulos = Modulos::where('desarrollo_id', $data['desarrollo']['id'])->get();
        return response()->json(["modulos" => $modulos],200);
    }

    public function insertModulo(Request $request)
    {
        $insert = Modulos::create([
            "nomb_modulo" => $request['nomb_modulo'],
            "desarrollo_id" => $request['desarrollo_id'],
        ]);

        $modulo = Modulos::all();
        $modulo->load('desarrollo');
        
        return response()->json([
            "modulo" =>  $modulo
        ],200);
    } 

    public function consultaModulos(Request $request)
    {
        $modulos = Modulos::all();
        $modulos->load('desarrollo');
        return response()->json(["modulos" => $modulos, "status" => "ok"]);
    }

    /* PERMISOS PARA EL SIDEBAR */
    public function getMenuDash(Request $request)
    {
        $data       = $request->all();
        $userAct    = Auth::user();
        $user       = User::with('roles')->where('id', $userAct['id'])->first();
        $countUser          = User::count();
        $countDesarrollos   = Desarrollos::count();
        $countRoles         = Roles::count();

        $permisos = [];
        foreach ($user->roles as $rol) {
            array_push($permisos, $rol->modulo_id);
        }
        $modulos    = Modulos::whereIn('id', $permisos)->get();
        return response()->json([
            "modulos" => $modulos,
            "countUser"         => $countUser,
            "countDesarrollos"  => $countDesarrollos,
            "countRoles"        => $countRoles
        ]);

    }
    
}
