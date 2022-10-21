<?php

namespace App\Http\Controllers\v3\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminGlobal\Modulos;
use App\Models\Bitacora\Bitacora;
use App\Models\GestionPaciente\Consultorios;
use App\Models\GestionPaciente\Medicos;
use App\User;
use DB;

class AuthController extends Controller
{

    public function login(Request $request)
    {

<<<<<<< HEAD
        /* return $request->all(); */

=======
>>>>>>> v3
        $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

<<<<<<< HEAD

        header("Content-Type: text/html; charset=utf-8");

        $usr = $request["username"];
        $usuario = $this->mailboxpowerloginrd($usr, $request["password"]);
        $usuario = mb_convert_encoding($usuario, 'UTF-8', 'UTF-8');

        if (gettype($usuario) == 'array') //LO ENCUENTRO EN EL DIRECTORIO ACTIVO
        {
            /* return "1"; */
            /* Extraigo la informacion basica del usuario que se loguea por primera vez para insertarlo en la tabla user */
            $detalleUser = mb_convert_encoding($usuario, 'UTF-8', 'UTF-8');

            /* Se encuentra en el directorio activo */
=======
        header("Content-Type: text/html; charset=utf-8");

        $user = $request["username"];
        $pass = $request["password"];

        $usuario = $this->mailboxpowerloginrd($user, $pass);
        $usuario = mb_convert_encoding($usuario, 'UTF-8', 'UTF-8');

        if ($usuario != 0) {
            $gruposLDAP = $usuario[0]['memberof'];
            $individual = implode(",", $gruposLDAP);
            $grupos = explode(",", $individual);
            $permisos = $this->validacionPermisosPerApp($grupos, $request->idDesarrollo);
        }


        if (gettype($usuario) == 'array') { //LO ENCUENTRO EN EL DIRECTORIO ACTIVO
>>>>>>> v3
            $user = User::with('roles')->where('email', $request->username)->first(); //Lo busco en la tabla de user
            //return $request->username;
            if ($user) //si existe en el directorio activo y en la tabla user
            {
<<<<<<< HEAD
                if (Hash::check($request['password'], $user->password)) //valido si la contraseña y el usuario son correctos
                {
                    //Actualizo los permisos en la tabla user
                    $rolUser = $this->rolesUser($usuario);
                    $updatePermisos = User::with('roles')->where('email', $request->username)->where('is_directory', 1)->update([
                        'rol' => json_encode($rolUser),
                    ]);

                    //Capturo el usuarios nuevamente
                    $user = User::with('roles')->where('email', $request->username)->first();
                    //Asigno el token al usuario
                    $tokenUser = $user->createToken('Auth Token')->accessToken;
                    $request->session()->regenerate();

                    /* REGISTRO EN BITACORA */
                    Bitacora::create(['ID_APP' => $request["idDesarrollo"],'USER_ACT' => $user["nro_doc"],'ACCION' => 'LOGIN SUCCESS','FECHA' => date('Y-m-d h:i:s'),'USER_EMPRESA' => $user["empresa"]]);

                    return response()->json([
                        "estado" => "1",
                        "user" => $user,
                        "token" => $tokenUser
                    ], 200);
                } else { //Si son incorrectas las credenciales de la tabla user
                    //Actualizo los permisos en la tabla user y la contraseña ya que puede que este actualizada en el directorio activo pero no en la tabla user
                    $rolUser = $this->rolesUser($usuario);
                    if ($user->is_directory == 1) {
                        $updatePermisos = User::with('roles')->where('email', $request->username)->update([
                            'rol' => json_encode($rolUser),
                            'password' => bcrypt($request['password'])
                        ]);
                    } else {
                        $updatePermisos = User::with('roles')->where('email', $request->username)->update([
=======
                if (Hash::check($request['password'], $user->password)) { //valido si la contraseña y el usuario son correctos
                    /* VALIDAR PERMISOS DEL USARIO POR APLICATIVO */
                    
                    if ($permisos->permiso == 1) {

                        User::with('roles')->where('email', $request->username)->where('is_directory', 1)->update([
                            'rol' => json_encode($permisos->roles),
                        ]);
                        
                        $tokenUser = $user->createToken('Auth Token')->accessToken;
                        $request->session()->regenerate();
                        
                        /* REGISTRO EN BITACORA */
                        Bitacora::create(['ID_APP' => $request["idDesarrollo"],'USER_ACT' => $user["nro_doc"],'ACCION' => 'LOGIN SUCCESS','FECHA' => date('Y-m-d h:i:s'),'USER_EMPRESA' => $user["empresa"]]);
                        
                        return response()->json([
                            "estado"    => $permisos->permiso,
                            "modulos"   => $permisos->modulos,
                            "token"     => isset($tokenUser) ? $tokenUser : null
                        ], 200);
                    }else{
                        return response()->json([
                            "estado"    => 3,
                            "token"     => null,
                            "modulos"   => null,
                        ], 200);
                    }

                } else {
                    
                    if ($user->is_directory == 1) {
                        User::with('roles')->where('email', $request->username)->where('is_directory', 1)->update([
                            'rol' => json_encode($permisos->roles),
                            'password' => bcrypt($request['password'])
                        ]);
                    } else {
                        User::with('roles')->where('email', $request->username)->update([
>>>>>>> v3
                            'password' => bcrypt($request['password'])
                        ]);
                    }

<<<<<<< HEAD
                    //Capturo el usuarios nuevamente
                    $user = User::with('roles')->where('email', $request->username)->first();
                    //Asigno el token al usuario
                    $tokenUser = $user->createToken('Auth Token')->accessToken;
                    $request->session()->regenerate();
                    /* REGISTRO EN BITACORA */
                    Bitacora::create(['ID_APP' => $request["idDesarrollo"],'USER_ACT' => $user["nro_doc"],'ACCION' => 'LOGIN SUCCESS','FECHA' => date('Y-m-d h:i:s'),'USER_EMPRESA' => $user["empresa"]]);

                    return response()->json([
                        "estado" => "1",
                        "user" => $user,
                        "token" => $tokenUser
                    ], 200);
                }
            } else { //Si no existe en la tabla usuarios pero si en el directorio activo

                /* funcion para obtener los roles disponibles del usuario segun el LDAP */
                $rolUser = $this->rolesUser($usuario);

                $pass = bcrypt($request['password']);

                User::create([
                    'nro_doc'       => $detalleUser[0]['wwwhomepage'][0],
                    'name'          => $detalleUser[0]['givenname'][0],
                    'last_name'     => $detalleUser[0]['sn'][0],
                    'email'         => $detalleUser[0]['samaccountname'][0],
                    'correo'        => $detalleUser[0]['mail'][0],
                    'rol'           => json_encode($rolUser),
                    'cargo'         => $detalleUser[0]['description'][0],
                    'empresa'       => $detalleUser[0]['physicaldeliveryofficename'][0],
=======
                }
            } else {
                
                $pass = bcrypt($request['password']);

                User::create([
                    'nro_doc'       => $usuario[0]['wwwhomepage'][0],
                    'name'          => $usuario[0]['givenname'][0],
                    'last_name'     => $usuario[0]['sn'][0],
                    'email'         => $usuario[0]['samaccountname'][0],
                    'correo'        => $usuario[0]['mail'][0],
                    'rol'           => json_encode($permisos->roles),
                    'cargo'         => $usuario[0]['description'][0],
                    'empresa'       => $usuario[0]['physicaldeliveryofficename'][0],
>>>>>>> v3
                    'password'      => $pass,
                    'is_director'   => 1,
                    'estado'        => 1
                ]);

<<<<<<< HEAD
                //Capturo el usuarios nuevamente
                $user = User::with('roles')->where('email', $request->username)->first();

                //Asigno el token al usuario
                $tokenUser = $user->createToken('Auth Token')->accessToken;
                $request->session()->regenerate();
                /* REGISTRO EN BITACORA */
                Bitacora::create(['ID_APP' => $request["idDesarrollo"],'USER_ACT' => $user["nro_doc"],'ACCION' => 'LOGIN SUCCESS','FECHA' => date('Y-m-d h:i:s'),'USER_EMPRESA' => $user["empresa"]]);

                return response()->json([
                    "estado" => "4",
                    "user" => $user,
                    "token" => $tokenUser
                ], 200);
            }
        } else if (gettype($usuario) == 'string') //SI NO LO ENCUENTRO EN EL DIRECTORIO ACTIVO
        {
            /* return "2"; */
            $user = User::with('roles')->where('email', $request->username)->first(); //Lo busco en la tabla de user
            if ($user) //si existe en la tabla user
            {
                if (Hash::check($request['password'], $user->password)) //valido si la contraseña y el usuario son correctos
                {
                    $tokenUser = $user->createToken('Auth Token')->accessToken;
                    $request->session()->regenerate();
                    /* REGISTRO EN BITACORA */
                    Bitacora::create(['ID_APP' => $request["idDesarrollo"],'USER_ACT' => $user["nro_doc"],'ACCION' => 'LOGIN SUCCESS','FECHA' => date('Y-m-d h:i:s'),'USER_EMPRESA' => $user["empresa"]]);

                    return response()->json([
                        "estado" => "1",
                        "user" => $user,
                        "token" => $tokenUser
                    ], 200);
                } else {
                    return response()->json([
                        "estado" => "2",
                        "user" => null,
                        "token" => null
                    ], 200);
                }
            } else { //Si no esta en la tabla user
                return response()->json([
                    "estado" => "3",
                    "user" => null,
                    "token" => null
                ], 200);
            }
        } else {
            /* return "3"; */
            return response()->json([
                "estado" => "3",
                "user" => null,
                "token" => null
            ], 200);
        }
    }

    public function rolesUser($usuario)
    {
        $detalleUser = mb_convert_encoding($usuario, 'UTF-8', 'UTF-8');
        $gruposLDAP = $detalleUser[0]['memberof'];
        $a = implode(",", $gruposLDAP);
        $grupos = explode(",", $a);
        $rolUser = [];
        foreach ($grupos as $key => $value) {
            if ($key != 'count') {

                /* ROLES DE HVSEDES */
                if ($value == 'CN=APD_AGSuperAdmin') {
                    array_push($rolUser, 1);
                } elseif ($value == 'CN=APD_AGAdministrador') {
                    array_push($rolUser, 2);
                } elseif ($value == 'CN=APD_HVConsultor') {
                    array_push($rolUser, 3);
                } elseif ($value == 'CN=APD_HVSupervisor') {
                    array_push($rolUser, 4);
                } elseif ($value == 'CN=APD_HVAdmServHab') {
                    array_push($rolUser, 5);
                } elseif ($value == 'CN=APD_HVAdmInfra') {
                    array_push($rolUser, 6);
                } elseif ($value == 'CN=APD_HVTalentoHumo') {
                    array_push($rolUser, 7);
                } elseif ($value == 'CN=APD_HVAdmTH') {
                    array_push($rolUser, 8);
                }

                /* ROLES DE FACTUCONTROL */
                elseif ($value == 'CN=APD_RadicadorFactu') {
                    array_push($rolUser, 9);
                } elseif ($value == 'CN=APD_CoordinadorFactu') {
                    array_push($rolUser, 10);
                } elseif ($value == 'CN=APD_TesoreriaFactu') {
                    array_push($rolUser, 11);
                } elseif ($value == 'CN=APD_Atencion') {
                    array_push($rolUser, 12);
                } elseif ($value == 'CN=APD_AdminFac') {
                    array_push($rolUser, 14);
                }

                /* ROLES DE CITOLOGÍAS */
                elseif ($value == 'CN=APD_ProfCitologias') {
                    array_push($rolUser, 13);
                }

                /* ROLES DE MAMITAS */
                elseif ($value == 'CN=APD_Mamitas2_0') {
                    array_push($rolUser, 15);
                }

                /* ROLES DE RESIDUOS */
                elseif ($value == 'CN=APD_SupAdmResiduos') {
                    array_push($rolUser, 16);
                }
                elseif ($value == 'CN=APD_AdmResiduos') {
                    array_push($rolUser, 17);
                }
                elseif ($value == 'CN=APD_UsersResiduos') {
                    array_push($rolUser, 18);
                }

                /* ROLES DE ESCALAS DE REHABILITACION */
                elseif ($value == 'CN=APD_UsersEscalas') {
                    array_push($rolUser, 21);
                } elseif ($value == 'CN=APD_AdminEscalas') {
                    array_push($rolUser, 22);
                }

                /* ROLES DE GESTIÓN DE PACIENTES*/
                elseif ($value == 'CN=APD_MedGP') {
                    array_push($rolUser, 23);
                } elseif ($value == 'CN=APD_FrontGP') {
                    array_push($rolUser, 24);
                }elseif ($value == 'CN=APD_AdminGP') {
                    array_push($rolUser, 25);
                }

                /* ROLES DE CONSENTIMIENTO INFORMADO*/
                elseif ($value == 'CN=APD_UsersConsentimientos') {
                    array_push($rolUser, 30);
                }
                /* ROLES DE LINEA ETICA*/
                elseif ($value == 'CN=APD_LineaEtica') {
                    array_push($rolUser, 40);
                }


            }
        }
        return $rolUser;
=======
                $user = User::with('roles')->where('email', $request->username)->first();

                if ($permisos->permiso == 1) {

                    User::with('roles')->where('email', $request->username)->where('is_directory', 1)->update([
                        'rol' => json_encode($permisos->roles),
                    ]);
                    
                    $tokenUser = $user->createToken('Auth Token')->accessToken;
                    $request->session()->regenerate();
                    
                    /* REGISTRO EN BITACORA */
                    Bitacora::create(['ID_APP' => $request["idDesarrollo"],'USER_ACT' => $user["nro_doc"],'ACCION' => 'LOGIN SUCCESS','FECHA' => date('Y-m-d h:i:s'),'USER_EMPRESA' => $user["empresa"]]);
                    
                    return response()->json([
                        "estado"    => $permisos->permiso,
                        "modulos"   => $permisos->modulos,
                        "token"     => isset($tokenUser) ? $tokenUser : null
                    ], 200);
                }else{
                    return response()->json([
                        "estado"    => 3,
                        "token"     => null,
                        "modulos"   => null,
                    ], 200);
                }

                
            }


            

        }

        else if (gettype($usuario) == 'string') { //NO LO ENCONTRO EN EL DIRECTORIO ACTIVO
            $user = User::with('roles')->where('email', $request->username)->first(); //Lo busco en la tabla de user
            
            if ($user) //si existe en la tabla user
            {
                $credentials = ['email' => $request->username, 'password' => $request->password];

                if (Auth::attempt($credentials)) { //valido si la contraseña y el usuario son correctos
                    
                    /* $tokenUser = $user->createToken('Auth Token')->accessToken;
                    $request->session()->regenerate(); */
                    
                    /* REGISTRO EN BITACORA */
                    Bitacora::create(['ID_APP' => $request["idDesarrollo"],'USER_ACT' => $user["nro_doc"],'ACCION' => 'LOGIN SUCCESS','FECHA' => date('Y-m-d h:i:s'),'USER_EMPRESA' => $user["empresa"]]);
                    
                    return response()->json([
                        "estado"    => 3,
                        "modulos"   => null,
                        "token"     => null
                    ], 200);
                    
                }
                else {
                    return response()->json([
                        "estado" => 2,
                        "user" => null,
                        "token" => null
                    ], 200);
                }
            }
        }
        
>>>>>>> v3
    }

    public function logout(Request $request)
    {

<<<<<<< HEAD

=======
>>>>>>> v3
        if ($request["idApp"] == 10050) {

            Consultorios::where('doc_prof', $request->user()->nro_doc)->update([
                'doc_prof' => null
            ]);

            Medicos::where('docMedico', $request->user()->nro_doc)->update([
                'estado'    => 0,
                'cupo'      => 0,
                'unidad'    => null
            ]);
        }

        /* REGISTRO EN BITACORA */
        Bitacora::create(['ID_APP' => $request["idApp"],'USER_ACT' => $request->user()->nro_doc,'ACCION' => 'LOGOUT SUCCESS','FECHA' => date('Y-m-d h:i:s'),'USER_EMPRESA' => $request->user()->empresa]);

        $request->user()->tokens()->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();




        return response()->json(["message" => "Sesion Finalizada"]);
    }

    public function user(Request $request)
    {
            $user = Auth::user();

            $u = (object) [
                'user' => $user
              ];

            return $u;
    }

<<<<<<< HEAD
    public function mailboxpowerloginrd($user,$pass){
=======
    public function mailboxpowerloginrd($user,$pass)
    {
>>>>>>> v3

        define('DOMINIO', 'virreysolisips.loc');
	    define('DN', 'dc=virreysolisips,dc=loc');

        $ldaprdn = trim($user).'@'.DOMINIO;
        $ldappass = trim($pass);
        $ds = DOMINIO;
        $dn = DN;
        $puertoldap = 389;
        $ldapconn = ldap_connect($ds,$puertoldap);
          ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION,3);
          ldap_set_option($ldapconn, LDAP_OPT_REFERRALS,0);
          $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);
          if ($ldapbind){
            $filter="(|(SAMAccountName=".trim($user)."))";
            $fields = array("*");
            $sr = @ldap_search($ldapconn, $dn, $filter, $fields);
            $info = @ldap_get_entries($ldapconn, $sr);
            $array = $info;
          }else{
                $array=0;
          }
        ldap_close($ldapconn);
        return $array;
    }

<<<<<<< HEAD
=======
    public function validacionPermisosPerApp($grupos, $app)
    {
        
        $desarrollos = config('app.aplicativos');
        
        foreach ($desarrollos as $soft) {

            if ($soft['id'] == $app) {

                $nombre = trim("App\Permisos\ ").$soft['estrategia'];
                $instancia = new $nombre;
                
                return $instancia->validarRol($grupos, $app);
            }
            

        }



        
    }

>>>>>>> v3

}
