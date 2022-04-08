<?php

namespace App\Http\Controllers\Viaticos;

use App\Http\Controllers\Controller;
use App\Models\Hvsedes\TalentoHumano\Colaboradores;
use App\Models\Viaticos\Directivos;
use App\Models\Viaticos\MotivosViajes;
use App\Models\Viaticos\Rechazo;
use App\Models\Viaticos\RegistroSolicitud;
use App\Models\Viaticos\Solicitud;
use App\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class ViaticosController extends Controller
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

    public function getSucursal(Request $request)
    {
        $sucursales = DB::connection('sqlsrv')->table('HOJADEVIDASEDES.SED_SEDE AS SED')
            ->selectRaw('SUC.SUC_DEPARTAMENTO, COUNT(SED.SED_NOMBRE_SEDE) as CantidadSedes, SUC.SUC_CODIGO_DEPARTAMENTO')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUC', 'SUC.SUC_CODIGO_DANE', '=', 'SED.SUC_CODIGO_DANE')
            ->groupBy('SUC.SUC_DEPARTAMENTO', 'SUC.SUC_CODIGO_DEPARTAMENTO')
            ->orderBy('CantidadSedes', 'DESC')
            ->get();
        return response()->json(["sucursales" => $sucursales, "status" => "ok"]);
    }

    public function getMotivoViajes(Request $request)
    {
        $motivosViajes = MotivosViajes::select('*')->distinct()->get();
        return response()->json(["motivosViajes" => $motivosViajes, "status" => "ok"]);
    }

    public function insertSolicitud(Request $request)
    {

        $data = $request->all();
        $fecSalida = $data["fecSalida"];
        $fecRetorno = $data["fecRetorno"];
        $fechaActual = date('Y-m-d H:i:s');
        $dias = (strtotime($fecSalida) - strtotime($fechaActual)) / 86400;
        $dias = abs($dias);
        $dias = floor($dias);
        $sucOrigen = $data["sucOrigen"];
        $sucDestino = $data["sucDestino"];
        $horaEstimada = $data["horaEstimada"];
        $nomColaborador = $data["nomColaborador"];
        $docPerAprobacion = $data["cedula"];
        $cedulaColaborador = $data["cedulaColaborador"];
        $cargo = $data["cargo"];
        $proceso = $data["proceso"];
        $hospedaje = $data["hospedaje"];
        $motivoViaje = $data["motivoViaje"];
        $obsMotivos = $data["obsMotivos"];
        $observaciones = $data["observaciones"];
        $documento = Auth::user()->nro_doc;

        if ($fecRetorno >= $fecSalida) {
            if ($dias < 7) {
                return response()->json([
                    "insertSolicitud" =>  false
                ], 200);
            } else {
                $insertSolicitud = RegistroSolicitud::create([
                    'idCiudadOrigen'    => $sucOrigen,
                    'idCiudadDestino'   => $sucDestino,
                    'fechaSalida'       => $fecSalida,
                    'fechaRetorno'      => $fecRetorno,
                    'horarioEstimado'   => $horaEstimada,
                    'idColaborador'     => $cedulaColaborador,
                    'docPerAprobacion'  => $docPerAprobacion,
                    'hospedaje'         => $hospedaje,
                    'idMotivoViaje'     => $motivoViaje,
                    'obsMotivos'        => $obsMotivos,
                    'observaciones'     => $observaciones,
                    'aprobado'          => 0,
                    'estadoSolicitud'   => 1,
                    'docCreador'        => $documento,
                ]);
                $data = Solicitud::latest('idSolicitud')->first();
                return response()->json([
                    "insertSolicitud" =>  true,
                    "idSolicitud" =>  $data->idSolicitud
                ], 200);
            }
        } else {
            return response()->json([
                "fechaRetornoMayor" =>  true
            ], 200);
        }
    }

    public function getDirectivos(Request $request)
    {
        $directivos = Directivos::select('*')->distinct()->get();

        $directivos->map(function ($item) {
            $item->nomcargo = $item->nomDirectivo . " - " . $item->cargo;
        });
        return response()->json(["directivos" => $directivos, "status" => "ok"]);
    }

    public function usuarioda(Request $request)
    {
        $buscar = $request["nombre"];
        $usersList = Colaboradores::where(function ($q) use ($buscar) {
            $buscar != null ? $q->where("NOMB_COLABORADOR", 'like', '%' . $buscar . '%') : $q;
        })->orWhere(function ($q) use ($buscar) {
            $buscar != null ? $q->where("DOC_COLABORADOR", 'like', '%' . $buscar . '%') : $q;
        })->get();

        $datos = [];

        foreach ($usersList as $user) {
            $cadaUno = array(
                'documento' => $user['DOC_COLABORADOR'],
                'nombres'   => $user['NOMB_COLABORADOR']
            );
            array_push($datos, $cadaUno);
        }
        return $datos;
    }

    public function getSolicitudes(Request $request)
    {
        $documento = Auth::user()->nro_doc;

        $aprobacion = DB::connection('sqlsrv')->table('VIATICOS.Solicitud AS SOL')
            ->selectRaw('SOL.idSolicitud, SOL.idColaborador, SOL.docPerAprobacion, COL.DOC_COLABORADOR, COL.NOMB_COLABORADOR, COL.CORREO, SOL.fechaSalida, SOL.fechaRetorno,
            SOL.idCiudadOrigen, SOL.idCiudadDestino, SUCOri.SUC_DEPARTAMENTO AS DepOrigen, SUCDes.SUC_DEPARTAMENTO AS DepDestino, SOL.aprobado')
            ->join('HOJADEVIDASEDES.COLABORADORES AS COL', 'COL.DOC_COLABORADOR', '=', 'SOL.idColaborador')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCOri', 'SUCOri.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadOrigen')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCDes', 'SUCDes.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadDestino')
            ->where('SOL.docPerAprobacion', $documento)
            ->where('SOL.aprobado', '<', 1)
            ->distinct()
            ->orderBy('SOL.idSolicitud', 'ASC')
            ->get();
        return response()->json(["aprobacion" => $aprobacion, "status" => "ok"]);
    }

    public function aprobacion(Request $request)
    {
        $data = $request->all();
        $idSolicitud = $data["idSolicitud"];
        $correo = $data["correo"];

        $insertSolicitud = RegistroSolicitud::where('idSolicitud', $idSolicitud)->update([
            'aprobado'  => 1,
        ]);

        return response()->json([
            "updateSolicitud" =>  true,
        ], 200);
    }

    public function getRechazoSolicitud(Request $request)
    {
        $data = $request->all();
        $idSolicitud = $data["idSolicitud"];
        $observaciones = $data["observaciones"];
        $correoRechazo = $data["correoRechazo"];

        $insertSolicitud = Rechazo::create([
            'solicitud_id'  => $idSolicitud,
            'observaciones' => $observaciones,
        ]);
        //si lo aprueba el valor es 1 si lo rechaza el valor es 2
        $insertSolicitud = RegistroSolicitud::where('idSolicitud', $idSolicitud)->update([
            'aprobado'  => 2,
        ]);

        return response()->json([
            "insertRechazo" =>  true,
        ], 200);
    }
}
