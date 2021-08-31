<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminGlobal\Modulos;
use App\Models\AdminGlobal\Submodulos;
use App\User;

class LoginController extends Controller
{

    public function login(Request $request)
    {
            $request->validate([
                'username' => ['required'],
                'password' => ['required']
            ]);

            require("LDAPConfig/ldap.php");
            header("Content-Type: text/html; charset=utf-8");

            $usr = $request["username"];
            $usuario = mailboxpowerloginrd($usr, $request["password"]);

            if ($usuario != "0" || $usuario != '') //LO ENCUENTRO EN EL DIRECTORIO ACTIVO
                {
                    /* Extraigo la informacion basica del usuario que se loguea por primera vez para insertarlo en la tabla user */
                    $detalleUser = mb_convert_encoding($usuario, 'UTF-8', 'UTF-8');

                    /* Se encuentra en el directorio activo */
                    $user = User::with('roles')->where('email', $request->username)->first(); //Lo busco en la tabla de user
                        if ($user) //si existe en el directorio activo y en la tabla user
                        {
                            if (Hash::check($request['password'], $user->password)) //valido si la contraseña y el usuario son correctos
                            {
                                //Actualizo los permisos en la tabla user
                                $rolUser = $this->rolesUser($usuario);
                                $updatePermisos = User::with('roles')->where('email', $request->username)->update([
                                    'rol' => json_encode($rolUser),
                                ]);

                                //Capturo el usuarios nuevamente
                                $user = User::with('roles')->where('email', $request->username)->first();
                                //Asigno el token al usuario
                                $tokenUser = $user->createToken('Auth Token')->accessToken;
                                $request->session()->regenerate();

                                return response()->json([
                                    "estado" => "1",
                                    "user" => $user,
                                    "token" => $tokenUser
                                ], 200);

                            } else { //Si son incorrectas las credenciales de la tabla user

                                //Actualizo los permisos en la tabla user y la contraseña ya que puede que este actualizada en el directorio activo pero no en la tabla user
                                $rolUser = $this->rolesUser($usuario);
                                $updatePermisos = User::with('roles')->where('email', $request->username)->update([
                                    'rol' => json_encode($rolUser),
                                    'password' => bcrypt($request['password'])
                                ]);

                                 //Capturo el usuarios nuevamente
                                 $user = User::with('roles')->where('email', $request->username)->first();
                                 //Asigno el token al usuario
                                 $tokenUser = $user->createToken('Auth Token')->accessToken;
                                 $request->session()->regenerate();

                                 return response()->json([
                                    "estado" => "1",
                                    "user" => $user,
                                     "token" => $tokenUser
                                 ], 200);

                            }
                        } else { //Si no existe en la tabla usuarios pero si en el directorio activo

                            /* funcion para obtener los roles disponibles del usuario segun el LDAP */
                            $rolUser = $this->rolesUser($usuario);

                            User::create([
                                'nro_doc'       => $detalleUser[0]['wwwhomepage'][0],
                                'name'          => $detalleUser[0]['givenname'][0],
                                'last_name'     => $detalleUser[0]['sn'][0],
                                'email'         => $detalleUser[0]['samaccountname'][0],
                                'correo'        => $detalleUser[0]['mail'][0],
                                'rol'           => json_encode($rolUser),
                                'cargo'         => $detalleUser[0]['description'][0],
                                'empresa'       => $detalleUser[0]['physicaldeliveryofficename'][0],
                                'password'      => bcrypt($request['password']),
                                'is_director'   => 1,
                                'estado'   => 1
                            ]);

                            //Capturo el usuarios nuevamente
                            $user = User::with('roles')->where('email', $request->username)->first();

                            //Asigno el token al usuario
                            $tokenUser = $user->createToken('Auth Token')->accessToken;
                            $request->session()->regenerate();

                            return response()->json([
                                    "estado" => "4",
                                    "user" => $user,
                                "token" => $tokenUser
                            ], 200);
                        }
                }
            else //SI NO LO ENCUENTRO EN EL DIRECTORIO ACTIVO
                {
                    $user = User::with('roles')->where('email', $request->username)->first(); //Lo busco en la tabla de user
                    if ($user) //si existe en la tabla user
                    {
                        if (Hash::check($request['password'], $user->password)) //valido si la contraseña y el usuario son correctos
                            {
                                $tokenUser = $user->createToken('Auth Token')->accessToken;
                                $request->session()->regenerate();

                                return response()->json([
                                    "estado" => "1",
                                    "user" => $user,
                                    "token" => $tokenUser
                                ], 200);
                            }else{
                                return response()->json([
                                    "estado" => "2",
                                    "user" => null,
                                    "token" => null
                                ], 200);
                            }
                    }else { //Si no esta en la tabla user
                        return response()->json([
                            "estado" => "3",
                            "user" => null,
                            "token" => null
                        ], 200);
                    }
                }
    }

    public function rolesUser($usuario)
    {
        $detalleUser = mb_convert_encoding($usuario, 'UTF-8', 'UTF-8');
        $gruposLDAP = $detalleUser[0]['memberof'];
        $a = implode(",",$gruposLDAP);
        $grupos = explode(",", $a);
        $rolUser = [];
        foreach ($grupos as $key => $value) {
            if ($key != 'count') {
                if ($value == 'CN=AGSuperAdmin') {
                    array_push($rolUser, 1);
                } elseif ($value == 'CN=AGAdministrador') {
                    array_push($rolUser, 2);
                } elseif ($value == 'CN=HVConsultor') {
                    array_push($rolUser, 3);
                } elseif ($value == 'CN=HVSupervisor') {
                    array_push($rolUser, 4);
                } elseif ($value == 'CN=HVAdmServHab') {
                    array_push($rolUser, 5);
                } elseif ($value == 'CN=HVAdmInfra') {
                    array_push($rolUser, 6);
                }
            }
        }
        return $rolUser;

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

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

    public function check(Request $request)
    {
        $session_act = $request->cookie('key');
        $user = Auth::user();
        return response()->json(["message" => 'Success.', "session_act" => $session_act, "user" => $user]);
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
