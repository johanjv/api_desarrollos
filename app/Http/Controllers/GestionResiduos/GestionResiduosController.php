<?php

namespace App\Http\Controllers\GestionResiduos;

use App\Events\ChangeStatusPeriodoEvent;
use App\Http\Controllers\Controller;
use App\Models\Hvsedes\Sucursal\Sucursal;
use App\Models\Residuos\Clasificacion;
use App\Models\Residuos\Residuos;
use App\Models\Residuos\TiempoResiduos;
use App\Models\Residuos\ValidarMes;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GestionResiduosController extends Controller
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

    public function getDataCalendar(Request $request)
    {

        $fechaParaValidar = ValidarMes::with(['registros' => function ($q) use ($request) {
            $q->where('unidad', $request["unidad"]);}])->where('id_mes_ano', $request['idMes'])->where('unidad', $request['unidad'])->count();

            //return $fechaParaValidar;
            if ($fechaParaValidar == 0) {
                if ($request['idMes'] != null) {
                    ValidarMes::create([
                        'id_mes_ano'    => $request['idMes'],
                        'unidad'        => $request['unidad']
                    ]);
                }
            }

        $datosCalendario = ValidarMes::with(['registros' => function ($q) use ($request) {
            $q->where('unidad', $request["unidad"]);}])->where('id_mes_ano', $request['idMes'])->where('unidad', $request['unidad'])->first();

        $periodosDisp = ValidarMes::selectRaw('id_mes_ano')->groupBy('id_mes_ano')->orderby('id_mes_ano', 'DESC')->get();

        return response()->json([
            "datosCalendario"   => $datosCalendario,
            "periodosDisp"      => $periodosDisp,
        ], 200);

    }

    public function getClasif(Request $request)
    {
        $clasificacion = Clasificacion::with([
            'categoria' => function($q) {
                $q->with([
                    'residuos'
                    ]);
                }])->orderBy('id_clasif_residuos', 'DESC')->get();

        return response()->json([
            "clasificacion"    => $clasificacion,
        ], 200);
    }

    public function saveRegistroDiario(Request $request)
    {
        foreach ($request["item"] as $item) {
            TiempoResiduos::create([
                'id_residuo'    => $item["id_residuos"],
                'cantidad'      => $item["valor"],
                'dia'           => $request["dia"],
                'mes'           => $request["mes"],
                'ano'           => $request["ano"],
                'fecha_concat'  => $request["ano"] . "-" . $request["mes"] . "-" . $request["dia"] . "T" . date('h:m:s'),
                'nro_doc_user'  => Auth::user()->nro_doc,
                'unidad'        => $request["unidad"],
                'id_mes_ano'    => $request["idMes"],
            ]);
        }

        $datosCalendario = ValidarMes::with(['registros' => function ($q) use ($request) {
                $q->where('unidad', $request["unidad"]);}])->where('id_mes_ano', $request['idMes'])->where('unidad', $request['unidad'])->first();

        return response()->json([
            "datosCalendario"    => $datosCalendario,
        ], 200);
    }


    public function aRevision(Request $request)
    {
        //return $request->all();
        $estadoEnvio = 0;
        $registros = TiempoResiduos::selectRaw('dia, mes, ano, unidad, id_mes_ano')
        ->where('unidad', $request['unidad'])
        ->where('mes', $request['mes'])
        ->where('ano', $request['year'])
        ->groupBy('dia','mes','ano','unidad', 'id_mes_ano')
        ->get();

        $countKey = 0;
        foreach ($registros as $r) {
            $countKey++;
        }

        $datosCalendario = [];

        if ($countKey == $request["ultDia"]) {

            $datosCalendario = ValidarMes::where('id_mes_ano', $request['item']['id_mes_ano'])->where('unidad', $request['unidad'])->update([
                'aprobado'      => 3,
                'fecha_envio'   => date('Y-m-d h:m:s')
            ]);

            $datosCalendario = ValidarMes::with(['registros' => function ($q) use ($request) {
            $q->where('unidad', $request["unidad"]);}])->where('id_mes_ano', $request['item']['id_mes_ano'])->where('unidad', $request['unidad'])->first();
            $estadoEnvio = 1;
        }else{
            $datosCalendario = ValidarMes::with(['registros' => function ($q) use ($request) {
            $q->where('unidad', $request["unidad"]);}])->where('id_mes_ano', $request['item']['id_mes_ano'])->where('unidad', $request['unidad'])->first();
            $estadoEnvio = 0;
        }

        return response()->json([
            "datosCalendario"    => $datosCalendario,
            "estadoEnvio"        => $estadoEnvio
        ], 200);
    }

    public function getPendientes(Request $request){

        $countEnProceso     = ValidarMes::where('aprobado', 0)->count();
        $countAprobado      = ValidarMes::where('aprobado', 1)->count();
        $countRechazados    = ValidarMes::where('aprobado', 2)->count();
        $countPendientes    = ValidarMes::where('aprobado', 3)->count();

        $pendientes = Sucursal::selectRaw('SUC_DEPARTAMENTO, SUC_CODIGO_DEPARTAMENTO')
        ->join('UNIDADES_ESTANDAR', 'HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', '=', 'UNIDADES_ESTANDAR.SED_COD_DEP')
        ->groupBy('HOJADEVIDASEDES.SUC_SUCURSAL.SUC_DEPARTAMENTO', 'HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO')
        ->orderBy('HOJADEVIDASEDES.SUC_SUCURSAL.SUC_DEPARTAMENTO', 'ASC')
        ->get();

        $pendientes->map(function ($item) {
            $item->unidadesPendientes   = ValidarMes::join('UNIDADES_ESTANDAR', 'ID_UNIDAD', '=', 'RESIDUOS.aprobacion_mes.unidad')
                ->join('HOJADEVIDASEDES.SUC_SUCURSAL', 'HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', '=', 'UNIDADES_ESTANDAR.SED_COD_DEP')
                ->where('HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', $item->SUC_CODIGO_DEPARTAMENTO)
                ->where('aprobado', 3)
            ->get();
        });

        return response()->json([
            "pendientes"        => $pendientes,
            'countEnProceso'    => $countEnProceso,
            'countAprobado'     => $countAprobado,
            'countRechazados'   => $countRechazados,
            'countPendientes'   => $countPendientes,
        ], 200);
    }

    public function statusPeriodo(Request $request)
    {
        $registros = $this->getPendientes($request);
        return Event(new ChangeStatusPeriodoEvent($registros));
    }

    public function getDetallePeriodo(Request $request)
    {

        $sumatorias = Residuos::selectRaw('RESIDUOS.residuos.id_residuos, RESIDUOS.residuos.nomb_residuos, RESIDUOS.categoria_residuos.nomb_categoria, SUM(RESIDUOS.tiempos_residuos.cantidad) as total')
            ->join('RESIDUOS.tiempos_residuos', 'RESIDUOS.residuos.id_residuos', '=', 'RESIDUOS.tiempos_residuos.id_residuo')
            ->join('RESIDUOS.categoria_residuos', 'RESIDUOS.categoria_residuos.id_categoria', '=', 'RESIDUOS.residuos.id_categoria')
            ->where('RESIDUOS.tiempos_residuos.unidad', $request['unidad'])
            ->where('RESIDUOS.tiempos_residuos.id_mes_ano', $request['periodo'])
            ->groupBy('RESIDUOS.residuos.nomb_residuos','RESIDUOS.categoria_residuos.nomb_categoria','RESIDUOS.residuos.id_residuos')
            ->whereBetween('fecha_concat', [$request['fechaDesde'] . "T00:00:00.000",$request['fechaHasta'] . "T23:59:59.999"])
            ->orderBy('total', 'DESC')
        ->get();

        $sumatorias->map(function ($item) use ($request) {
            $item->registros = TiempoResiduos::selectRaw('*')->where('id_mes_ano', $request['periodo'])
                ->join('users', 'users.nro_doc', '=', 'nro_doc_user')
                ->join('RESIDUOS.residuos', 'RESIDUOS.residuos.id_residuos', '=', 'id_residuo')
                ->whereBetween('fecha_concat', [$request['fechaDesde'] . "T00:00:00.000", $request['fechaHasta'] . "T23:59:59.999"])
                ->where('unidad', $request['unidad'])
                ->where('id_residuo', $item->id_residuos)
                ->orderBy('dia', 'asc')
            ->get();
        });

        return response()->json([
            'sumatorias'    => $sumatorias
        ], 200);

    }

    public function updatedStatus(Request $request)
    {
        $periodos = ValidarMes::where('unidad', $request['unidad'])->where('id_mes_ano', $request['periodo'])->update([
            'aprobado' => 1,
            'nro_doc_user' => Auth::user()->nro_doc,
            'fecha_revision' => date('Y-m-d h:m:s')

        ]);


        return response()->json([
            'periodos'    => $periodos
        ], 200);

    }

    public function getDatosDia(Request $request)
    {
        $registro = TiempoResiduos::selectRaw('*')->where('id_mes_ano', $request['idMes'])
            ->join('users', 'users.nro_doc', '=', 'nro_doc_user')
            ->join('RESIDUOS.residuos', 'RESIDUOS.residuos.id_residuos', '=', 'id_residuo')
            ->where('unidad', $request['unidad'])
            ->where('dia', substr($request['day'], -2))
            ->where('mes', substr($request['day'], 5, 2))
            ->where('ano', substr($request['day'], 0, 4))
            ->orderBy('dia', 'asc')
        ->get();

        return response()->json([
            'registro'    => $registro
        ], 200);
    }

    public function editarRegistro(Request $request)
    {
        TiempoResiduos::where('dia', $request['dia'])
            ->where('mes', $request['mes'])
            ->where('ano', $request['ano'])
            ->where('dia', $request['dia'])
            ->where('unidad', $request['unidad'])
            ->where('id_mes_ano', $request['idMes'])
        ->delete();

        foreach ($request["item"] as $item) {
            TiempoResiduos::create([
                'id_residuo'    => $item["id_residuos"],
                'cantidad'      => $item["valor"],
                'dia'           => $request["dia"],
                'mes'           => $request["mes"],
                'ano'           => $request["ano"],
                'fecha_concat'  => $request["ano"] . "-" . $request["mes"] . "-" . $request["dia"] . "T" . date('h:m:s'),
                'nro_doc_user'  => Auth::user()->nro_doc,
                'unidad'        => $request["unidad"],
                'id_mes_ano'    => $request["idMes"],
            ]);
        }

        $datosCalendario = ValidarMes::with(['registros' => function ($q) use ($request) {
            $q->where('unidad', $request["unidad"]);}])->where('id_mes_ano', $request['idMes'])->where('unidad', $request['unidad'])->first();

        return response()->json([
            "datosCalendario"    => $datosCalendario,
        ], 200);
    }



}
