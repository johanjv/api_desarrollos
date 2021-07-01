<?php

namespace App\Http\Controllers;

use App\Modulos;
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

        /* VALIDAMOS SI EL USUARIO DEL DIRECTORIO ACTIVO EXISTE*/
        if ($usuario == "0" || $usuario == '') {    //SI LAS CREDENCIALES SON INCORRECTAS
            $user = User::where('email', $request->username)->count();  //CONTEO DEL USER QUE FUE ENVIADO POR REQUEST EN LA TABLA DE users
            if ($user > 0) {    //SI EXISTE EN LA TABLA USER LE ASIGNO EL TOKEN
                $user = User::with('roles')->where('email', $request->username)->first(); 
                $permisos = [];
                foreach ($user->roles as $rol) {
                    array_push($permisos, $rol->modulo_id);
                }
                $modulos    = Modulos::whereIn('id', $permisos)->get();

                if (count($modulos) > 0) {   
                    return response()->json(["status" => 'ok', "token" => $user->createToken('Auth Token')->accessToken], 200);
                }else {
                    return response()->json(["status" => 'ok', "token" => null], 200);
                }
            } else {    //SI NO EXISTE EN EL DIRECTORIO ACTIVO O EN LA TABLA users DEVUELVO ERROR
                throw ValidationException::withMessages([
                    'username' => ['Las credenciales proporcionadas son incorrectas']
                ]);
            }
        } else {    //SI LAS CREDENCIALES DEL DIRECTORIO ACTIVO SON CORRECTAS
            $user = User::where('email', $request->username)->count();  //VALIDO SI EXISTE EN LA TABLA users
            if ($user > 0) {    //SI EXISTE EN LA TABLA users LE ASIGNO EL TOKEN
                $user = User::with('roles')->where('email', $request->username)->first();
                    return response()->json(["status" => 'ok',"token" => $user->createToken('Auth Token')->accessToken], 200);
            } else { //SI LAS CREDENCIALES DEL DIRECTORIO ACTIVO SON CORRECTAS PERO NO ESTA EN LA TABLA users LO INSERTO EN users
                //dd($usuario[0]['cn'][0]); //nombre completo
                User::with('roles')->create(
                    [
                        'email'     => $request->username,
                        'name'      => $usuario[0]['cn'][0],
                        'password' => bcrypt($request['password'])
                    ]
                );
                /* ASIGNO EL TOKEN*/
                $user = User::with('roles')->where('email', $request->username)->first();
                return response()->json(["status"    => 'ok',"token"     => $user->createToken('Auth Token')->accessToken], 200);
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

        $userAct        = Auth::user();
        $user           = User::with('roles')->with('roles')->where('id', $userAct['id'])->first();
        $modPermitidos  = Modulos::where('slug', $data['url'])->first('id');
        $permisos = [];
        foreach ($user->roles as $rol) {
            array_push($permisos, $rol->modulo_id); 
        }
        
        $modulos    = Modulos::whereIn('id', $permisos)->get();
        

        foreach ($modulos as $mod) {
            if ($mod->slug == $data['url']) {
                return response()->json(["modulos" => $mod->slug]); 
            }
        }

    }
}
