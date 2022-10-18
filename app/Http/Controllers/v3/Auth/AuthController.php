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

        $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        header("Content-Type: text/html; charset=utf-8");

        $user = $request["username"];
        $pass = $request["password"];

        $usuario = $this->mailboxpowerloginrd($user, $pass);
        $usuario = mb_convert_encoding($usuario, 'UTF-8', 'UTF-8');

        if (gettype($usuario) == 'array') { //LO ENCUENTRO EN EL DIRECTORIO ACTIVO
            $user = User::with('roles')->where('email', $request->username)->first(); //Lo busco en la tabla de user
            //return $request->username;
            if ($user) //si existe en el directorio activo y en la tabla user
            {
                if (Hash::check($request['password'], $user->password)) { //valido si la contraseña y el usuario son correctos
                    /* VALIDAR PERMISOS DEL USARIO POR APLICATIVO */
                    //return $request->idDesarrollo;


                    $gruposLDAP = $usuario[0]['memberof'];
                    $individual = implode(",", $gruposLDAP);
                    $grupos = explode(",", $individual);

                    $permisos = $this->validacionPermisosPerApp($grupos, $request->idDesarrollo);



                    return $permisos;
                }else{
                    return "ACTUALIZAR CONTRASEÑA";
                }
            }else{
                return "TOCA CREARLO";
            }


            

        }

        else if (gettype($usuario) == 'string') { //NO LO ENCONTRO EN EL DIRECTORIO ACTIVO
            $user = User::with('roles')->where('email', $request->username)->first(); //Lo busco en la tabla de user
            if ($user) //si existe en la tabla user
            {
                $credentials = ['email' => $request->username, 'password' => $request->password];

                if (Auth::attempt($credentials)) { //valido si la contraseña y el usuario son correctos
                    return "USER EXTERNO";
                }
                else {
                    return "CREDENCIALES INCORRECTAS";
                }
                

            }
        }
        
    }

    public function logout(Request $request)
    {


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

    public function mailboxpowerloginrd($user,$pass)
    {

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

    public function validacionPermisosPerApp($grupos, $app)
    {
        
        $desarrollos = config('app.aplicativos');
        
        foreach ($desarrollos as $soft) {

            if ($soft['id'] == $app) {

                $nombre = trim("App\Permisos\ ").$soft['estrategia'];
                $instancia = new $nombre;
                
                return $instancia->validarRol($grupos);
            }
            

        }



        
    }


}
