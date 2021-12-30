<?php

namespace App\Http\Controllers\AdminGlobal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminGlobal\Desarrollos;
use App\Models\Hvsedes\Grupos;
use App\Models\Hvsedes\Servicios;
use App\Models\Hvsedes\Sucursal\SedSede;
use App\Models\Hvsedes\Sucursal\Sucursal;
use App\Models\AdminGlobal\Roles;
use App\Models\AdminGlobal\RolUser;
use App\RolUserMod;
use App\Models\AdminGlobal\Modulos;
use App\Models\AdminGlobal\Submodulos;
use App\User;
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
        ], 200);
    }

    public function insertDesarrollo(Request $request)
    {
        $insert = Desarrollos::create([
            "nomb_desarrollo" => $request['nomb_des']
        ]);

        $desarrollos = Desarrollos::all();

        return response()->json([
            "desarrollos" =>  $desarrollos
        ], 200);
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
            $user['newFecha'] = date_format($user['created_at'], "d/m/Y");
            $user['isDirec'] = $user['is_directory'] == 1 ? 'SI' : 'NO';
        }
        return response()->json(["users" => $users], 200);
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
        return response()->json(["roles" => $roles], 200);
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
            $user['newFecha'] = date_format($user['created_at'], "d/m/Y");
            $user['isDirec'] = $user['is_directory'] == 1 ? 'SI' : 'NO';
        }
        return response()->json(["users" => $users], 200);
    }

    public function getModulosPerDesarrollo(Request $request)
    {
        $data = $request->all();
        $modulos = Modulos::where('desarrollo_id', $data['desarrollo']['id'])->get();
        return response()->json(["modulos" => $modulos], 200);
    }

    public function insertModulo(Request $request)
    {
        //convierte en minuscula
        $minusculas = strtolower($request['nomb_modulo']);
        //quita los espacios por un guin -
        $slug = str_replace(" ", "-", $minusculas);

        $insert = Modulos::create([
            "nomb_modulo" => $request['nomb_modulo'],
            "desarrollo_id" => $request['desarrollo_id'],
            "slug" => $slug,
        ]);

        $modulo = Modulos::all();
        $modulo->load('desarrollo');

        return response()->json([
            "modulo" =>  $modulo
        ], 200);
    }

    public function consultaModulos(Request $request)
    {
        $modulos = Modulos::all();
        $modulos->load('desarrollo');
        return response()->json(["modulos" => $modulos, "status" => "ok"]);
    }

    public function insertRol(Request $request)
    {
        $insert = Roles::create([
            "nomb_rol" => $request['nomb_rol']
        ]);

        $roles = Roles::all();

        return response()->json([
            "roles" =>  $roles
        ], 200);
    }

    public function consultaRoles(Request $request)
    {
        $roles = Roles::all();
        return response()->json(["roles" => $roles, "status" => "ok"]);
    }

    public function saveSubmodulo(Request $request)
    {

        $insert = Submodulos::create([
            "nomb_sub_modulo"   => $request['objSubmodulo']['nomb_sub_mod'],
            "modulo_id"         => $request['objSubmodulo']['modulo_id'],
            "slug"              => $this->slugify($request['objSubmodulo']['nomb_sub_mod'])
        ]);

        $subs = Submodulos::all();

        return response()->json(["subs" => $subs, "status" => "ok"]);
    }

    function slugify($string)
    {
        $string = utf8_encode($string);
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $string = preg_replace('/[^a-z0-9- ]/i', '', $string);
        $string = str_replace(' ', '-', $string);
        $string = trim($string, '-');
        $string = strtolower($string);

        if (empty($string)) {
            return 'n-a';
        }

        return $string;
    }

    /* PERMISOS PARA EL SIDEBAR */
    public function getMenuDash(Request $request)
    {
        $countSedes  = SedSede::count();
        $countSucU   = DB::table('HOJADEVIDASEDES.SUC_SUCURSAL')->Join('HOJADEVIDASEDES.SED_SEDE', 'HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', '=', 'HOJADEVIDASEDES.SED_SEDE.SED_CODIGO_DEPARTAMENTO')
            ->select('HOJADEVIDASEDES.SUC_SUCURSAL.SUC_DEPARTAMENTO')->groupBy('HOJADEVIDASEDES.SUC_SUCURSAL.SUC_DEPARTAMENTO')->get();
        $countServ   = Servicios::count();
        $countGrupos = Grupos::count();
        $countDes    = Desarrollos::count();
        $countUser   = User::count();
        $countSuc   = sizeof($countSucU);
        $modulos = $this->getMenuPerGrupos($request['desarrollo_id']);

        return response()->json([
            "modulos"     => $modulos,
            'countSedes'  => $countSedes,
            'countSuc'    => $countSuc,
            'countServ'   => $countServ,
            'countGrupos' => $countGrupos,
            'countDes'    => $countDes,
            'countUser'   => $countUser
        ]);
    }

    /* OBTENER MENU PARA HVSEDES */
    public function getMenuPerGrupos($idDesarrollo)
    {

        $userAct    = Auth::user();
        $permisos   = json_decode($userAct->rol);

        if ($idDesarrollo == config('app.hvSedes')) {
            if (in_array(config('app.superAdmin'), $permisos) || in_array(config('app.administrador'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->orderBy('orden', 'ASC')->get();
                $loads = [
                    'submodulos' => function ($q) {
                        $q->where('id', '!=', '5');
                    }
                ];
                $modulos->load($loads);
            } else if (in_array(config('app.hvConsultor'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->where('id', '!=', '10027')->orderBy('orden', 'ASC')->get();
                $modulos->load("submodulos");
            } else if (in_array(config('app.hvAdmServHab'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->where('id', '=', '21')->orWhere('id', '=', '10027')->orderBy('orden', 'ASC')->get();
                $loads = [
                    'submodulos' => function ($q) {
                        $q->where('id', '=', '2')->orWhere('id', '=', '3');
                    }
                ];
                $modulos->load($loads);
            } else if (in_array(config('app.hvAdmInfra'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->where('id', '=', '22')->orWhere('id', '=', '10027')->orderBy('orden', 'ASC')->get();

                $loads = [
                    'submodulos' => function ($q) {
                        $q->where('id', '=', '9');
                    }
                ];

                $modulos->load($loads);

            } else if (in_array(config('app.HvTalentoHumo'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->where('id', '=', '10030')->orderBy('orden', 'ASC')->get();
            } else if (in_array(config('app.hvAdmTH'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->where('id', '=', '10030')->orWhere('id', '=', '10027')->orderBy('orden', 'ASC')->get();
                $loads = [
                    'submodulos' => function ($q) {
                        $q->where('id', '=', '10');
                    }
                ];
                $modulos->load($loads);
            }
        } else if ($idDesarrollo == config('app.factuControl')) {
            if (in_array(config('app.superAdmin'), $permisos) || in_array(config('app.administrador'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->get();
                $loads = ['submodulos'];
                $modulos->load($loads);
            } else if (in_array(config('app.RadicadorFactu'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->where('id', '=', '10031')->orWhere('id', '=', '10039')->get();
            } else if (in_array(config('app.CoordinadorFactu'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->where('id', '=', '10032')->orWhere('id', '=', '10039')->get();
            } else if (in_array(config('app.TesoreriaFactu'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->where('id', '=', '10032')->orWhere('id', '=', '10039')->get();
            } else if (in_array(config('app.Atencion'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->where('id', '=', '10032')->orWhere('id', '=', '10039')->get();
            }else if (in_array(config('app.AdminFac'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->where('id', '=', '10029')->orWhere('id', '=', '10046')->get();
            }
        } else if ($idDesarrollo == config('app.citologias')) {
            if (in_array(config('app.superAdmin'), $permisos) || in_array(config('app.administrador'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->get();
                $loads = ['submodulos'];
                $modulos->load($loads);
            }else if (in_array(config('app.ProfCitologias'), $permisos)) {
                $modulos    = Modulos::where('desarrollo_id', $idDesarrollo)->get();
                $loads = ['submodulos'];
                $modulos->load($loads);
            }
        } else {
            $modulos = null;
        }
        return $modulos;
    }
}
