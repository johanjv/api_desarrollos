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

        /* Insert array roles */
        foreach ($data['roles'] as $rol) {
            $rolUser = RolUser::create([
                "user_id" => $data['user']['id'],
                "rol_id" => $rol['id']
            ]);
        }
        
        /* Insert array mod */
        foreach ($data['modulos'] as $modulo) {
            $rolUserMod = RolUserMod::create([
                "rol_user_id" => $rolUser->id, 
                "modulo_id" => $modulo['id']
            ]);
        }

        $users = User::with('roles')->get();
        foreach ($users as $user) {
            $user['newFecha'] = date_format($user['created_at'],"d/m/Y");
            $user['isDirec'] = $user['is_directory'] == 1 ? 'SI' : 'NO';
        }
        return response()->json(["users" => $users],200);
    }

    public function getModulosPerDesarrollo(Request $request)
    {
        $data = $request->all();
        $arrayDes = [];
        foreach ($data['desarrollo'] as $des) {
            array_push($arrayDes, $des['id']);
            /* Hacer un push a arrayDes para luego un whereIn en la consulta*/
        }

        $modulos = DB::table('MASTER.modulos')->select('*')->whereIn('desarrollo_id', $arrayDes)->get();
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
    
}
