<?php

namespace App\Http\Controllers\HvSedes;

use App\Http\Controllers\Controller;
use App\Models\AdminGlobal\Modulos;
use Illuminate\Http\Request;
use App\Models\HvSedes\Grupos;
use App\Models\HvSedes\Servicios;
use App\Models\HvSedes\Sucursal\Estado;
use App\Models\HvSedes\Sucursal\Sucursal;
use App\Models\HvSedes\Sucursal\Unidad;
use App\Models\HvSedes\Sucursal\UniUnidad;
use App\Models\HvSedes\Sucursal\SedSede;
use App\Models\HvSedes\ServHab\ServicioHabilitado;
use App\Models\Hvsedes\Infraestructura\Area;
use App\Models\Hvsedes\Infraestructura\ServInfra;
use App\RolUserMod;
use App\User;
use DB;
use Illuminate\Support\Facades\Auth;

class HVSedesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * funcion encargada de obtener todos las sucursales disponibles en bd para retornarlas a la vista.
     *
     * @return "todas las sucursales por departamento"    => $sucursales
     */
    public function getSucursales(Request $request)
    {
        $sucursales = Sucursal::select('SUC_DEPARTAMENTO')->distinct()->get();
        return response()->json(["sucursales" => $sucursales, "status" => "ok"]);
    }

    /**
     * funcion encargada de obtener las unidades disponibles segun la sucursal recibida por get para retornarlas a la vista.
     *
     * @return "unidades segun la sucursal seleccionada"    => $unidades,
     * @return "Conteo de servicios por unidades"           => $countUnidades,
     * @return "nombre de la unidad"                        => $data['suc'],
     * @return "procedimiento almacenado"                    => $servPorUnidad,
     * @return 'todos los servicios por unidades'           => $servPorUnidadAg,
     */
    public function getUnidades(Request $request)
    {
        $data               = $request->all();
        $sucursales         = Sucursal::where('SUC_DEPARTAMENTO', $data['suc'])->pluck('SUC_CODIGO_DEPARTAMENTO');
        $unidades           = Unidad::where('SED_CODIGO_DEPARTAMENTO', $sucursales)->get();
        $countUnidades      = Unidad::where('SED_CODIGO_DEPARTAMENTO', $sucursales)->count();
        $cod_habilitacion   = SedSede::where('SED_CODIGO_DEPARTAMENTO', $sucursales)->pluck('SED_CODIGO_HABILITACION_SEDE');
        $servPorUnidad      = UniUnidad::whereIn('SED_CODIGO_HABILITACION_SEDE', $cod_habilitacion)->get();
        $servPorUnidadAg    =  DB::table('HOJADEVIDASEDES.UNI_UNIDAD')->selectRaw('TXU_CODIGO_UNIDAD, COUNT(TXU_CODIGO_UNIDAD) as sumaUni')
            ->whereIn('SED_CODIGO_HABILITACION_SEDE', $cod_habilitacion)
            ->where('TXU_CODIGO_UNIDAD', '!=', 'CRA')
            ->where('TXU_CODIGO_UNIDAD', '!=', 'CRI')
            ->where('TXU_CODIGO_UNIDAD', '!=', 'CRO')
            ->groupBy('TXU_CODIGO_UNIDAD')->get();

        $servCR  =  DB::table('HOJADEVIDASEDES.UNI_UNIDAD')->selectRaw('TXU_CODIGO_UNIDAD')->whereIn('SED_CODIGO_HABILITACION_SEDE', $cod_habilitacion)
            ->where('UNI_NOMBRE_UNIDAD', 'like', '%' . 'REHABILITACION' . '%')->count();


        /* UNI_NOMBRE_UNIDAD
        REHABILITACION OLAYA */

        return response()->json([
            "unidades"          => $unidades,
            "countUnidades"     => $countUnidades,
            "nombSuc"           => $data['suc'],
            "servPorUnidad"     => $servPorUnidad,
            'servPorUnidadAg'   => $servPorUnidadAg,
            'servCR'            => $servCR,
            "status"            => "ok",
        ]);
    }

    public function loadData(Request $request)
    {
        $data = $request->all();

        $gruposLDAP = Auth::user()->rol;
        $a = json_decode($gruposLDAP);
        if (in_array(1, $a) || in_array(2, $a)) {
            $sha = DB::select('exec HOJADEVIDASEDES.SP_SERVICOS_HABILITADOS_X_SEDE "' . $data['nombUnidad'] . '"');
        }else{
            $sha = DB::select('exec HOJADEVIDASEDES.SP_SERVICOS_HABILITADOS_X_SEDE_2 "' . $data['nombUnidad'] . '"');
        }

        $cod_habilitacion   = SedSede::where('SED_NOMBRE_SEDE', $data['nombUnidad'])->pluck('SED_CODIGO_HABILITACION_SEDE');
        $servPorUnidad      = UniUnidad::where('SED_CODIGO_HABILITACION_SEDE', $cod_habilitacion)->get();
        $servPorUnidadAg    = DB::table('HOJADEVIDASEDES.UNI_UNIDAD')->selectRaw('TXU_CODIGO_UNIDAD, COUNT(TXU_CODIGO_UNIDAD) as sumaUni')->whereIn('SED_CODIGO_HABILITACION_SEDE', $cod_habilitacion)->groupBy('TXU_CODIGO_UNIDAD')->get();

        return response()->json([
            "servHab"           => $sha,
            'servPorUnidad'     => $servPorUnidad,
            "servPorUnidadAg"   => $servPorUnidadAg,
            "nombSuc"        => $data['nombSuc'],
            "nombUnidad"        => $data['nombUnidad'],
            "status"            => "ok"
        ]);
    }

    public function getMenu(Request $request)
    {
        $menu = DB::table('Opcion')->select('*')->get();
        return response()->json(["menu" => $menu, "status" => "ok"]);
    }

    public function getDataTable(Request $request)
    {
        $data = $request->all();
        if (isset($data['nombUnidad'])) {
            if ($data['opc'] == "Servicios Habilitados") {
                $list    = DB::select('exec HOJADEVIDASEDES.SP_SERVICOS_HABILITADOS_X_SEDE "' . $data['nombUnidad'] . '"');
            }
            if ($data['opc'] == "Infraestructura") {
                $list    = DB::select('exec HOJADEVIDASEDES.SP_INFRAESTRUCTURA_X_SEDE "' . $data['nombUnidad'] . '"');
                $list2    = DB::select('exec HOJADEVIDASEDES.SP_CONSULTORIOS_EN_USO_X_SEDE "' . $data['nombUnidad'] . '"');
                return response()->json([
                    "list" => $list,
                    "list2" => $list2,
                    "status" => "ok"
                ]);
            }
        } else {
            $list = null;
        }

        return response()->json(["list" => $list, "status" => "ok"]);
    }

    public function getGrupos(Request $request)
    {
        $grupos = Grupos::all();
        return response()->json(["grupos" => $grupos, "status" => "ok"]);
    }

    public function saveGrupo(Request $request)
    {
        $insert = Grupos::create([
            "GRU_NOMBRE_GRUPO_SERVICIO" => strtoupper($request['nomb_grupo']),
        ]);

        $grupos = Grupos::all();

        return response()->json(["grupos" => $grupos, "status" => "ok"]);
    }

    public function getServicios(Request $request)
    {
        $servicios = Servicios::all();
        return response()->json(["servicios" => $servicios, "status" => "ok"]);
    }

    public function saveServicio(Request $request)
    {
        $insert = Servicios::create([
            "SER_CODIGO_SERVICIO" => $request['cod_serv'],
            "SER_NOMBRE_SERVICIO" => strtoupper($request['nomb_serv']),
        ]);

        $servicios = Servicios::all();

        return response()->json(["servicios" => $servicios, "status" => "ok"]);
    }

    public function getSed(Request $request)
    {
        $sedes = SedSede::all();
        return response()->json(["sedes" => $sedes, "status" => "ok"]);
    }

    public function saveVinculacion(Request $request)
    {
        $data = $request->all();

        $sede = $data["formData"]["sede"]["SED_CODIGO_HABILITACION_SEDE"];

        foreach ($data["formData"]["grupo"] as $dt) {
            foreach ($dt["servicio"] as $serv) {
                $insert = DB::table('HOJADEVIDASEDES.SHA_SERVICIOS_HABILITADOS')->insert([
                    'SED_CODIGO_HABILITACION_SEDE'  => $sede,
                    'GRU_CODIGO_GRUPO'              => $dt["GRU_CODIGO_GRUPO"],
                    'EST_CODIGO_ESTADO'             => "A",
                    'SER_CODIGO_SERVICIO' => $serv["SER_CODIGO_SERVICIO"],
                    'SHA_FECHA_MODIFICACION'        => "2022-07-06 00:00:00.000"
                ]);
            }
        }

        $servHab =  DB::table('HOJADEVIDASEDES.SHA_SERVICIOS_HABILITADOS')->get();

        return response()->json(["servHab" => $servHab, "status" => "ok"]);
    }

    public function getServHabs(Request $request)
    {
        $servHabs =  DB::table('HOJADEVIDASEDES.SHA_SERVICIOS_HABILITADOS')->get();
        return response()->json(["servHabs" => $servHabs, "status" => "ok"]);
    }

    public function getData(Request $request)
    {
        $item = DB::table('HOJADEVIDASEDES.SHA_SERVICIOS_HABILITADOS AS SV')
            ->selectRaw('S.SED_NOMBRE_SEDE, COUNT(SV.SER_CODIGO_SERVICIO) as CantidadServ')
            ->join('HOJADEVIDASEDES.SED_SEDE AS S', 'S.SED_CODIGO_HABILITACION_SEDE', '=', 'SV.SED_CODIGO_HABILITACION_SEDE')
            ->join('HOJADEVIDASEDES.SER_SERVICIOS AS SS', 'SS.SER_CODIGO_SERVICIO', '=', 'SV.SER_CODIGO_SERVICIO')
            ->groupBy('SV.SED_CODIGO_HABILITACION_SEDE', 'S.SED_NOMBRE_SEDE')
            ->orderBy('CantidadServ', 'DESC')
            ->get();
        return response()->json(["item" => $item, "status" => "ok"]);
    }

    public function insertSedes(Request $request)
    {
        //cuenta los cdigitos que vienen incluyendo el cero
        $cod_sede = strlen($request["cod_sede"]);
        $cod_hab = strlen($request["cod_hab"]);
        //////////////////////////////////////////////////
        if ($cod_sede > 1 && $cod_hab > 9) {
            $insert = SedSede::create([
                'SED_CODIGO_HABILITACION_SEDE'  => $request["cod_hab_sede"],
                'SED_CODIGO_HABILITACION'       => $request["cod_hab"],
                'SED_NOMBRE_SEDE'               => strtoupper($request["nomb_sede"]),
                'SED_CODIGO_SEDE'               => $request["cod_sede"],
                'EST_CODIGO_ESTADO'             => "A",
                'SUC_CODIGO_DANE'               => $request["codsucursal"]["SUC_CODIGO_DANE"],
                'SED_CODIGO_DEPARTAMENTO'       => $request["codsucursal"]["SUC_CODIGO_DEPARTAMENTO"],
            ]);

            $sedes = SedSede::all();
            $sedes->load('sucursal');

            return response()->json([
                "sedes" =>  $sedes
            ], 200);
        } else {
            return response()->json([
                "sedes" =>  false
            ], 200);
        }
    }

    public function getCodSucursales(Request $request)
    {
        $codsucursales = Sucursal::where('SUC_DEPARTAMENTO', $request["codsucursales"])->get();
        return $codsucursales;
    }

    public function consultaSedes(Request $request)
    {
        $sedes = SedSede::all();
        $sedes->load('sucursal');
        return response()->json(["sedes" => $sedes, "status" => "ok"]);
    }

    public function estado(Request $request)
    {
        $estado = Estado::all();
        return response()->json(["estado" => $estado, "status" => "ok"]);
    }

    public function editarSedes(Request $request)
    {
        $data = $request->all();
        $update = SedSede::where("SED_ID", $data["id_edit"])->update([
            'EST_CODIGO_ESTADO'  => $data["estado_edit"]["EST_CODIGO_ESTADO"],
            'SED_NOMBRE_SEDE'    => $data["nomb_sede_edit"],
        ]);

        $update = SedSede::all();

        return response()->json([
            "update" =>  $update
        ], 200);
    }

    public function getSedesPorSucursal()
    {
        $sedesPorSucursal = DB::table('HOJADEVIDASEDES.SED_SEDE AS SED')
            ->selectRaw('SUC.SUC_DEPARTAMENTO, COUNT(SED.SED_NOMBRE_SEDE) as CantidadSedes')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUC', 'SUC.SUC_CODIGO_DANE', '=', 'SED.SUC_CODIGO_DANE')
            ->groupBy('SUC.SUC_DEPARTAMENTO')
            ->orderBy('CantidadSedes', 'DESC')
        ->get();

        $serviciosPorSucursal = DB::table('HOJADEVIDASEDES.SHA_SERVICIOS_HABILITADOS as sh')
            ->selectRaw('suc.SUC_DEPARTAMENTO,COUNT(sh.SER_CODIGO_SERVICIO) as cantServ')
            ->join('HOJADEVIDASEDES.SED_SEDE AS sede', 'sh.SED_CODIGO_HABILITACION_SEDE', '=', 'sede.SED_CODIGO_HABILITACION_SEDE')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS suc', 'suc.SUC_CODIGO_DANE', '=', 'sede.SUC_CODIGO_DANE')
            ->groupBy('SUC.SUC_DEPARTAMENTO')
            ->orderBy('cantServ', 'DESC')
        ->get();

        return response()->json([
            "sedesPorSucursal" =>  $sedesPorSucursal,
            "serviciosPorSucursal" =>  $serviciosPorSucursal
        ], 200);
    }

    public function getSucursalesConSedes()
    {

        $sucursales =  DB::table('HOJADEVIDASEDES.SUC_SUCURSAL AS SUC')
            ->selectRaw('SUC.SUC_DEPARTAMENTO, SUC_CODIGO_DEPARTAMENTO')
            ->join('HOJADEVIDASEDES.SED_SEDE AS SED', 'SED.SED_CODIGO_DEPARTAMENTO', '=', 'SUC.SUC_CODIGO_DEPARTAMENTO')
            ->groupBy('SUC.SUC_DEPARTAMENTO')
            ->groupBy('SUC.SUC_CODIGO_DEPARTAMENTO')
            ->orderBy('SUC.SUC_DEPARTAMENTO')
            ->get();

        return response()->json([
            "sucursales" =>  $sucursales
        ], 200);
    }

    public function getUserFilter(Request $request)
    {
        $data = $request->all();
        $userAct     = Auth::user();
        if ($data['item'] != null) {
            $users = User::with('roles')->where('name', 'LIKE', '%'.$data['item'].'%')->where('email', "!=", $userAct['email'])->get();
        }else{
            $users = User::with('roles')->where('email', "!=", $userAct['email'])->get();
        }

        foreach ($users as $user) {
            $user['newFecha'] = date_format($user['created_at'], "d/m/Y");
            $user['isDirec'] = $user['is_directory'] == 1 ? 'SI' : 'NO';
        }

        return response()->json(["users" => $users], 200);

    }

    public function getPermisosUser(Request $request)
    {
        $data = $request->all();
        $modulos = Modulos::where('desarrollo_id', $data['idDesarrollo'])->get();
        $userPermisos = RolUserMod::where('user_id', $data['user'])->get();

        return response()->json([
            'modulos' => $modulos,
            'permisos' => $userPermisos
        ], 200);
    }

    public function savePermisosUser(Request $request)
    {
        $data = $request->all();
        $userPermisos = RolUserMod::where('user_id', $data['user'])->delete();
        $permisosNuevos = [];

        foreach ($data['permisos'] as $perm) {
            array_push($permisosNuevos, $perm);
        }

        foreach ($permisosNuevos as $pnew) {
            RolUserMod::create([
                'rol_id' => 1,
                'user_id' => $data['user'],
                'modulo_id' => $pnew
            ]);
        }

        $userPermisosNew = RolUserMod::where('user_id', $data['user'])->get();

        return $userPermisosNew;
    }

    public function saveEditgrupo(Request $request)
    {
        $data = $request->all();
        if (isset($data['item']['ESTADO']['EST_ID'])) {
            $grupoEdit = Grupos::where('GRU_CODIGO_GRUPO', $data['item']['GRU_CODIGO_GRUPO'])->update([
                'GRU_NOMBRE_GRUPO_SERVICIO' => $data['item']['GRU_NOMBRE_GRUPO_SERVICIO'],
                'ESTADO' => $data['item']['ESTADO']['EST_ID']
            ]);
        }else{
            $grupoEdit = Grupos::where('GRU_CODIGO_GRUPO', $data['item']['GRU_CODIGO_GRUPO'])->update([
                'GRU_NOMBRE_GRUPO_SERVICIO' => $data['item']['GRU_NOMBRE_GRUPO_SERVICIO']
            ]);
        }

        $grupos = Grupos::all();

        return response()->json([
            'grupos' => $grupos,
        ], 200);
    }

    public function saveEditServicio(Request $request)
    {
        $data = $request->all();
        if (isset($data['item']['ESTADO']['EST_ID'])) {
            $servicioEdit = Servicios::where('SER_CODIGO_SERVICIO', $data['item']['SER_CODIGO_SERVICIO'])->update([
                'SER_NOMBRE_SERVICIO' => $data['item']['SER_NOMBRE_SERVICIO'],
                'ESTADO' => $data['item']['ESTADO']['EST_ID']
            ]);
        }else{
            $servicioEdit = Servicios::where('SER_CODIGO_SERVICIO', $data['item']['SER_CODIGO_SERVICIO'])->update([
                'SER_NOMBRE_SERVICIO' => $data['item']['SER_NOMBRE_SERVICIO']
            ]);
        }

        $servicios = Servicios::all();

        return response()->json([
            'servicios' => $servicios,
        ], 200);
    }

    public function getGruposPorSede(Request $request)
    {
        $data = $request->all();
        $grupos = ServicioHabilitado::selectRaw('GRU_CODIGO_GRUPO')->where('SED_CODIGO_HABILITACION_SEDE', $data["SED_CODIGO_HABILITACION_SEDE"])->groupBy('GRU_CODIGO_GRUPO')->pluck('GRU_CODIGO_GRUPO');
        $grup = Grupos::whereIn("GRU_CODIGO_GRUPO", $grupos)->get();

        return response()->json(["grupos" => $grup, "status" => "ok"]);
    }

    public function cambiarEstadoSH(Request $request)
    {
        $change = $request['EST_CODIGO_ESTADO']  == 'A' ? 'I' : 'A';
        $shEdit = DB::table('HOJADEVIDASEDES.SHA_SERVICIOS_HABILITADOS')->where('SHA_ID', $request['SHA_ID'])->update([
            'EST_CODIGO_ESTADO' => $change
        ]);

        $sha = DB::select('exec HOJADEVIDASEDES.SP_SERVICOS_HABILITADOS_X_SEDE "' . $request['NOMBRE_SEDE'] . '"');

        return response()->json([
            "servHab" => $sha
        ]);


    }

    public function saveArea(Request $request)
    {
        $data = $request->all();

        $area = Area::create([
            'AXU_NOMBRE_AREA' => strtoupper($data['nomb_area'])
        ]);

        $areas = Area::all();

        return response()->json([
            'areas' => $areas,
        ], 200);
    }

    public function getAreas(Request $request)
    {
        $areas = Area::all();

        return response()->json([
            'areas' => $areas,
        ], 200);
    }

    public function saveEditArea(Request $request)
    {
        $data = $request->all();
        $area = Area::where('AXU_CODIGO_AREA', $data['item']['AXU_CODIGO_AREA'])->update([
            'AXU_NOMBRE_AREA' => $data['item']['AXU_NOMBRE_AREA']
        ]);

        $areas = Area::all();

        return response()->json([
            'areas' => $areas,
        ], 200);
    }

    public function saveServicioInfra(Request $request)
    {
        $insert = ServInfra::create([
            "SXA_NOMBRE_SERVICIO" => strtoupper($request['nomb_serv']),
        ]);

        $servicios = ServInfra::all();

        return response()->json(["servicios" => $servicios], 200);
    }

    public function getServiciosInfra(Request $request)
    {
        $servicios = ServInfra::all();
        return response()->json(["servicios" => $servicios], 200);
    }

    public function saveEditServicioInfra(Request $request)
    {
        $data = $request->all();

        $serv = ServInfra::where('SXA_CODIGO_SERVICIO', $data['item']['SXA_CODIGO_SERVICIO'])->update([
            'SXA_NOMBRE_SERVICIO' => $data['item']['SXA_NOMBRE_SERVICIO']
        ]);

        $servicios = ServInfra::all();

        return response()->json(["servicios" => $servicios], 200);
    }

    public function saveUnidad(Request $request)
    {
        $data = $request->all();

        $inserNewUnidad = DB::table("HOJADEVIDASEDES.SXA_TIPO_X_UNIDAD")->insert([
            "TXU_CODIGO_UNIDAD" => strtoupper($data['tipo']),
            "TXU_NOMBRE_UNIDAD" => strtoupper($data['nomb_unidad'])
        ]);

        $insertUnidad = UniUnidad::create([
            'EST_CODIGO_ESTADO' => $data['estado'],
            'UNI_NOMBRE_UNIDAD' => strtoupper($data['nomb_unidad']),
            'SED_CODIGO_HABILITACION_SEDE' => $data['sede'],
            'TXU_CODIGO_UNIDAD' => strtoupper($data['tipo'])
        ]);

        $unidades = UniUnidad::all();
        return response()->json(["unidades" => $unidades], 200);

    }

    public function getTiposUnidad(Request $request)
    {
        $tiposUnidad = DB::table('HOJADEVIDASEDES.SXA_TIPO_X_UNIDAD')->selectRaw('TXU_CODIGO_UNIDAD, TXU_NOMBRE_UNIDAD')->get();
        return response()->json(["tiposUnidad" => $tiposUnidad], 200);
    }

    public function getUnidadesinfra(Request $request)
    {
        $unidades = UniUnidad::all();
        return response()->json(["unidades" => $unidades], 200);
    }

    public function saveEditUnidad(Request $request)
    {
        $data = $request->all();

        $unidad = UniUnidad::where('UNI_CODIGO', $data['item']['UNI_CODIGO'])->update([
            'UNI_NOMBRE_UNIDAD' => $data['item']['UNI_NOMBRE_UNIDAD']
        ]);

        $unidades = UniUnidad::all();
        return response()->json(["unidades" => $unidades], 200);

    }

    public function saveVinculacionInfra(Request $request)
    {
        $data = $request->all();
        $unidad = $data["formData"]["unidad"];

        foreach ($unidad["area"] as $area) {
            foreach ($area["servicio"] as $servicio) {
                $insertAsocia = DB::table('HOJADEVIDASEDES.CXU_CAPACIDAD_X_UNIDAD')->insert([
                    "SED_CODIGO_HABILITACION_SEDE" => $data["formData"]["sede"]["SED_CODIGO_HABILITACION_SEDE"], //sede
                    "TXU_CODIGO_UNIDAD"         => $unidad["TXU_CODIGO_UNIDAD"],
                    "AXU_CODIGO_AREA"           => $area["AXU_CODIGO_AREA"], //area
                    "SXA_CODIGO_SERVICIO"       => $servicio["SXA_CODIGO_SERVICIO"],
                    "CXU_CANTIDAD_AM"           => $servicio["cantidad_am"],
                    "CXU_CANTIDAD_PM"           => $servicio["cantidad_pm"],
                    "CXU_OCUPACION_AM"          => $servicio["ocupado_am"],
                    "CXU_OCUPACION_PM"          => $servicio["ocupado_pm"],
                    "CXU_DISPONIBILIDAD_AM"     => $servicio["cantidad_am"] - $servicio["ocupado_am"],
                    "CXU_DISPONIBILIDAD_PM"     => $servicio["cantidad_pm"] - $servicio["ocupado_pm"],
                    "EST_CODIGO_ESTADO"         => "I",
                    "SXU_FECHA_MODIFICACION"    => now()
                ]);
            }
        }

        return response()->json(["unidades" => $data], 200);

    }

    public function getUnidadesPorSede(Request $request)
    {
        $data = $request->all();
        $unidades = UniUnidad::where("SED_CODIGO_HABILITACION_SEDE", $data["sede"])->get();
        return response()->json(["unidades" => $unidades], 200);

    }
}
