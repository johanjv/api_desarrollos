<?php

namespace App\Http\Controllers;

use App\Modulos;
use App\Submodulos;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        require("ldap.php");
        header("Content-Type: text/html; charset=utf-8");

        $usr = $request["username"];
        $usuario = mailboxpowerloginrd($usr, $request["password"]);
       

        if ($usuario == "0" || $usuario == '') {
            //return "NO se encuentra en directorio activo";
            $user = User::with('roles')->where('email', $request->username)->first();
            if ($user) {
                if (Hash::check($request['password'], $user->password)) {
                    $permisos = [];
                    foreach ($user->roles as $rol) {
                        array_push($permisos, $rol->modulo_id);
                    }

                    $modulos = Modulos::whereIn('id', $permisos)->where('desarrollo_id', $request['idDesarrollo'])->get();
                
                    if (count($modulos) > 0 ) {   
                        return response()->json(["status" => 'ok', "token" => $user->createToken('Auth Token')->accessToken], 200);
                    } else {
                        return response()->json(["status" => 'ok', "token" => 2], 200);
                    }
                } else {
                    return response()->json(["status" => 'Credenciales Incorrectas', "token" => 1], 200);
                }
            }
        } else {
            $user = User::with('roles')->where('email', $request->username)->first();
            if ($user) {
                //return "se encuentra en directorio activo y en la tabla usuarios";
                if (Hash::check($request['password'], $user->password)) {
                    $permisos = [];
                    foreach ($user->roles as $rol) {
                        array_push($permisos, $rol->modulo_id);
                    }

                    $modulos = Modulos::whereIn('id', $permisos)->where('desarrollo_id', $request['idDesarrollo'])->get();
                
                    if (count($modulos) > 0 ) {   
                        return response()->json(["status" => 'ok', "token" => $user->createToken('Auth Token')->accessToken], 200);
                    } else {
                        return response()->json(["status" => 'ok', "token" => 2], 200);
                    }
                } else {
                    return response()->json(["status" => 'Credenciales Incorrectas', "token" => 1], 200);
                }
            }else{
                //return "se encuentra en directorio activo y NO en la tabla usuarios";
                User::create([
                    'email'     => $request->username,
                    'name'      => $usuario[0]['cn'][0],
                    'password' => bcrypt($request['password'])
                ]);

                    $user = User::with('roles')->where('email', $request->username)->first();
                    $permisos = [];
                        foreach ($user->roles as $rol) {
                            array_push($permisos, $rol->modulo_id);
                        }

                    $modulos = Modulos::whereIn('id', $permisos)->where('desarrollo_id', $request['idDesarrollo'])->get();
            
                if (count($modulos) > 0 ) {   
                    return response()->json(["status" => 'ok', "token" => $user->createToken('Auth Token')->accessToken], 200);
                } else {
                    return response()->json(["status" => 'ok', "token" => 2], 200);
                }
            }
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
    }

    public function checkAutorizacion(Request $request)
    {
        $data = $request->all();

        $userAct           = Auth::user();
        $user              = User::with('roles')->with('roles')->where('id', $userAct['id'])->first();

        $permisos = [];
        foreach ($user->roles as $rol) {
            array_push($permisos, $rol->modulo_id); 
        }
        
        $modulos    = Modulos::with('submodulos')->whereIn('id', $permisos)->get();
        
        foreach ($modulos as $mod) {
            if ($mod->slug == $data['url']) {
                return response()->json(["modulos" => $mod->slug]); 
            }
            foreach ($mod->submodulos as $sub) {
                if ($sub->slug == $data['url']) {
                    return response()->json(["modulos" => $sub->slug]); 
                }
            }
        }

        

    }

    public function saveNewUser(Request $request)
    {
        $data = $request->all();
        $userCreate = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'is_directory'  => "0",
            'password'      => bcrypt($request['password'])
        ]);

        $users = User::with('roles')->get();
        foreach ($users as $user) {
            $user['newFecha'] = date_format($user['created_at'],"d/m/Y");
            $user['isDirec'] = $user['is_directory'] == 1 ? 'SI' : 'NO';
        }

        return response()->json(["users" => $users], 200);

    }

}
