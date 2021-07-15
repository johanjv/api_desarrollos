<?php

namespace App\Http\Controllers\HvSedes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServHab\ServicioHabilitado;
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
        $servPorUnidadAg    =  DB::table('HOJADEVIDASEDES.UNI_UNIDAD')->selectRaw('TXU_CODIGO_UNIDAD, COUNT(TXU_CODIGO_UNIDAD) as sumaUni')->whereIn('SED_CODIGO_HABILITACION_SEDE', $cod_habilitacion)->groupBy('TXU_CODIGO_UNIDAD')->get();

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
        if (isset($data['nombUnidad'])){
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
        }else{
            $list = null;
        }
        
        return response()->json(["list" => $list, "status" => "ok"]);

    }
}