<?php

namespace App\Http\Controllers\HvSedes;

use App\Models\hvsedes\Grupos;
use App\Http\Controllers\Controller;
use App\Models\hvsedes\Servicios;
use Illuminate\Http\Request;
use App\Models\ServHab\ServicioHabilitado;
use App\Models\Sucursal\Estado;
use App\Models\Sucursal\Sucursal;
use App\Models\Sucursal\Unidad;
use App\Models\Sucursal\UniUnidad;
use App\Models\Sucursal\SedSede;
use DB;

class HomeController extends Controller
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
        $data               = $request->all();
        $sha                = DB::select('exec HOJADEVIDASEDES.SP_SERVICOS_HABILITADOS_X_SEDE "' . $data['nombUnidad'] . '"');
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
                'SED_NOMBRE_SEDE'               => $request["nomb_sede"],
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

        return response()->json([
            "sedesPorSucursal" =>  $sedesPorSucursal
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
}
