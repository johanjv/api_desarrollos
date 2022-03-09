<?php

namespace App\Http\Controllers\GestionResiduos;

use App\Events\ChangeStatusPeriodoEvent;
use App\Http\Controllers\Controller;
use App\Models\Hvsedes\Sucursal\Sucursal;
use App\Models\Residuos\Categoria;
use App\Models\Residuos\Clasificacion;
use App\Models\Residuos\HistorialRechazo;
use App\Models\Residuos\Residuos;
use App\Models\Residuos\TiempoResiduos;
use App\Models\Residuos\ValidarMes;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $fechaParaValidar = ValidarMes::with(['histR', 'userR', 'registros' => function ($q) use ($request) {
            $q->where('unidad', $request["unidad"]);}])
        ->where('id_mes_ano', $request['idMes'])->where('unidad', $request['unidad'])->count();

            if ($fechaParaValidar == 0) {
                if ($request['idMes'] != null) {
                    ValidarMes::create([
                        'id_mes_ano'    => $request['idMes'],
                        'unidad'        => $request['unidad']
                    ]);
                }
            }

        $datosCalendario = ValidarMes::with(['histR', 'userR', 'registros' => function ($q) use ($request) {
            $q->where('unidad', $request["unidad"]);}])
            ->where('id_mes_ano', $request['idMes'])->where('unidad', $request['unidad'])
        ->first();

        $sumatoriaPerResiduo = TiempoResiduos::selectRaw('nomb_residuos, SUM(cantidad) as total')
            ->join('RESIDUOS.residuos','RESIDUOS.residuos.id_residuos','=','RESIDUOS.tiempos_residuos.id_residuo')
            ->where('unidad', $request["unidad"])
            ->where('id_mes_ano', $request["idMes"])
            ->groupBy('nomb_residuos')
        ->get();

        $sumatoriaTotal = TiempoResiduos::selectRaw('SUM(cantidad) as sumatoriaTotal')
            ->join('RESIDUOS.residuos','RESIDUOS.residuos.id_residuos','=','RESIDUOS.tiempos_residuos.id_residuo')
            ->where('unidad', $request["unidad"])
            ->where('id_mes_ano', $request["idMes"])
        ->first();

        $periodos = ValidarMes::selectRaw('id_mes_ano, aprobado, id')->groupBy('id_mes_ano','aprobado', 'id')->orderby('id_mes_ano', 'DESC')->get();
        $periodosDisp = [];
        foreach ($periodos as $p) {
            array_push($periodosDisp, $p);
        }

        if ($periodosDisp[1]['aprobado'] != 1) {
            array_splice($periodosDisp, 0, 1);
        }

        $formula1 = array(11,25,73,50,20,17,66,63,5,76);
        $formula2 = array(47,8,17,23,70,13);

        $tipoFormula = DB::table('UNIDADES_ESTANDAR')->where('ID_UNIDAD', $request['unidad'])->first();

        if ($sumatoriaTotal['sumatoriaTotal'] == null || $sumatoriaTotal['sumatoriaTotal'] == "0.0") {
            $idr = 0; $idi = 0; $idos = 0; $idrs = 0;
        }else{
            if (in_array($tipoFormula->SED_COD_DEP, $formula1)) {
                $idr    = (($sumatoriaPerResiduo[12]['total']/$sumatoriaTotal['sumatoriaTotal'])*100);
                $idi    = (((($sumatoriaPerResiduo[4]['total']+$sumatoriaPerResiduo[1]['total']+$sumatoriaPerResiduo[5]['total']))/$sumatoriaTotal['sumatoriaTotal'])*100);
                $idos   = ((( $sumatoriaPerResiduo[2]['total']+$sumatoriaPerResiduo[3]['total'] + $sumatoriaPerResiduo[8]['total'] + $sumatoriaPerResiduo[11]['total'] + $sumatoriaPerResiduo[13]['total'] + $sumatoriaPerResiduo[6]['total'] + $sumatoriaPerResiduo[7]['total'] + $sumatoriaPerResiduo[10]['total'] + $sumatoriaPerResiduo[0]['total']) / $sumatoriaTotal['sumatoriaTotal']) * 100);
                $idrs   = (( $sumatoriaPerResiduo[12]['total']/$sumatoriaTotal['sumatoriaTotal']) * 100);
            }

            if (in_array($tipoFormula->SED_COD_DEP, $formula2)) {
                $idr    = (($sumatoriaPerResiduo[12]['total']/$sumatoriaTotal['sumatoriaTotal'])*100);
                $idi    = (((($sumatoriaPerResiduo[2]['total']+$sumatoriaPerResiduo[4]['total']+$sumatoriaPerResiduo[1]['total']+$sumatoriaPerResiduo[5]['total']))/$sumatoriaTotal['sumatoriaTotal'])*100);
                $idos   = ((( $sumatoriaPerResiduo[3]['total']+$sumatoriaPerResiduo[8]['total'] + $sumatoriaPerResiduo[11]['total'] + $sumatoriaPerResiduo[13]['total'] + $sumatoriaPerResiduo[6]['total'] + $sumatoriaPerResiduo[7]['total'] + $sumatoriaPerResiduo[10]['total'] + $sumatoriaPerResiduo[0]['total']) / $sumatoriaTotal['sumatoriaTotal']) * 100);
                $idrs   = (( $sumatoriaPerResiduo[9]['total']/$sumatoriaTotal['sumatoriaTotal']) * 100);
            }

        }

        return response()->json([
            "datosCalendario"   => $datosCalendario,
            "periodosDisp"      => $periodosDisp,
            "idr"               => $idr,
            "idi"               => $idi,
            "idos"              => $idos,
            "idrs"              => $idrs,
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
                'is_festivo'    => !isset($item["isFestivo"]) ? 0 : $item["isFestivo"],
                'observacion'   => $item["observacion"]
            ]);
        }

        $datosCalendario = ValidarMes::with(['histR', 'userR', 'registros' => function ($q) use ($request) {
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

            $datosCalendario = ValidarMes::with(['histR', 'userR', 'registros' => function ($q) use ($request) {
            $q->where('unidad', $request["unidad"]);}])->where('id_mes_ano', $request['item']['id_mes_ano'])->where('unidad', $request['unidad'])->first();
            $estadoEnvio = 1;
        }else{
            $datosCalendario = ValidarMes::with(['histR', 'userR', 'registros' => function ($q) use ($request) {
            $q->where('unidad', $request["unidad"]);}])->where('id_mes_ano', $request['item']['id_mes_ano'])->where('unidad', $request['unidad'])->first();
            $estadoEnvio = 0;
        }

        return response()->json([
            "datosCalendario"    => $datosCalendario,
            "estadoEnvio"        => $estadoEnvio
        ], 200);
    }


    public function saveDocumentosRes(Request $request)
    {
        $misAdj = [];
        if ($request->hasFile("files")) {
            $files = $request->file("files");
            foreach ($files as $file) {
                Storage::disk('ftp_residuos')->put($file->getClientOriginalName(), $file);
                array_push($misAdj, $file->getClientOriginalName());
            }

            ValidarMes::where('id_mes_ano', $request['idMes'])->where('unidad', $request['unidad'])->update([
                'adjuntos' => json_encode($misAdj)
            ]);

        }


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
            $item->unidadesPendientes   = ValidarMes::with(['histR', 'userR', 'registros' => function ($q) use ($item) {
            $q->where('unidad', $item->ID_UNIDAD);}])->join('UNIDADES_ESTANDAR', 'ID_UNIDAD', '=', 'RESIDUOS.aprobacion_mes.unidad')
                ->join('HOJADEVIDASEDES.SUC_SUCURSAL', 'HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', '=', 'UNIDADES_ESTANDAR.SED_COD_DEP')
                ->where('HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', $item->SUC_CODIGO_DEPARTAMENTO)
                ->where('aprobado', 3)
            ->get();
            $item->unidadesProceso   = ValidarMes::with(['histR', 'userR', 'registros' => function ($q) use ($item) {
            $q->where('unidad', $item->ID_UNIDAD);}])->join('UNIDADES_ESTANDAR', 'ID_UNIDAD', '=', 'RESIDUOS.aprobacion_mes.unidad')
                ->join('HOJADEVIDASEDES.SUC_SUCURSAL', 'HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', '=', 'UNIDADES_ESTANDAR.SED_COD_DEP')
                ->where('HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', $item->SUC_CODIGO_DEPARTAMENTO)
                ->where('aprobado', 0)
            ->get();
            $item->unidadesAprobadas = ValidarMes::with(['histR', 'userR', 'registros' => function ($q) use ($item) {
            $q->where('unidad', $item->ID_UNIDAD);}])->join('UNIDADES_ESTANDAR', 'ID_UNIDAD', '=', 'RESIDUOS.aprobacion_mes.unidad')
                ->join('HOJADEVIDASEDES.SUC_SUCURSAL', 'HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', '=', 'UNIDADES_ESTANDAR.SED_COD_DEP')
                ->where('HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', $item->SUC_CODIGO_DEPARTAMENTO)
                ->where('aprobado', 1)
            ->get();
            $item->unidadesRechazadas = ValidarMes::with(['histR', 'userR', 'registros' => function ($q) use ($item) {
            $q->where('unidad', $item->ID_UNIDAD);}])->join('UNIDADES_ESTANDAR', 'ID_UNIDAD', '=', 'RESIDUOS.aprobacion_mes.unidad')
                ->join('HOJADEVIDASEDES.SUC_SUCURSAL', 'HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', '=', 'UNIDADES_ESTANDAR.SED_COD_DEP')
                ->where('HOJADEVIDASEDES.SUC_SUCURSAL.SUC_CODIGO_DEPARTAMENTO', $item->SUC_CODIGO_DEPARTAMENTO)
                ->where('aprobado', 2)
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
            /* ->where('RESIDUOS.tiempos_residuos.id_mes_ano', $request['periodo']) */
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
            'aprobado'          => 1,
            'nro_doc_user'      => Auth::user()->nro_doc,
            'fecha_revision'    => date('Y-m-d h:m:s'),
            'start_periodo'     => $request['fechaDesde'],
            'end_periodo'       => $request['fechaHasta']
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
                'is_festivo'    => isset($item["isFestivo"]) ? 1 : 0,
                'observacion'   => $item["observacion"]
            ]);
        }

        $datosCalendario = ValidarMes::with(['histR', 'userR', 'registros' => function ($q) use ($request) {
            $q->where('unidad', $request["unidad"]);}])->where('id_mes_ano', $request['idMes'])->where('unidad', $request['unidad'])->first();

        return response()->json([
            "datosCalendario"    => $datosCalendario,
        ], 200);
    }

    public function rechazarPeriodo(Request $request)
    {
        $periodoGet = ValidarMes::where('unidad', $request['unidad'])->where('id_mes_ano', $request['periodo'])->first();

        $periodos   = ValidarMes::where('unidad', $request['unidad'])->where('id_mes_ano', $request['periodo'])->update([
            'aprobado' => 2,
            'nro_doc_user' => Auth::user()->nro_doc,
            'fecha_revision' => date('Y-m-d h:m:s'),
            'observacion' => $request['motivo']
        ]);

        $rechazadoObs = HistorialRechazo::create([
            'id_aprobacion_mes'     => $periodoGet->id,
            'observacion_rechazo'   => $request['motivo'],
            'fecha_rechazo'         => date('Y-m-d h:m:s'),
            'nro_doc_user'          => Auth::user()->nro_doc,
        ]);

        return response()->json([
            'periodos'    => $periodos
        ], 200);
    }

    public function getFileFTPResiduos(Request $request)
    {
        //return $request->all();
        if (Storage::disk('ftp_residuos')->exists($request["item"])) {
            $directorios = Storage::disk('ftp_residuos')->directories();
            foreach ($directorios as $directorio) {
                if ($request["item"] == $directorio) {
                    $ruta = Storage::disk('ftp_residuos')->files($directorio);
                    $archivo = Storage::disk('ftp_residuos')->get($ruta[0]);
                    $ruta = explode("/", $ruta[0]);

                    Storage::disk('residuos_up')->put($ruta[0], $archivo);
                    return $ruta[0];
                }
            }
        }
        return "ERROR";
    }

    public function saveClas(Request $request)
    {
        Clasificacion::create([
            'nomb_clasif' => $request['clasif']
        ]);

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

    public function getCat(Request $request)
    {
        $categorias = Categoria::with(['residuos','clasificacion'])->get();

        return response()->json([
            "categorias"    => $categorias,
        ], 200);
    }

    public function saveCat(Request $request)
    {
        Categoria::create([
            'nomb_categoria' => $request['nomb_cat'],
            'id_clasif_residuos' => $request['clasif']
        ]);

        $categorias = Categoria::with(['residuos','clasificacion'])->get();

        return response()->json([
            "categorias"    => $categorias,
        ], 200);

    }

    public function getRes(Request $request)
    {
        $residuos = Residuos::with(['categoria'])->get();

        return response()->json([
            "residuos"    => $residuos,
        ], 200);
    }

    public function saveRes(Request $request)
    {
        Residuos::create([
            'nomb_residuos' => $request['nomb_res'],
            'id_categoria' => $request['cat']
        ]);

        $residuos = Residuos::with(['categoria'])->get();

        return response()->json([
            "residuos"    => $residuos,
        ], 200);

    }

    public function saveEditItem(Request $request)
    {
        $item = Clasificacion::where('id_clasif_residuos', $request['item']['id_clasif_residuos'])->update([
            'nomb_clasif' => $request['newItem']
        ]);

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

    public function saveEditItemCat(Request $request)
    {
        $item = Categoria::where('id_categoria', $request['item']['id_categoria'])->update([
            'nomb_categoria' => $request['newItem']
        ]);

        $categorias = Categoria::with(['residuos','clasificacion'])->get();

        return response()->json([
            "categorias"    => $categorias,
        ], 200);
    }

    public function saveEditItemRes(Request $request)
    {
        $item = Residuos::where('id_residuos', $request['item']['id_residuos'])->update([
            'nomb_residuos' => $request['newItem']
        ]);

        $residuos = Residuos::with(['categoria'])->get();

        return response()->json([
            "residuos"    => $residuos,
        ], 200);
    }




}
