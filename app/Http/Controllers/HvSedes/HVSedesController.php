<?php

namespace App\Http\Controllers\HvSedes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Imports\PlantaImport;
use App\Models\AdminGlobal\Modulos;
use App\Models\Hvsedes\Grupos;
use App\Models\Hvsedes\Servicios;
use App\Models\Hvsedes\Sucursal\Estado;
use App\Models\Hvsedes\Sucursal\Sucursal;
use App\Models\Hvsedes\Sucursal\Unidad;
use App\Models\Hvsedes\Sucursal\UniUnidad;
use App\Models\Hvsedes\Sucursal\SedSede;
use App\Models\Hvsedes\ServHab\ServicioHabilitado;
use App\Models\Hvsedes\Infraestructura\Area;
use App\Models\Hvsedes\Infraestructura\ServInfra;
use App\Models\Hvsedes\TalentoHumano\Cargo;
use App\Models\Hvsedes\TalentoHumano\CargosColab;
use App\Models\Hvsedes\TalentoHumano\Colaboradores;
use App\Models\Hvsedes\TalentoHumano\Eps;
use App\RolUserMod;
use App\User;
use DB;
use Directory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Symfony\Component\Console\Input\Input;

use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\ExcelMatch;
use PhpParser\Node\Stmt\Return_;
use Maatwebsite\Excel\Facades\Excel;


class HVSedesController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

     /**
     * funcion encargada de retornar todos las sucursales disponibles en bd para retornarlas a la vista.
     *
     * @return "todas las sucursales por departamento"    => $sucursales
     */
    public function getSucursales(Request $request)
    {
        $sucursales = Sucursal::select('SUC_DEPARTAMENTO')->distinct()->get();
        return response()->json(["sucursales" => $sucursales, "status" => "ok"], 200);
    }

     /**
     * funcion encargada de retornar las unidades disponibles segun la sucursal recibida por get para retornarlas a la vista.
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
        ], 200);
    }

     /**
     * funcion encargada de retornar los servicios habilitados, los servicios por unidad, el consolidado de la sede, nombre de la sucursal
     * nombre de la unidad, el detalle de la sede. (Todo esto es obtenido desde los SP que filtran por sucursal y sede)
     *
     * @return "unidades segun la sucursal seleccionada"    => $unidades,
     * @return "Conteo de servicios por unidades"           => $countUnidades,
     * @return "nombre de la unidad"                        => $data['suc'],
     * @return "procedimiento almacenado"                    => $servPorUnidad,
     * @return 'todos los servicios por unidades'           => $servPorUnidadAg,
     */
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
        $consolidado        = DB::table('HOJADEVIDASEDES.SED_SEDE AS A')->selectRaw('A.SED_CODIGO_HABILITACION_SEDE, A.SED_NOMBRE_SEDE, C.AXU_NOMBRE_AREA, COUNT(C.AXU_CODIGO_AREA) as AREA')
        ->join('HOJADEVIDASEDES.CXU_CAPACIDAD_X_UNIDAD AS B', "A.SED_CODIGO_HABILITACION_SEDE", "=", "B.SED_CODIGO_HABILITACION_SEDE")
        ->where('A.SED_NOMBRE_SEDE', $data['nombUnidad'])
        ->join('HOJADEVIDASEDES.AXU_AREA_X_UNIDAD AS C', "C.AXU_CODIGO_AREA", "=", "B.AXU_CODIGO_AREA")
        ->groupBy("A.SED_CODIGO_HABILITACION_SEDE", "A.SED_NOMBRE_SEDE", "C.AXU_CODIGO_AREA","C.AXU_NOMBRE_AREA")
        ->orderBy("A.SED_NOMBRE_SEDE")
        ->get();
        $detalleSed   = SedSede::where('SED_NOMBRE_SEDE', $data['nombUnidad'])->first();


        return response()->json([
            "servHab"           => $sha,
            'servPorUnidad'     => $servPorUnidad,
            "servPorUnidadAg"   => $servPorUnidadAg,
            "consolidado"       => $consolidado,
            "nombSuc"           => $data['nombSuc'],
            "nombUnidad"        => $data['nombUnidad'],
            "detalleSed"         => $detalleSed,
            "status"            => "ok"
        ], 200);
    }

    /* public function getMenu(Request $request)
    {
        $menu = DB::table('Opcion')->select('*')->get();
        return response()->json(["menu" => $menu, "status" => "ok"], 200);
    } */

     /**
     * funcion encargada listar la Infraestructura de una sede segun la sucursal seleccionada
     *
     * @return "Infraestructura por sede"    => $list
     * @return "Consultorios en uso por sede"    => $list2
     */
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
                ], 200);
            }
            if ($data['opc'] == "TalentoHumano") {
                $sede = SedSede::where('SED_NOMBRE_SEDE', $request['nombUnidad'])->first();
                $planta = Colaboradores::with([
                    'eps',
                    'cargos' => function($q){
                        return $q->with('cargoDetalle');
                    }
                ])->where('ID_HAB_SEDE', $sede->SED_CODIGO_HABILITACION_SEDE)->where('ESTADO', 1)->get();


            $consolidado = Colaboradores::selectRaw('CCC.COD_CARGO,CCC.NOMBRE_CARGO,COUNT(CCC.COD_CARGO) as CANT_CARGO')
                ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
                ->join('dbo.CARGOS AS CCC', 'CCC.COD_CARGO', '=', 'CC.COD_CARGO')
                ->where('ID_HAB_SEDE', $sede->SED_CODIGO_HABILITACION_SEDE)
                ->where('HOJADEVIDASEDES.COLABORADORES.ESTADO', 1)
                ->groupBy('CCC.COD_CARGO', 'CCC.NOMBRE_CARGO')
                ->get();

                return response()->json([
                    "list" => $planta,
                    "list2" => $consolidado,
                ]);
            }
        } else {
            $list = null;
        }

        return response()->json(["list" => $list, "status" => "ok"], 200);
    }

     /**
     * funcion encargada de retornar los grupos para visualizarlos en Servicios Habilitados
     *
     * @return "Todos los grupos de la BD" => $grupos
     */
    public function getGrupos(Request $request)
    {
        $grupos = Grupos::all();
        return response()->json(["grupos" => $grupos, "status" => "ok"], 200);
    }

     /**
     * funcion encargada de almacenar/crear los grupos para visualizarlos en Servicios Habilitados
     *
     * @return "Todos los grupos de la BD" => $grupos
     */
    public function saveGrupo(Request $request)
    {
        $insert = Grupos::create([
            "GRU_NOMBRE_GRUPO_SERVICIO" => strtoupper($request['nomb_grupo']),
        ]);

        $grupos = Grupos::all();

        return response()->json(["grupos" => $grupos, "status" => "ok"], 200);
    }

     /**
     * funcion encargada de retornar los Servicios para visualizarlos en Servicios Habilitados
     *
     * @return "Todos los Servicios de la BD" => $servicios
     */
    public function getServicios(Request $request)
    {
        $servicios = Servicios::all();
        return response()->json(["servicios" => $servicios, "status" => "ok"], 200);
    }

     /**
     * funcion encargada de almacenar/crear los Servicios para visualizarlos en Servicios Habilitados
     *
     * @return "Todos los Servicio de la BD" => $servicios
     */
    public function saveServicio(Request $request)
    {
        $insert = Servicios::create([
            "SER_CODIGO_SERVICIO" => $request['cod_serv'],
            "SER_NOMBRE_SERVICIO" => strtoupper($request['nomb_serv']),
        ]);

        $servicios = Servicios::all();

        return response()->json(["servicios" => $servicios, "status" => "ok"], 200);
    }


     /**
     * funcion encargada de retornar las sedes
     *
     * @return "Todas las Sedes de la BD" => $sedes
     */
    public function getSed(Request $request)
    {
        $sedes = SedSede::all();
        return response()->json(["sedes" => $sedes, "status" => "ok"], 200);
    }

     /**
     * funcion encargada de almacenar/crear la vinculacion/asociacion de los grupos y servicios
     *
     * @return "Todos los servicios habilitados" => $servHab
     */
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

        return response()->json(["servHab" => $servHab, "status" => "ok"], 200);
    }

     /**
     * funcion encargada de retornar los servicios habilitados
     *
     * @return "Todos los servicios habilitados" => $servHab
     */
    public function getServHabs(Request $request)
    {
        $servHabs =  DB::table('HOJADEVIDASEDES.SHA_SERVICIOS_HABILITADOS')->get();
        return response()->json(["servHabs" => $servHabs, "status" => "ok"], 200);
    }

    /* public function getData(Request $request)
    {
        $item = DB::table('HOJADEVIDASEDES.SHA_SERVICIOS_HABILITADOS AS SV')
            ->selectRaw('S.SED_NOMBRE_SEDE, COUNT(SV.SER_CODIGO_SERVICIO) as CantidadServ')
            ->join('HOJADEVIDASEDES.SED_SEDE AS S', 'S.SED_CODIGO_HABILITACION_SEDE', '=', 'SV.SED_CODIGO_HABILITACION_SEDE')
            ->join('HOJADEVIDASEDES.SER_SERVICIOS AS SS', 'SS.SER_CODIGO_SERVICIO', '=', 'SV.SER_CODIGO_SERVICIO')
            ->groupBy('SV.SED_CODIGO_HABILITACION_SEDE', 'S.SED_NOMBRE_SEDE')
            ->orderBy('CantidadServ', 'DESC')
            ->get();
        return response()->json(["item" => $item, "status" => "ok"], 200);
    } */

     /**
     * funcion encargada de almacenar/crear las sedes
     *
     * @return "Todas las sedes de la BD" => $sedes
     */
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

    /**
     * funcion encargada de retornar las sedes
     *
     * @return "Todas las sedes de la BD" => $sedes
     */
    public function consultaSedes(Request $request)
    {
        $sedes = SedSede::all();
        $sedes->load('sucursal');
        return response()->json(["sedes" => $sedes, "status" => "ok"], 200);
    }

    /**
     * funcion encargada de retornar los estados disponibles desde la BD
     *
     * @return "Todas los estados de la BD" => $sedes
     */
    public function estado(Request $request)
    {
        $estado = Estado::all();
        return response()->json(["estado" => $estado, "status" => "ok"], 200);
    }

    /**
     * funcion encargada de modificar/actualizar una sede en especifico
     *
     * @return "Todas las sedes de la BD" => $sedes
     */
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

    /**
     * funcion encargada de retornar las sedes por sucursal y los servicios por sucursal (Graficos)
     *
     * @return "Sedes por Sucursal" => $sedesPorSucursal
     * @return "Servicios por Sucursal" => $serviciosPorSucursal
     */
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


    /**
     * funcion encargada de retornar las sucursal con sus respectivas sedes
     *
     * @return "Sucursales con sedes" => $sucursales
     */
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

    /* public function getUserFilter(Request $request)
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

    } */

    /* public function getPermisosUser(Request $request)
    {
        $data = $request->all();
        $modulos = Modulos::where('desarrollo_id', $data['idDesarrollo'])->get();
        $userPermisos = RolUserMod::where('user_id', $data['user'])->get();

        return response()->json([
            'modulos' => $modulos,
            'permisos' => $userPermisos
        ], 200);
    } */

    /* public function savePermisosUser(Request $request)
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
    } */

    /**
     * funcion encargada de almacenar la edicion de los grupos de los servicios habilitados
     *
     * @return "Todos los grupos de la BD" => $grupos
     */
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

    /**
     * funcion encargada de almacenar la edicion de los servicios de los servicios habilitados
     *
     * @return "Todos los servicios de la BD" => $servicios
     */
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

    /**
     * funcion encargada de retornar los grupos por sede de los servicios habilitados
     *
     * @return "Todos los grupos segun la sede" => $grupos
     */
    public function getGruposPorSede(Request $request)
    {
        $data = $request->all();
        $grupos = ServicioHabilitado::selectRaw('GRU_CODIGO_GRUPO')->where('SED_CODIGO_HABILITACION_SEDE', $data["SED_CODIGO_HABILITACION_SEDE"])->groupBy('GRU_CODIGO_GRUPO')->pluck('GRU_CODIGO_GRUPO');
        $grup = Grupos::whereIn("GRU_CODIGO_GRUPO", $grupos)->get();

        return response()->json(["grupos" => $grup, "status" => "ok"], 200);
    }

    /**
     * funcion encargada de modificar el estado de los servicios habilitados (Habilitar o Deshabilitar)
     *
     * @return "Todos los servicios habilitados segun el SP definido" => $servHab
     */
    public function cambiarEstadoSH(Request $request)
    {
        $change = $request['EST_CODIGO_ESTADO']  == 'A' ? 'I' : 'A';
        $shEdit = DB::table('HOJADEVIDASEDES.SHA_SERVICIOS_HABILITADOS')->where('SHA_ID', $request['SHA_ID'])->update([
            'EST_CODIGO_ESTADO' => $change
        ]);

        $sha = DB::select('exec HOJADEVIDASEDES.SP_SERVICOS_HABILITADOS_X_SEDE "' . $request['NOMBRE_SEDE'] . '"');

        return response()->json([
            "servHab" => $sha
        ], 200);


    }

    /**
     * funcion encargada de almacenar/crear las areas de Infraestructura
     *
     * @return "Todas las areas de la BD" => $areas
     */
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

    /**
     * funcion encargada de retornar las areas de Infraestructura
     *
     * @return "Todas las areas de la BD" => $areas
     */
    public function getAreas(Request $request)
    {
        $areas = Area::all();

        return response()->json([
            'areas' => $areas,
        ], 200);
    }

    /**
     * funcion encargada de almacenar la edicion de las areas de Infraestructura
     *
     * @return "Todas las areas de la BD" => $areas
     */
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

    /**
     * funcion encargada de almacenar los servicios de Infraestructura
     *
     * @return "Todos los servicios de Infraestructura de la BD" => $servicios
     */
    public function saveServicioInfra(Request $request)
    {
        $insert = ServInfra::create([
            "SXA_NOMBRE_SERVICIO" => strtoupper($request['nomb_serv']),
        ]);

        $servicios = ServInfra::all();

        return response()->json(["servicios" => $servicios], 200);
    }

    /**
     * funcion encargada de retornar los servicios de Infraestructura
     *
     * @return "Todos los servicios de Infraestructura de la BD" => $servicios
     */
    public function getServiciosInfra(Request $request)
    {
        $servicios = ServInfra::all();
        return response()->json(["servicios" => $servicios], 200);
    }

    /**
     * funcion encargada de almacenar la edicion de los servicios de Infraestructura
     *
     * @return "Todos los servicios de Infraestructura de la BD" => $servicios
     */
    public function saveEditServicioInfra(Request $request)
    {
        $data = $request->all();

        $serv = ServInfra::where('SXA_CODIGO_SERVICIO', $data['item']['SXA_CODIGO_SERVICIO'])->update([
            'SXA_NOMBRE_SERVICIO' => $data['item']['SXA_NOMBRE_SERVICIO']
        ]);

        $servicios = ServInfra::all();

        return response()->json(["servicios" => $servicios], 200);
    }

    /**
     * funcion encargada de almacenar las unidades de Infraestructura
     *
     * @return "Todas las unidades de Infraestructura de la BD" => $servicios
     */
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

    /**
     * funcion encargada de retornar los tipos de unidad de Infraestructura
     *
     * @return "Todos los tipos de unidad de Infraestructura de la BD" => $tiposUnidad
     */
    public function getTiposUnidad(Request $request)
    {
        $tiposUnidad = DB::table('HOJADEVIDASEDES.SXA_TIPO_X_UNIDAD')->selectRaw('TXU_CODIGO_UNIDAD, TXU_NOMBRE_UNIDAD')->get();
        return response()->json(["tiposUnidad" => $tiposUnidad], 200);
    }

    /**
     * funcion encargada de retornar las unidades de Infraestructura
     *
     * @return "Todas las unidades de Infraestructura de la BD" => $unidades
     */
    public function getUnidadesinfra(Request $request)
    {
        $unidades = UniUnidad::all();
        return response()->json(["unidades" => $unidades], 200);
    }

    /**
     * funcion encargada de almacenar la edicion de las unidades de Infraestructura
     *
     * @return "Tods las unidades de Infraestructura de la BD" => $unidades
     */
    public function saveEditUnidad(Request $request)
    {
        $data = $request->all();

        $unidad = UniUnidad::where('UNI_CODIGO', $data['item']['UNI_CODIGO'])->update([
            'UNI_NOMBRE_UNIDAD' => $data['item']['UNI_NOMBRE_UNIDAD']
        ]);

        $unidades = UniUnidad::all();
        return response()->json(["unidades" => $unidades], 200);

    }

    /**
     * funcion encargada de almacenar/crear la vinculacion/asociacion de las areas, unidades y servicios de Infraestructura
     *
     * @return "Devuelve el resultado del Insert 1 o 0, 1: correcto 0: Incorrecto" => $unidades
     */
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

    /**
     * funcion encargada de retornar las unidades segun la sede seleccionada de Infraestructura
     *
     * @return "Todas las unidades de Infraestructura de acuerdo a la sede" => $unidades
     */
    public function getUnidadesPorSede(Request $request)
    {
        $data = $request->all();
        $unidades = UniUnidad::where("SED_CODIGO_HABILITACION_SEDE", $data["sede"])->get();
        return response()->json(["unidades" => $unidades], 200);
    }

    /**
     * funcion encargada de almacenar en el servidor los PDF de documentacion de Infraestructura
     * e insertarlos como referencia en una tabla en la BD ("HOJADEVIDASEDES.PDFs")
     *
     * @return "Lista de PDFs de la BD segun su estado, 1 ACTIVO, 2 INACTIVO" => $pdfs
     */
    public function saveDocumentos(Request $request)
    {
        $data = $request->all();
        $tipoDocumento = '';
        if ($request['tipo'] == 1){ $tipoDocumento = 0; } elseif ($request['tipo'] == 2){ $tipoDocumento = 1; } elseif ($request['tipo'] == 3){ $tipoDocumento = 2; }

        $directorios = Storage::disk('hvsedes_uploads')->directories();
            foreach ($directorios as $directorio) {
                if ($data['sucursal'] == $directorio) {
                    return "existe el directorio";
                }else{
                    $carpetaSuc = Storage::disk('hvsedes_uploads')->makeDirectory($directorios[$tipoDocumento]."/".$data['sucursal']); //crear directorio SUCURSAL
                }

                $suc = Storage::disk('hvsedes_uploads')->directories($directorio);
                foreach ($suc as $sucursal) {
                    if ($directorio."/".$data['sucursal'] == $sucursal) {
                        $carpetaSede = Storage::disk('hvsedes_uploads')->makeDirectory($sucursal."/".$data['sede']); //crear directorio SEDE
                    }
                    $rutas = Storage::disk('hvsedes_uploads')->directories($sucursal);
                    foreach ($rutas as $dirs) {
                        if ($dirs === $directorio."/".$data['sucursal']."/".$data["sede"]) {
                            $dirSelect = $dirs;
                        }
                    }
                }
            }

            if($request->hasFile("files")){
                $files = $request->file("files");
                foreach ($files as $file) {
                    $nombre = $file->getClientOriginalName();
                    if($file->guessExtension() == "pdf"){
                        $rt = public_path("uploads/hvsedes/".$dirSelect."/".$nombre);
                        $rtBD = $dirSelect."/".$nombre;
                        $insertPDF = DB::table("HOJADEVIDASEDES.PDFs")->insert([
                            "NOMBRE" => $file->getClientOriginalName(),
                            "TIPO"   => $tipoDocumento,
                            "URL"    => $rtBD,
                            "SUCURSAL"=> $data['sucursal'],
                            "SEDE"=> $data['sede'],
                            "ESTADO" => 1
                            ]);
                        copy($file, $rt);
                    }
                }
            }

        $pdfs = DB::table("HOJADEVIDASEDES.PDFs")->where('SUCURSAL', $request['sucursal'])->where('SEDE', $request['sede'])->where('ESTADO', 1)->where('TIPO', $tipoDocumento)->get();

        return response()->json(["filesperTipo" => $pdfs], 200);

    }

    /**
     * funcion encargada de retornar los PDF disponibles de documentacion de Infraestructura
     * de acuerdo al tipo seleccionado
     * TIPOS:
     * 0: CERTIFICADOS DE HABILITACION
     * 1: LICENCIA DE EQUIPOS
     * 2: NORMATIVA
     *
     * @return "Lista de PDFs de la BD segun su estado, 1 ACTIVO, 2 INACTIVO" => $pdfs
     */
    public function getFilesPerTipo(Request $request)
    {
        $tipoDocumento = '';
        if ($request['tipo'] == 1){ $tipoDocumento = 0; } elseif ($request['tipo'] == 2){ $tipoDocumento = 1; } elseif ($request['tipo'] == 3){ $tipoDocumento = 2; }
        $pdfs = DB::table("HOJADEVIDASEDES.PDFs")->where('TIPO', $tipoDocumento)->where('SUCURSAL', $request['sucursal'])->where('SEDE', $request['sede'])->where('ESTADO', 1)->get();
        return response()->json(["filesperTipo" => $pdfs], 200);
    }

    /**
     * funcion encargada de inhabilitar los PDFs de documentacion de Infraestructura
     *
     * @return "Lista de PDFs de la BD segun su estado, 1 ACTIVO, 2 INACTIVO" => $pdfs
     */
    public function deletePdf(Request $request)
    {
        if ($request['tipo'] == 1){ $tipoDocumento = 0; } elseif ($request['tipo'] == 2){ $tipoDocumento = 1; } elseif ($request['tipo'] == 3){ $tipoDocumento = 2; }
        $pdf = DB::table("HOJADEVIDASEDES.PDFs")->where('ID', $request['file']['ID'])->update([
            'ESTADO' => 0
        ]);
        $pdfs = DB::table("HOJADEVIDASEDES.PDFs")->where('TIPO', $tipoDocumento)->where('SUCURSAL', $request['sucursal'])->where('SEDE', $request['sede'])->where('ESTADO', 1)->get();
        return response()->json(["filesperTipo" => $pdfs], 200);
    }

    /**
     * funcion encargada de retornar todos los cargos disponibles en Talento Humano
     *
     * @return "Todos los cargos de la BD" => $cargos
     */
    public function getCargos(Request $request)
    {
        $cargos = Cargo::all();
        return response()->json(["cargos" => $cargos], 200);
    }

    /**
     * funcion encargada de retornar todas las EPS disponibles en Talento Humano
     *
     * @return "todas las EPS de la BD" => $eps
     */
    public function getEps(Request $request)
    {
        $eps = Eps::all();
        return response()->json(["eps" => $eps], 200);
    }

    /**
     * funcion encargada de almacenar/crear nuevos colaboradores para Talento Humano
     *
     * @return "todas las EPS de la BD" => $eps
     */
    public function saveColaborador(Request $request)
    {
        $data = $request->all();
        //return $data;
        $newCol = Colaboradores::create([
            "DOC_COLABORADOR"       => $data["documento"],
            "NOMB_COLABORADOR"      => strtoupper($data["nombre_completo"]),
            "GENERO_COLABORADOR"    => $data["sexo"]["id"],
            "COD_EPS"               => $data["eps"]["COD_EPS"],
            "ID_UNIDAD"             => substr($data["sede"]["SED_CODIGO_HABILITACION_SEDE"], -4),
            "ID_HAB_SEDE"           => $data["sede"]["SED_CODIGO_HABILITACION_SEDE"],
            "ESTADO"                => 1
        ]);

        foreach ($data["cargo"] as $cargo) {
            $cargos = CargosColab::create([
                'DOC_COLABORADOR' => $data["documento"],
                'COD_CARGO' => $cargo["COD_CARGO"],
                'HORAS_CONT' => $cargo["horas_cont"],
                'HORAS_LAB' => $cargo["horas_lab"],
                'HORAS_SEMANA' => $cargo["horas_semana"]
            ]);
        }

        $planta = Colaboradores::with(['eps','cargos' => function($q){ return $q->with('cargoDetalle'); }])->where('ID_HAB_SEDE', $request['sed'])->where('ESTADO', 1)->get();

        $plantaActiva = Colaboradores::selectRaw('COUNT(CC.COD_CARGO) as Activos')
        ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
        ->where('HOJADEVIDASEDES.COLABORADORES.ID_HAB_SEDE', $request['sed'])
        ->where('CC.ESTADO', 1)
        ->first();


        $plantaInactiva = Colaboradores::selectRaw('COUNT(CC.COD_CARGO) as Inactivos')
        ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
        ->where('HOJADEVIDASEDES.COLABORADORES.ID_HAB_SEDE', $request['sed'])
        ->where('CC.ESTADO', 0)
        ->first();
        return response()->json([
            "activa" => $plantaActiva,
            "inactiva" => $plantaInactiva,
            "planta" => $planta,
        ], 200);
    }

    /**
     * funcion encargada de retornar la planta segun la sede en Talento Humano
     *
     * @return "Planta de la BD" => $planta
     */
    public function getPlantaAdm(Request $request)
    {
        $planta = Colaboradores::with(['eps','cargos' => function($q){ return $q->with('cargoDetalle'); }])->where('ID_HAB_SEDE', $request['sed'])->where('ESTADO', 1)->get();

        $plantaActiva = Colaboradores::selectRaw('COUNT(CC.COD_CARGO) as Activos')
        ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
        ->where('HOJADEVIDASEDES.COLABORADORES.ID_HAB_SEDE', $request['sed'])
        ->where('CC.ESTADO', 1)
        ->first();

        $plantaInactiva = Colaboradores::selectRaw('COUNT(CC.COD_CARGO) as Inactivos')
        ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
        ->where('HOJADEVIDASEDES.COLABORADORES.ID_HAB_SEDE', $request['sed'])
        ->where('CC.ESTADO', 0)
        ->first();


        return response()->json([
            "activa" => $plantaActiva,
            "inactiva" => $plantaInactiva,
            "planta" => $planta,
        ], 200);

    }

    /**
     * funcion encargada de almacenar la planta de forma masiva segun la sede en Talento Humano
     *
     * @return "Planta de la BD" => $planta
     */
    public function importPlanta(Request $request)
    {
         $request->validate([
            'import_file' => 'required|file|mimes:xls,xlsx'
        ]);

        $path = $request->file('import_file');
        $excelFile = Excel::toCollection(new PlantaImport, $path);

        foreach ($excelFile[0] as $row) {
            Colaboradores::create([
                'DOC_COLABORADOR'       => $row['documento'],
                'NOMB_COLABORADOR'      => $row['apellidos_y_nombres'],
                'GENERO_COLABORADOR'    => $row['genero'],
                'COD_EPS'               => $row['codigo_eps'],
                'ID_UNIDAD'             => $row['unidad'],
                'ID_HAB_SEDE'           => $row['sede']
            ]);

            CargosColab::create([
                'DOC_COLABORADOR'       => $row['documento'],
                'COD_CARGO'             => $row['codigo_cargo'],
                'HORAS_CONT'            => $row['horas_contratadas'],
                'HORAS_LAB'             => $row['horas_laboradas'],
                'HORAS_SEMANA'          => $row['horas_semana']
            ]);
        }

        $planta = Colaboradores::with(['eps','cargos' => function($q){ return $q->with('cargoDetalle'); }])->where('ID_HAB_SEDE', $request['sed'])->where('ESTADO', 1)->get();

        $plantaActiva = Colaboradores::selectRaw('COUNT(CC.COD_CARGO) as Activos')
        ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
        ->where('HOJADEVIDASEDES.COLABORADORES.ID_HAB_SEDE', $request['sed'])
        ->where('CC.ESTADO', 1)
        ->first();


        $plantaInactiva = Colaboradores::selectRaw('COUNT(CC.COD_CARGO) as Inactivos')
        ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
        ->where('HOJADEVIDASEDES.COLABORADORES.ID_HAB_SEDE', $request['sed'])
        ->where('CC.ESTADO', 0)
        ->first();

        return response()->json([
            "activa" => $plantaActiva,
            "inactiva" => $plantaInactiva,
            "planta" => $planta,
        ], 200);
    }

    public function saveEditColaborador(Request $request)
    {
        $data = $request->all();
        //return $data;
        $colEdit = Colaboradores::where('DOC_COLABORADOR', $data['item']['DOC_COLABORADOR'])->update([
            'GENERO_COLABORADOR'    => $data['item']['sexo']['id'],
            'NOMB_COLABORADOR'      => $data['item']['NOMB_COLABORADOR'],
            'COD_EPS'               => $data['item']['eps']['COD_EPS'],
        ]);

        $deleteCargos = CargosColab::where('DOC_COLABORADOR', $data['item']['DOC_COLABORADOR'])->delete();
        //return $deleteCargos;
        //return $data['item']['cargos'];
        foreach ($data['item']['cargos'] as $cargo) {
            //return $cargo;
            $colCargEdit = CargosColab::create([
                'DOC_COLABORADOR'   => $data['item']['DOC_COLABORADOR'],
                'COD_CARGO'         => $cargo['COD_CARGO'],
                'HORAS_CONT'        => $cargo['horas_cont'],
                'HORAS_LAB'         => $cargo['horas_lab'],
                'HORAS_SEMANA'      => $cargo['horas_semana']
            ]);
        }

        $planta = Colaboradores::with(['eps','cargos' => function($q){ return $q->with('cargoDetalle'); }])->where('ID_HAB_SEDE', $data['sede']['SED_CODIGO_HABILITACION_SEDE'])->where('ESTADO', 1)->get();

        $plantaActiva = Colaboradores::selectRaw('COUNT(CC.COD_CARGO) as Activos')
        ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
        ->where('HOJADEVIDASEDES.COLABORADORES.ID_HAB_SEDE', $data['sede']['SED_CODIGO_HABILITACION_SEDE'])
        ->where('CC.ESTADO', 1)
        ->first();


        $plantaInactiva = Colaboradores::selectRaw('COUNT(CC.COD_CARGO) as Inactivos')
        ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
        ->where('HOJADEVIDASEDES.COLABORADORES.ID_HAB_SEDE', $data['sede']['SED_CODIGO_HABILITACION_SEDE'])
        ->where('CC.ESTADO', 0)
        ->first();

        return response()->json([
            "activa" => $plantaActiva,
            "inactiva" => $plantaInactiva,
            "planta" => $planta,
        ], 200);
    }

    public function saveRetiroColaborador(Request $request)
    {
        $data = $request->all();
        $conteoCargos = sizeof($data["item"]["cargos"]);
        if ($conteoCargos == 1) {
            $retirar = Colaboradores::where("DOC_COLABORADOR", $data["item"]["DOC_COLABORADOR"])->update([
                "ESTADO" => 0,
            ]);
        }
        foreach ($data["item"]["cargos"] as $cargo) {
            if ($cargo["cargo_detalle"]["retirar"] == true) {
                $retiro = CargosColab::where("DOC_COLABORADOR", $data["item"]["DOC_COLABORADOR"])->where("COD_CARGO", $cargo["cargo_detalle"]["COD_CARGO"])->update([
                    "ESTADO"       => 0,
                    "FECHA_RETIRO"  => date("Y-m-d"),
                    "MOTIVO_RETIRO" => $data["motivo"]
                ]);
            }
        }
        $planta = Colaboradores::with(['eps','cargos' => function($q){ return $q->with('cargoDetalle'); }])->where('ID_HAB_SEDE', $data['sede']['SED_CODIGO_HABILITACION_SEDE'])->where('ESTADO', 1)->get();

        $plantaActiva = Colaboradores::selectRaw('COUNT(CC.COD_CARGO) as Activos')
        ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
        ->where('HOJADEVIDASEDES.COLABORADORES.ID_HAB_SEDE', $data['sede']['SED_CODIGO_HABILITACION_SEDE'])
        ->where('CC.ESTADO', 1)
        ->first();


        $plantaInactiva = Colaboradores::selectRaw('COUNT(CC.COD_CARGO) as Inactivos')
        ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
        ->where('HOJADEVIDASEDES.COLABORADORES.ID_HAB_SEDE', $data['sede']['SED_CODIGO_HABILITACION_SEDE'])
        ->where('CC.ESTADO', 0)
        ->first();

        return response()->json([
            "activa" => $plantaActiva,
            "inactiva" => $plantaInactiva,
            "planta" => $planta,
        ], 200);
    }

    public function getRetiros(Request $request)
    {
        $allRetiros = Colaboradores::with(['eps','cargos2' => function($q){ return $q->with('cargoDetalle'); }])->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS CC', 'CC.DOC_COLABORADOR', '=', 'HOJADEVIDASEDES.COLABORADORES.DOC_COLABORADOR')
        ->where('HOJADEVIDASEDES.COLABORADORES.ID_HAB_SEDE', $request['sed'])
        ->whereBetween('CC.FECHA_RETIRO', [$request['fechaInicio'], $request['fechaFinal']])
        ->where('CC.ESTADO', 0)
        ->get();

        return response()->json([
            "allRetiros" => $allRetiros
        ], 200);
    }

}
