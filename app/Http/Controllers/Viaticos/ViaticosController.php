<?php

namespace App\Http\Controllers\Viaticos;

use App\Http\Controllers\Controller;
use App\Mail\NotificacionResiduosAprobado;
use App\Mail\NotificacionViaticosAdjuntos;
use App\Mail\NotificacionViaticosAprobado;
use App\Mail\NotificacionViaticosRegistro;
use App\Mail\NotificacionViaticosRechazo;
use App\Models\Hvsedes\TalentoHumano\Colaboradores;
use App\Models\Residuos\ValidarMes;
use App\Models\Viaticos\Acomodacion;
use App\Models\Viaticos\Aerolineas;
use App\Models\Viaticos\Alimentos;
use App\Models\Viaticos\GrupoRegistro;
use App\Models\Viaticos\Grupos;
use App\Models\Viaticos\Hoteles;
use App\Models\Viaticos\Itinerario;
use App\Models\Viaticos\Millas;
use App\Models\Viaticos\MotivosViajes;
use App\Models\Viaticos\Opciones;
use App\Models\Viaticos\Rechazo;
use App\Models\Viaticos\RegistroSolicitud;
use App\Models\Viaticos\Seguro;
use App\Models\Viaticos\TarifaHoteles;
use App\Models\Viaticos\TarifaViaticos;
use App\Models\Viaticos\ViaticosAeropuerto;
use App\Models\Viaticos\ViaticosCiudadAeropuerto;
use App\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use function PHPSTORM_META\map;

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
        $sucursales = DB::connection('sqlsrv')->table('HOJADEVIDASEDES.SUC_SUCURSAL AS SUC')
            ->selectRaw('SUC.SUC_DEPARTAMENTO, SUC.SUC_CODIGO_DEPARTAMENTO')
            ->groupBy('SUC.SUC_DEPARTAMENTO', 'SUC.SUC_CODIGO_DEPARTAMENTO')
            ->orderBy('SUC.SUC_DEPARTAMENTO', 'ASC')
            ->get();
        return response()->json(["sucursales" => $sucursales, "status" => "ok"]);
    }

    public function getMotivoViajes(Request $request)
    {
        $motivosViajes = MotivosViajes::select('*')->distinct()->orderBy('nomMotivo', 'ASC')->get();
        return response()->json(["motivosViajes" => $motivosViajes, "status" => "ok"]);
    }

    public function insertSolicitud(Request $request)
    {
        $correoDirectivos = $request["directivos"]["correo"];
        $nombreDirectivo = $request["directivos"]["name"] . " " . $request["directivos"]["last_name"];
        $fecSalida = $request["fecSalida"];
        $fecRetorno = $request["fecRetorno"];
        $fechaActual = date('Y-m-d');
        $dias = (strtotime($fecSalida) - strtotime($fechaActual)) / 86400;
        $dias = abs($dias);
        $dias = floor($dias);
        $sucOrigen = $request["sucOrigen"];
        $sucDestino = $request["sucDestino"];
        $horaEstimadaSalida = $request["horaEstimadaSalida"];
        $horaEstimadaRetorno = $request["horaEstimadaRetorno"];
        $nomColaborador = $request["nomColaborador"];
        $docPerAprobacion = $request["cedula"];
        $cedulaColaborador = $request["cedulaColaborador"];
        $cargo = $request["cargo"];
        $proceso = $request["proceso"];
        $hospedaje = $request["hospedaje"];
        $motivoViaje = $request["motivoViaje"];
        $obsMotivos = $request["obsMotivos"];
        $observaciones = $request["observaciones"];
        $documento = Auth::user()->nro_doc;
        $rol = json_decode(Auth::user()->rol);

        $admin = 0;
        foreach ($rol as $value) {
            if ($value == 20) {
                $admin = 1;
            }
        }

        if ($fecRetorno >= $fecSalida) {
            if ($dias < 7 && $admin == 0) {
                return response()->json([
                    "insertSolicitud" =>  false
                ], 200);
            } else {
                $insertSolicitud = RegistroSolicitud::create([
                    'idCiudadOrigen'      => $sucOrigen,
                    'idCiudadDestino'     => $sucDestino,
                    'fechaSalida'         => $fecSalida,
                    'fechaRetorno'        => $fecRetorno,
                    'horaEstimadaSalida'  => $horaEstimadaSalida,
                    'horaEstimadaRetorno' => $horaEstimadaRetorno,
                    'docPerAprobacion'    => $docPerAprobacion,
                    'hospedaje'           => $hospedaje,
                    'idMotivoViaje'       => $motivoViaje,
                    'obsMotivos'          => $obsMotivos,
                    'observaciones'       => $observaciones,
                    'aprobado'            => 0,
                    'docCreador'          => $documento,
                ]);
                $data = RegistroSolicitud::latest('idSolicitud')->first();
                foreach ($request["nomColaborador"] as $key => $value) {
                    $insertSolicitud = GrupoRegistro::create([
                        'solicitud_id'    => $data->idSolicitud,
                        'colaborador_id'  => $value["documento"],
                    ]);
                }
                $datos = DB::connection('sqlsrv')->table('VIATICOS.Solicitud AS SOL')
                    ->selectRaw('SOL.idSolicitud, SOL.docPerAprobacion, SOL.fechaSalida, SOL.fechaRetorno,SOL.idCiudadOrigen, SOL.idCiudadDestino, 
                SUCOri.SUC_DEPARTAMENTO AS DepOrigen, SUCDes.SUC_DEPARTAMENTO AS DepDestino, SOL.aprobado')
                    ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCOri', 'SUCOri.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadOrigen')
                    ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCDes', 'SUCDes.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadDestino')
                    ->where('SOL.idSolicitud', $data->idSolicitud)
                    ->distinct()
                    ->first();

                Mail::to($correoDirectivos)->send(new NotificacionViaticosRegistro($data, $nombreDirectivo, $datos));
                return response()->json([
                    "insertSolicitud" =>  true,
                    "idSolicitud"     =>  $data->idSolicitud
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
        $directivos = DB::connection('sqlsrv')->table('dbo.users AS users')
            ->selectRaw('users.nro_doc, users.name, users.last_name, users.correo, users.cargo')
            ->where("users.viaticosAprobacion", 1)
            ->get();
        $directivos->map(function ($item) {
            $item->nomcargo = $item->name . " " . $item->last_name . " - " . $item->cargo;
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
                'nombres'   => $user['NOMB_COLABORADOR'],
                'correo'    => $user['CORREO']
            );
            array_push($datos, $cadaUno);
        }
        return $datos;
    }

    public function usuariodal(Request $request)
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
                'DOC_COLABORADOR'  => $user['DOC_COLABORADOR'],
                'NOMB_COLABORADOR' => $user['NOMB_COLABORADOR'],
                'CORREO'           => $user['CORREO']
            );
            array_push($datos, $cadaUno);
        }
        return $datos;
    }

    public function getSolicitudes(Request $request)
    {
        $documento = Auth::user()->nro_doc;
        $aprobacionAnulados = DB::connection('sqlsrv')->table('VIATICOS.Solicitud AS SOL')
            ->selectRaw('SOL.idSolicitud, SOL.docPerAprobacion, SOL.fechaSalida, SOL.fechaRetorno, SOL.fechaSolicitud, SOL.idSolicitud,
            SOL.idCiudadOrigen, SOL.idCiudadDestino, SOL.observaciones, SOL.idMotivoViaje, motivos.nomMotivo, SUCOri.SUC_DEPARTAMENTO AS DepOrigen, SUCDes.SUC_DEPARTAMENTO AS DepDestino, SOL.aprobado')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCOri', 'SUCOri.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadOrigen')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCDes', 'SUCDes.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadDestino')
            ->join('VIATICOS.MotivosViajes AS motivos', 'motivos.idMotivoViajes', '=', 'SOL.idMotivoViaje')
            ->where('SOL.docPerAprobacion', $documento)
            ->where('SOL.aprobado', '<', 1)
            ->distinct("SOL.idSolicitud")
            ->orderBy('SOL.idSolicitud', 'ASC')
            ->get();

        $fechaActual = date('Y-m-d H:i:s');
        foreach ($aprobacionAnulados as $value) {
            $dias = (strtotime($value->fechaSolicitud) - strtotime($fechaActual)) / 86400;
            $dias = abs($dias);
            $dias = floor($dias);
            //si es mayor a dos dias se anulan automaticamente ya que no deberian ser aprobados ni rechazados
            if ($dias > 2) {
                $insertSolicitud = RegistroSolicitud::where('idSolicitud', $value->idSolicitud)->update([
                    'aprobado'     => 3,
                    'sistemaAnula' => "El sistema anuló el registro",
                ]);
            }
        }

        $aprobacion = DB::connection('sqlsrv')->table('VIATICOS.Solicitud AS SOL')
            ->selectRaw('SOL.idSolicitud, SOL.docPerAprobacion, SOL.fechaSalida, SOL.fechaRetorno, SOL.fechaSolicitud, SOL.idSolicitud,
            SOL.idCiudadOrigen, SOL.idCiudadDestino, SOL.observaciones, SOL.idMotivoViaje, motivos.nomMotivo, SUCOri.SUC_DEPARTAMENTO AS DepOrigen, SUCDes.SUC_DEPARTAMENTO AS DepDestino, SOL.aprobado')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCOri', 'SUCOri.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadOrigen')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCDes', 'SUCDes.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadDestino')
            ->join('VIATICOS.MotivosViajes AS motivos', 'motivos.idMotivoViajes', '=', 'SOL.idMotivoViaje')
            ->where('SOL.docPerAprobacion', $documento)
            ->where('SOL.aprobado', '<', 1)
            ->distinct("SOL.idSolicitud")
            ->orderBy('SOL.idSolicitud', 'ASC')
            ->get();

        $aprobacion->map(function ($item) {
            $item->datos = DB::table('VIATICOS.grupoRegistro AS GR')->selectRaw('GR.colaborador_id, COL.NOMB_COLABORADOR')
                ->join('HOJADEVIDASEDES.COLABORADORES AS COL', 'COL.DOC_COLABORADOR', '=', 'GR.colaborador_id')
                ->where("solicitud_id", $item->idSolicitud)->get();
        });
        return response()->json(["aprobacion" => $aprobacion, "status" => "ok"]);
    }

    public function aprobacion(Request $request)
    {
        $data = $request->all();
        $idSolicitud = $data["idSolicitud"];

        $validaTiempo = RegistroSolicitud::select('*')->where('idSolicitud', $idSolicitud)->get();
        $fechaSolicitud = $validaTiempo[0]["fechaSolicitud"];
        $fechaActual = date('Y-m-d H:i:s');
        $dias = (strtotime($fechaSolicitud) - strtotime($fechaActual)) / 86400;
        $dias = abs($dias);
        $dias = floor($dias);
        //Esta consulta obtiene los documentos que estan ligados al idSolicitud ya que pueden ser 1 o varios
        $documentosCol = GrupoRegistro::selectRaw('idGrupoRegistro, solicitud_id, colaborador_id, fechaSolicitud')
            ->where('solicitud_id', $idSolicitud)->get();
        //serealiza un foreach con una consulta para obtener los documentos de los colaboradores
        $toatalCorreos = [];
        foreach ($documentosCol as $key => $value) {
            $correoColaboradores = DB::connection('sqlsrv')->table('HOJADEVIDASEDES.COLABORADORES')
                ->where("DOC_COLABORADOR", $value["colaborador_id"])
                ->pluck('CORREO');
            array_push($toatalCorreos, $correoColaboradores[0]);
        }
        //array_push($toatalCorreos, "lenidl@virreysolisips.com.co");
        if ($dias > 2) {
            //1 es para aprobado el 2 es para rechazado y el 3 para anulado
            $insertSolicitud = RegistroSolicitud::where('idSolicitud', $idSolicitud)->update([
                'aprobado'     => 3,
                'sistemaAnula' => "El sistema anuló el registro",
            ]);

            return response()->json([
                "anulado" =>  true,
            ], 200);
        } else {
            $insertSolicitud = RegistroSolicitud::where('idSolicitud', $idSolicitud)->update([
                'aprobado'  => 1,
            ]);

            $datos = DB::connection('sqlsrv')->table('VIATICOS.Solicitud AS SOL')
                ->selectRaw('SOL.idSolicitud, SOL.docPerAprobacion, SOL.fechaSalida, SOL.fechaRetorno,SOL.idCiudadOrigen, SOL.idCiudadDestino, 
                SUCOri.SUC_DEPARTAMENTO AS DepOrigen, SUCDes.SUC_DEPARTAMENTO AS DepDestino, SOL.aprobado')
                ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCOri', 'SUCOri.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadOrigen')
                ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCDes', 'SUCDes.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadDestino')
                ->where('SOL.idSolicitud', $idSolicitud)
                ->distinct()
                ->first();
            $solicitudGet = RegistroSolicitud::where('idSolicitud', $idSolicitud)->first();

            foreach ($toatalCorreos as $key => $value) {
                Mail::to($value)->send(new NotificacionViaticosAprobado($solicitudGet, $datos));
            }

            return response()->json([
                "updateSolicitud" =>  true,
            ], 200);
        }
    }

    public function getRechazoSolicitud(Request $request)
    {
        $data = $request->all();
        $documento = Auth::user()->nro_doc;
        $idSolicitud = $data["idSolicitud"];
        $observaciones = $data["observaciones"];

        $validaTiempo = RegistroSolicitud::select('*')->where('idSolicitud', $idSolicitud)->get();
        $fechaSolicitud = $validaTiempo[0]["fechaSolicitud"];
        $fechaActual = date('Y-m-d H:i:s');
        $dias = (strtotime($fechaSolicitud) - strtotime($fechaActual)) / 86400;
        $dias = abs($dias);
        $dias = floor($dias);

        //Esta consulta obtiene los documentos que estan ligados al idSolicitud ya que pueden ser 1 o varios
        $documentosCol = GrupoRegistro::selectRaw('idGrupoRegistro, solicitud_id, colaborador_id, fechaSolicitud')
            ->where('solicitud_id', $idSolicitud)->get();

        //serealiza un foreach con una consulta para obtener los documentos de los colaboradores
        $toatalCorreos = [];
        foreach ($documentosCol as $key => $value) {
            $correoColaboradores = DB::connection('sqlsrv')->table('HOJADEVIDASEDES.COLABORADORES')
                ->where("DOC_COLABORADOR", $value["colaborador_id"])
                ->pluck('CORREO');
            array_push($toatalCorreos, $correoColaboradores[0]);
        }

        if ($dias > 2) {
            //1 es para aprobado el 2 es para rechazado y el 3 para anulado
            $insertSolicitud = RegistroSolicitud::where('idSolicitud', $idSolicitud)->update([
                'aprobado'     => 3,
                'sistemaAnula' => "El sistema anuló el registro",
            ]);

            return response()->json([
                "anulado" =>  true,
            ], 200);
        } else {
            $insertSolicitud = Rechazo::create([
                'solicitud_id'  => $idSolicitud,
                'observaciones' => $observaciones,
                'docPerRechaza' => $documento,
            ]);
            //si lo aprueba el valor es 1 si lo rechaza el valor es 2 si es anulado el valor es 3
            $insertSolicitud = RegistroSolicitud::where('idSolicitud', $idSolicitud)->update([
                'aprobado'  => 2,
            ]);

            $datos = DB::connection('sqlsrv')->table('VIATICOS.Solicitud AS SOL')
                ->selectRaw('SOL.idSolicitud, SOL.docPerAprobacion, SOL.fechaSalida, SOL.fechaRetorno,SOL.idCiudadOrigen, SOL.idCiudadDestino, 
            SUCOri.SUC_DEPARTAMENTO AS DepOrigen, SUCDes.SUC_DEPARTAMENTO AS DepDestino, SOL.aprobado')
                ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCOri', 'SUCOri.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadOrigen')
                ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCDes', 'SUCDes.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadDestino')
                ->distinct()
                ->first();
            $solicitudGet = RegistroSolicitud::where('idSolicitud', $idSolicitud)->first();
            $solicitudGetobs = Rechazo::where('solicitud_id', $idSolicitud)->first();

            foreach ($toatalCorreos as $key => $value) {
                Mail::to($value)->send(new NotificacionViaticosRechazo($solicitudGet, $solicitudGetobs, $datos));
            }

            return response()->json([
                "insertRechazo" =>  true,
            ], 200);
        }
    }
    public function getSolicitudesAprobadas(Request $request)
    {
        $aprobacion = DB::connection('sqlsrv')->table('VIATICOS.Solicitud AS SOL')
            ->selectRaw('SOL.idSolicitud, SOL.docPerAprobacion, SOL.fechaSalida, SOL.fechaRetorno,SOL.idCiudadOrigen, SOL.idCiudadDestino, 
            SUCOri.SUC_DEPARTAMENTO AS DepOrigen, SUCDes.SUC_DEPARTAMENTO AS DepDestino, SOL.aprobado, SOL.hospedaje')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCOri', 'SUCOri.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadOrigen')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCDes', 'SUCDes.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadDestino')
            ->where('SOL.aprobado', 1)
            ->distinct()
            ->orderBy('SOL.idSolicitud', 'ASC')
            ->get();
        //El número 1 es aprobado 
        $rechazo = DB::connection('sqlsrv')->table('VIATICOS.Solicitud AS SOL')
            ->selectRaw('SOL.idSolicitud, SOL.docPerAprobacion, SOL.fechaSalida, SOL.fechaRetorno,SOL.idCiudadOrigen, SOL.idCiudadDestino, 
            SUCOri.SUC_DEPARTAMENTO AS DepOrigen, SUCDes.SUC_DEPARTAMENTO AS DepDestino, SOL.aprobado, SOL.hospedaje')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCOri', 'SUCOri.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadOrigen')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCDes', 'SUCDes.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadDestino')
            ->where('SOL.aprobado', 2)
            ->distinct()
            ->orderBy('SOL.idSolicitud', 'ASC')
            ->get();
        //El número 2 es aprobado 
        $anulado = DB::connection('sqlsrv')->table('VIATICOS.Solicitud AS SOL')
            ->selectRaw('SOL.idSolicitud, SOL.docPerAprobacion, SOL.fechaSalida, SOL.fechaRetorno,SOL.idCiudadOrigen, SOL.idCiudadDestino, 
            SUCOri.SUC_DEPARTAMENTO AS DepOrigen, SUCDes.SUC_DEPARTAMENTO AS DepDestino, SOL.aprobado, SOL.hospedaje')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCOri', 'SUCOri.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadOrigen')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCDes', 'SUCDes.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadDestino')
            ->where('SOL.aprobado', 3)
            ->distinct()
            ->orderBy('SOL.idSolicitud', 'ASC')
            ->get();
        //El número 3 es aprobado 
        return response()->json(["aprobacion" => $aprobacion, "rechazo" => $rechazo, "anulado" => $anulado, "status" => "ok"]);
    }
    public function getAerolineas(Request $request)
    {
        $aerolineas = Aerolineas::select('*')->get();
        return response()->json(["aerolineas" => $aerolineas, "status" => "ok"]);
    }
    public function getOpciones(Request $request)
    {
        $opciones = Opciones::select('*')->get();
        return response()->json(["opciones" => $opciones, "status" => "ok"]);
    }
    public function getGrupos(Request $request)
    {
        $grupos = Grupos::select('*')->get();
        return response()->json(["grupos" => $grupos, "status" => "ok"]);
    }
    public function getHoteles(Request $request)
    {
        $data = $request->all();
        $hoteles = Hoteles::selectRaw('idHoteles, id_dep_ciudad, nomHotel, desCena')
            ->where("id_dep_ciudad", $data["idCiudadDestino"])->get();
        return response()->json(["hoteles" => $hoteles, "status" => "ok"]);
    }
    public function getAcomodacion(Request $request)
    {
        $acomodacion = Acomodacion::selectRaw('idAcomodacion, nomAcomodacion, estado')->where('estado', 1)->get();
        return response()->json(["acomodacion" => $acomodacion, "status" => "ok"]);
    }
    public function getAcomodacionTarifas(Request $request)
    {
        $data = $request->all();
        $idAcomodacion = $data["habitacion_id"]["idAcomodacion"];
        $idHoteles = $data["hotel_id"]["idHoteles"];

        $tarifasTotales = Hoteles::selectRaw('idHoteles, id_dep_ciudad, nomHotel, tarifas.idHotelTarifa, tarifas.hotel_id, tarifas.acomodacion_id, tarifaSinImpuesto,
        seguro, habitacion')
            ->join('VIATICOS.hotelTarifas AS tarifas', 'tarifas.hotel_id', '=', 'idHoteles')
            ->where('tarifas.acomodacion_id', $idAcomodacion)
            ->where('tarifas.hotel_id', $idHoteles)
            ->get();

        return response()->json(["tarifas" => $tarifasTotales, "status" => "ok"]);
    }
    public function getAlimentos(Request $request)
    {
        $alimentos = Alimentos::selectRaw('idTarifas, alimentos, valor, estado')->where('estado', 1)->get();
        return response()->json(["alimentos" => $alimentos, "status" => "ok"]);
    }
    public function getCalculaDias(Request $request)
    {
        $data = $request->all();
        $fechaSalida = $data["fechaSalida"];
        $fechaRetorno = $data["fechaRetorno"];

        $dias = (strtotime($fechaSalida) - strtotime($fechaRetorno)) / 86400;
        $dias = abs($dias);
        $dias = floor($dias);

        return response()->json(["dias" => $dias, "status" => "ok"]);
    }
    public function getValorAeroSucursal(Request $request)
    {
        $data = $request->all();
        $valorAeropuerto = ViaticosAeropuerto::selectRaw('idViaticosSucursal, recorridoUno, recorridoDos, totalRecorrido, codSuc, estado')
            ->where("codSuc", $data["idCiudadDestino"])->get();
        return response()->json(["valorAeropuerto" => $valorAeropuerto, "status" => "ok"]);
    }

    public function getSeguro(Request $request)
    {
        $seguro = Seguro::selectRaw('idSeguro, nomSeguro, estado')
            ->where("estado", 1)->get();
        return response()->json(["seguro" => $seguro, "status" => "ok"]);
    }

    public function insertItinerarios(Request $request)
    {
        $data = $request->all();
        $datosIndividual = json_decode($data["colaboradores"]);
        $documento = Auth::user()->nro_doc;
        $correos = explode(",", $data["correos"]);

        if ($request->hasFile("files") > 0) {
            $idSolicitud               = $data["idSolicitud"];
            foreach ($datosIndividual as $dat) {
                $insertItinerario = Itinerario::create([
                    'solicitud_id'              => $data["idSolicitud"],
                    'aerolinea_id'              => $dat->detalleViaje->aerolinea->idAreolineas,
                    'hotel_id'                  => isset($dat->detalleViaje->hotel->idHoteles) ? $dat->detalleViaje->hotel->idHoteles : null,
                    'viaticosSuc_id'            => $data["idViaticosSucursal"],
                    'opcion_id'                 => $dat->detalleViaje->opcion->idOpcion,
                    'grupo_id'                  => $dat->detalleViaje->grupos->idGrupos,
                    'acomodacion_id'            => isset($dat->detalleViaje->habitacion->idAcomodacion) ? $dat->detalleViaje->habitacion->idAcomodacion : null,
                    'seguro_id'                 => isset($dat->detalleViaje->valSeguro->idSeguro) ? $dat->detalleViaje->valSeguro->idSeguro : null,
                    'horaSalida'                => $dat->detalleViaje->horaSalida,
                    'horaRetorno'               => $dat->detalleViaje->horaRetorno,
                    'tarifaAdminTrans'          => $dat->detalleViaje->tarifaAdministrativaTrans,
                    'tarifaAdminHosp'           => $dat->detalleViaje->tarifaAdministrativaHosp,
                    'valorTiquete'              => $dat->detalleViaje->valorTiquete,
                    'otroValor'                 => $dat->detalleViaje->otroValor,
                    'valorHotelNoche'           => isset($dat->detCalculo->valorPorNoche) ? $dat->detCalculo->valorPorNoche : null,
                    'docPerRegistra'            => $documento,
                    'docAignacionValorViaticos' => $data["docAignacionValorViaticos"],
                    'valorViaticosAsignados'    => $dat->totalRecorridos,
                    'docPerViaja'               => $dat->DOC_COLABORADOR,
                    'obsOtroValor'              => isset($dat->detalleViaje->obsOtroValor) ? $dat->detalleViaje->obsOtroValor : "",
                    'transAeroDomiAeropuerto'   => $dat->detalleViaje->transAeroDomiAeropuerto,
                    'transInternos'             => str_replace('.', '', $dat->detalleViaje->transInternos),
                    'aeropuerto'                => $dat->detalleViaje->aeropuerto->aeropuerto,
                ]);
            }
            //aprobado # 4 es cuando queda ya finalizado el registro
            $insertSolicitud = RegistroSolicitud::where('idSolicitud', $idSolicitud)->update([
                'aprobado'  => 4,
            ]);

            if ($request->hasFile("files")) {
                $files = $request->file("files");

                foreach ($files as $uno) {
                    //$rt = $uno->getClientOriginalName();
                    $rt = "uploads/viaticos/" . $uno->getClientOriginalName();
                    copy($uno, $rt);
                    foreach ($correos as $value) {
                        Mail::to($value)->send(new NotificacionViaticosAdjuntos($rt));
                    }
                }
            }

            return response()->json([
                "insertItinerario" =>  true,
            ], 200);
        } else {
            return response()->json([
                "sinArchivos" =>  true,
            ], 200);
        }
    }

    public function getDatosColaborador(Request $request)
    {
        //Esta consulta obtiene los documentos que estan ligados al idSolicitud ya que pueden ser 1 o varios
        $documentosCol = GrupoRegistro::selectRaw('idGrupoRegistro, solicitud_id, colaborador_id, fechaSolicitud')
            ->where('solicitud_id', $request["idSolicitud"])->where('estado', null)->get();

        //serealiza un foreach con una consulta para obtener los documentos de los colaboradores
        $toatalCorreos = [];
        foreach ($documentosCol as $key => $value) {
            $datosColaborador = DB::connection('sqlsrv')->table('HOJADEVIDASEDES.COLABORADORES AS COL')
                ->selectRaw('COL.DOC_COLABORADOR, COL.NOMB_COLABORADOR, COL.CORREO, cargos.COD_CARGO, nomcargo.NOMBRE_CARGO')
                ->join('HOJADEVIDASEDES.CARGOS_COLABORADOR AS cargos', 'COL.DOC_COLABORADOR', '=', 'cargos.DOC_COLABORADOR')
                ->join('dbo.CARGOS AS nomcargo', 'cargos.COD_CARGO', '=', 'nomcargo.COD_CARGO')
                ->where("COL.DOC_COLABORADOR", $value["colaborador_id"])
                ->get();
            array_push($toatalCorreos, $datosColaborador[0]);
        }
        $cantidad = count($toatalCorreos);

        return response()->json(["datosColaborador" => $toatalCorreos, "cantidad" => $cantidad, "status" => "ok"]);
    }

    public function cancelaRegistro(Request $request)
    {
        $data = $request->all();
        $documento = Auth::user()->nro_doc;
        //5 cancela el registro
        $insertSolicitud = RegistroSolicitud::where('idSolicitud', $data["idSolicitud"])->update([
            'aprobado'      => 5,
            'docPerCancela' => $documento,
        ]);

        return response()->json([
            "cancelado" =>  true,
        ], 200);
    }

    public function cancelCasosMasivos(Request $request)
    {
        $data = $request->all();
        $documento = Auth::user()->nro_doc;
        //5 cancela el registro
        foreach ($data["cancelaCasos"] as $value) {
            $insertSolicitud = RegistroSolicitud::where('idSolicitud', $value["idSolicitud"])->update([
                'aprobado'          => 5,
                'docPerCancela'     => $documento,
                'obserCancelaCasos' => $data["obserCancelaCasos"],
            ]);
        }

        return response()->json([
            "cancelado" =>  true,
        ], 200);
    }

    public function getViaticosDash()
    {
        $datosGrafico = [];
        $getAprobadosDash = RegistroSolicitud::where('aprobado', 1)->count();
        $getRechazadoDash = RegistroSolicitud::where('aprobado', 2)->count();
        $getAnuladoDash = RegistroSolicitud::where('aprobado', 3)->count();
        $getFinalizadodoDash = RegistroSolicitud::where('aprobado', 4)->count();
        $getCanceladoDash = RegistroSolicitud::where('aprobado', 5)->count();

        array_push($datosGrafico, $getAprobadosDash);
        array_push($datosGrafico, $getRechazadoDash);
        array_push($datosGrafico, $getAnuladoDash);
        array_push($datosGrafico, $getFinalizadodoDash);
        array_push($datosGrafico, $getCanceladoDash);
        return response()->json([
            "datosGrafico" =>  $datosGrafico
        ], 200);
    }

    public function getHotelesAdm(Request $request)
    {
        $data = $request->all();
        $hoteles = Hoteles::selectRaw('idHoteles, id_dep_ciudad, nomHotel, estado, desCena, SUC.SUC_DEPARTAMENTO')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUC', 'SUC.SUC_CODIGO_DEPARTAMENTO', '=', 'id_dep_ciudad')
            ->distinct()
            ->orderBy('nomHotel', 'ASC')
            ->get();
        return response()->json(["hoteles" => $hoteles, "status" => "ok"]);
    }

    public function editHotel(Request $request)
    {
        $data = $request->all();
        $update = Hoteles::where("idHoteles", $data["idHoteles"])->update([
            'id_dep_ciudad' => $request["idDepartamento"],
            'nomHotel'      => $request["nomHotel"],
            'estado'        => $request["estado"],
            'desCena'       => $request["desCena"],
        ]);
        return response()->json([
            "update" =>  $update
        ], 200);
    }

    public function agregaHoteInsert(Request $request)
    {
        $data = $request->all();

        $insertHotel = Hoteles::create([
            'id_dep_ciudad' => $data["idDepartamento"],
            'nomHotel'      => $data["nomHotel"],
            'estado'        => 1,
            'desCena'       => 0
        ]);

        return response()->json([
            "insertHotel" =>  true,
        ], 200);
    }

    public function agregaHoteTarifa(Request $request)
    {
        $data = $request->all();

        $totalHabitacion = $data["tarifaSinImpuesto"] + $data["seguroTarifa"];

        $insertHotelTarifa = TarifaHoteles::create([
            'hotel_id'          => $data["idHotel"],
            'acomodacion_id'    => $data["idAcomodacion"],
            'tarifaSinImpuesto' => $data["tarifaSinImpuesto"],
            'seguro'            => $data["seguroTarifa"],
            'habitacion'        => $totalHabitacion,
        ]);

        return response()->json([
            "insertHotelTarifa" =>  true,
        ], 200);
    }

    public function getHotelesTarifas(Request $request)
    {
        $hotelesTarifas = TarifaHoteles::selectRaw('idHotelTarifa, hotel_id, acomodacion_id, tarifaSinImpuesto, seguro, habitacion, H.nomHotel, A.nomAcomodacion')
            ->join('VIATICOS.hoteles as H', 'H.idHoteles', '=', 'hotel_id')
            ->join('VIATICOS.acomodacion as A', 'A.idAcomodacion', '=', 'acomodacion_id')
            ->orderBy('H.nomHotel', 'ASC')
            ->get();
        return response()->json(["hotelesTarifas" => $hotelesTarifas, "status" => "ok"]);
    }

    public function editaTarifa(Request $request)
    {
        $data = $request->all();

        $hotel = TarifaHoteles::selectRaw('hotel_id')
            ->where("idHotelTarifa", $data["idHotelTarifa"])
            ->get();

        $totalTarifa = $request["seguro"] + $request["tarifa"];

        $update = TarifaHoteles::where("idHotelTarifa", $data["idHotelTarifa"])->update([
            'acomodacion_id'    => $data["acomodacion"],
            'tarifaSinImpuesto' => $data["tarifa"],
            'seguro'            => $data["seguro"],
            'habitacion'        => $totalTarifa,
        ]);
        $update = Hoteles::where("idHoteles", $hotel[0]["hotel_id"])->update([
            'nomHotel'    => $data["nomHotel"],
        ]);
        return response()->json([
            "update" =>  true
        ], 200);
    }

    public function getMillas(Request $request)
    {
        $millas = Millas::selectRaw('idMillas, cantidadMillas, Observaciones, docRegistro')->get();
        return response()->json(["millas" => $millas, "status" => "ok"]);
    }

    public function insertMillas(Request $request)
    {
        $data = $request->all();
        $documento = Auth::user()->nro_doc;

        $insertMillas = Millas::create([
            'cantidadMillas' => $data["cantidadMillas"],
            'Observaciones'  => $data["observaciones"],
            'docRegistro'    => $documento,
        ]);

        return response()->json([
            "insertMillas" =>  true,
        ], 200);
    }

    public function editarMillas(Request $request)
    {
        $data = $request->all();
        $update = Millas::where("idMillas", $data["idMillas"])->update([
            'cantidadMillas' => $data["cantidadMillas"],
            'Observaciones'  => $data["observaciones"],
        ]);

        return response()->json([
            "editMillas" =>  true,
        ], 200);
    }

    public function insertAerolineas(Request $request)
    {
        $data = $request->all();

        $insertAerolineas = Aerolineas::create([
            'nomAerolinea' => $data["nombreAerolinea"],
            'estado' => 1,
        ]);

        return response()->json([
            "insertAerolineas" =>  true,
        ], 200);
    }

    public function editarAerolineas(Request $request)
    {
        $data = $request->all();
        $update = Aerolineas::where("idAreolineas", $data["idAreolineas"])->update([
            'nomAerolinea' => $data["nomAerolinea"],
        ]);

        return response()->json([
            "editAerolineas" =>  true,
        ], 200);
    }

    public function insertAcomodacion(Request $request)
    {
        $data = $request->all();

        $insertAcomodacion = Acomodacion::create([
            'nomAcomodacion' => $data["nombreAcomodacion"],
            'estado' => 1,
        ]);

        return response()->json([
            "insertAcomodacion" =>  true,
        ], 200);
    }

    public function editarAcomodacion(Request $request)
    {
        $data = $request->all();
        $update = Acomodacion::where("idAcomodacion", $data["idAcomodacion"])->update([
            'nomAcomodacion' => $data["nomAcomodacion"],
        ]);

        return response()->json([
            "editAcomodacion" =>  true,
        ], 200);
    }

    public function insertMotivo(Request $request)
    {
        $data = $request->all();
        $insertAcomodacion = MotivosViajes::create([
            'nomMotivo' => $data["nombreMotivo"],
            'estado' => 1,
        ]);

        return response()->json([
            "insertMotivo" =>  true,
        ], 200);
    }

    public function editarMotivo(Request $request)
    {
        $data = $request->all();
        $update = MotivosViajes::where("idMotivoViajes", $data["idMotivoViajes"])->update([
            'nomMotivo' => $data["nomMotivo"],
        ]);

        return response()->json([
            "editMotivo" =>  true,
        ], 200);
    }

    public function insertGrupos(Request $request)
    {
        $data = $request->all();
        $insertAcomodacion = Grupos::create([
            'nomGrupo' => $data["nombreGrupo"],
            'estado' => 1,
        ]);

        return response()->json([
            "insertGrupo" =>  true,
        ], 200);
    }

    public function editarGrupo(Request $request)
    {
        $data = $request->all();
        $update = Grupos::where("idGrupos", $data["idGrupos"])->update([
            'nomGrupo' => $data["nomGrupo"],
        ]);

        return response()->json([
            "editGrupo" =>  true,
        ], 200);
    }

    public function insertRuta(Request $request)
    {
        $data = $request->all();
        $insertAcomodacion = Opciones::create([
            'nomOpcion' => $data["nombreRuta"],
            'estado' => 1,
        ]);

        return response()->json([
            "insertRuta" =>  true,
        ], 200);
    }

    public function editarRuta(Request $request)
    {
        $data = $request->all();
        $update = Opciones::where("idOpcion", $data["idOpcion"])->update([
            'nomOpcion' => $data["nomOpcion"],
        ]);

        return response()->json([
            "editRuta" =>  true,
        ], 200);
    }

    public function getTarifaViaticos(Request $request)
    {
        $tarifas = TarifaViaticos::select('*')->get();
        return response()->json(["tarifas" => $tarifas, "status" => "ok"]);
    }

    public function insertTarifa(Request $request)
    {
        $data = $request->all();
        $insertTarifa = TarifaViaticos::create([
            'alimentos' => $data["nombreAlimentos"],
            'valor' => $data["valor"],
            'estado' => 1,
        ]);

        return response()->json([
            "insertTarifa" =>  true,
        ], 200);
    }

    public function editarTarifa(Request $request)
    {
        $data = $request->all();
        $update = TarifaViaticos::where("idTarifas", $data["idTarifas"])->update([
            'alimentos' => $data["alimentos"],
            'valor'     => $data["valor"],
            'estado'    => 1,
        ]);

        return response()->json([
            "editTarifa" =>  true,
        ], 200);
    }

    public function getSolicitudesAdmin(Request $request)
    {
        $documento = Auth::user()->nro_doc;
        $aprobacion = DB::connection('sqlsrv')->table('VIATICOS.Solicitud AS SOL')
            ->selectRaw('SOL.idSolicitud, SOL.docPerAprobacion, SOL.fechaSalida, SOL.fechaRetorno,
            SOL.idCiudadOrigen, SOL.idCiudadDestino, SOL.observaciones, SOL.idMotivoViaje, motivos.nomMotivo, SUCOri.SUC_DEPARTAMENTO AS DepOrigen, SUCDes.SUC_DEPARTAMENTO AS DepDestino, SOL.aprobado')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCOri', 'SUCOri.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadOrigen')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUCDes', 'SUCDes.SUC_CODIGO_DEPARTAMENTO', '=', 'SOL.idCiudadDestino')
            ->join('VIATICOS.MotivosViajes AS motivos', 'motivos.idMotivoViajes', '=', 'SOL.idMotivoViaje')
            ->where('SOL.docPerAprobacion', $documento)
            ->distinct("SOL.idSolicitud")
            ->orderBy('SOL.idSolicitud', 'ASC')
            ->get();

        $aprobacion->map(function ($item) {
            $item->datos = DB::table('VIATICOS.grupoRegistro AS GR')->selectRaw('GR.colaborador_id, COL.NOMB_COLABORADOR')
                ->join('HOJADEVIDASEDES.COLABORADORES AS COL', 'COL.DOC_COLABORADOR', '=', 'GR.colaborador_id')
                ->where("solicitud_id", $item->idSolicitud)->get();
        });
        return response()->json(["aprobacion" => $aprobacion, "status" => "ok"]);
    }

    public function getTarifaSucursales(Request $request)
    {
        $sucursales = ViaticosAeropuerto::selectRaw('idViaticosSucursal, recorridoUno, recorridoDos, totalRecorrido, codSuc, estado, SUC.SUC_DEPARTAMENTO, SUC.SUC_CODIGO_DEPARTAMENTO')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUC', 'SUC.SUC_CODIGO_DEPARTAMENTO', '=', 'codSuc')
            ->distinct("SUC.SUC_DEPARTAMENTO")
            ->orderBy('SUC.SUC_DEPARTAMENTO', 'ASC')
            ->get();
        return response()->json(["sucursales" => $sucursales, "status" => "ok"]);
    }

    public function editarTarifaSucursales(Request $request)
    {
        $data = $request->all();
        $valorTotal = $data["recorridoUno"] + $data["recorridoDos"];
        $update = ViaticosAeropuerto::where("idViaticosSucursal", $data["idViaticosSucursal"])->update([
            'recorridoUno'   => $data["recorridoUno"],
            'recorridoDos'   => $data["recorridoDos"],
            'totalRecorrido' => $valorTotal,
        ]);

        return response()->json([
            "editTarifa" =>  true,
        ], 200);
    }

    public function eliminaColaborador(Request $request)
    {
        $data = $request->all();
        $update = GrupoRegistro::where("solicitud_id", $data["idSolicitud"])->where("colaborador_id", $data["documento"])->update([
            'estado'         => 1,
            'observaciones'  => $data["observaciones"],
        ]);

        return response()->json([
            "eliminado" =>  true,
        ], 200);
    }

    public function agregaColaborador(Request $request)
    {
        $data = $request->all();

        $insertSolicitud = GrupoRegistro::create([
            'solicitud_id'    => $data["idSolicitud"],
            'colaborador_id'  => $data["datos"]["DOC_COLABORADOR"],
        ]);

        return response()->json([
            "insert" =>  true,
        ], 200);
    }

    public function getViaticosCiudadAeropuerto(Request $request)
    {
        $data = $request->all();
        $viaticosCiudadAeropuerto = ViaticosCiudadAeropuerto::selectRaw('id, idDepartamento, aeropuerto, valorIda, valorVuelta, total, estado')
            ->where("idDepartamento", $data["idCiudadOrigen"])->get();
        return response()->json(["viaticosCiudadAeropuerto" => $viaticosCiudadAeropuerto, "status" => "ok"]);
    }

    public function getViaticosAeropuerto(Request $request)
    {
        $viaticos = ViaticosCiudadAeropuerto::selectRaw('id, idDepartamento, aeropuerto, valorIda, valorVuelta, total, estado, SUC.SUC_DEPARTAMENTO, SUC.SUC_CODIGO_DEPARTAMENTO')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUC', 'SUC.SUC_CODIGO_DEPARTAMENTO', '=', 'idDepartamento')
            ->distinct("SUC.SUC_DEPARTAMENTO")
            ->orderBy('SUC.SUC_DEPARTAMENTO', 'ASC')
            ->get();
        return response()->json(["viaticos" => $viaticos, "status" => "ok"]);
    }

    public function editarTarifaAeropuerto(Request $request)
    {
        $data = $request->all();
        $valorTotal = $data["valorIda"] + $data["valorVuelta"];
        $update = ViaticosCiudadAeropuerto::where("id", $data["idAeropuerto"])->update([
            'aeropuerto'  => $data["aeropuerto"],
            'valorIda'    => $data["valorIda"],
            'valorVuelta' => $data["valorVuelta"],
            'total'       => $valorTotal,
        ]);

        return response()->json([
            "editTarifa" =>  true,
        ], 200);
    }
    public function insertTarifaAeropuerto(Request $request)
    {
        $data = $request->all();
        $valorTotal = $data["valorIda"] + $data["valorVuelta"];
        $insertTarifa = ViaticosCiudadAeropuerto::create([
            'idDepartamento' => $data["departamento"]["SUC_CODIGO_DEPARTAMENTO"],
            'aeropuerto'     => $data["aeropuerto"],
            'valorIda'       => $data["valorIda"],
            'valorVuelta'    => $data["valorVuelta"],
            'total'          => $valorTotal,
            'estado'         => 1,
        ]);

        return response()->json([
            "insertTarifa" =>  true,
        ], 200);
    }
}
