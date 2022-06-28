<?php

namespace App\Http\Controllers\EscalasRehabilitacion;

use App\Http\Controllers\Controller;
use App\Models\Api_Afiliados_Interna\Afiliados;
use App\Models\Escalas\Abandonos;
use App\Models\Escalas\Historial;
use App\Models\Escalas\Justificacion;
use App\Models\Escalas\Programa;
use App\Models\Escalas\Registros;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class EscalasRehabilitacionController extends Controller
{
    public function detalleGrafico(Request $request)
    {
        $programas = Programa::selectRaw('idPrograma, nombre')->get();

        $programas->map(function($item){
            $item->inicial  = Registros::join('ESCALAS.historial as h', 'h.registro_id', 'ESCALAS.registro.idRegistro')->where('ESCALAS.registro.programa_id', $item->idPrograma)->where('h.estado_id', 1)->count();
            $item->parcial  = Registros::join('ESCALAS.historial as h', 'h.registro_id', 'ESCALAS.registro.idRegistro')->where('ESCALAS.registro.programa_id', $item->idPrograma)->where('h.estado_id', 3)->count();
            $item->final    = Registros::join('ESCALAS.historial as h', 'h.registro_id', 'ESCALAS.registro.idRegistro')->where('ESCALAS.registro.programa_id', $item->idPrograma)->where('h.estado_id', 2)->count();
            $item->abandono = Registros::join('ESCALAS.historial as h', 'h.registro_id', 'ESCALAS.registro.idRegistro')->where('ESCALAS.registro.programa_id', $item->idPrograma)->where('h.estado_id', 4)->count();
        });

        return response()->json([ "multiplesVal" => $programas, ], 200);
    }

    public function detalleStats(Request $request)
    {
        $cardiaco  = Registros::with('programas')->where('programa_id', 1)->count();
        $pulmonar  = Registros::with('programas')->where('programa_id', 2)->count();
        $acondici  = Registros::with('programas')->where('programa_id', 3)->count();
        $pisopelv  = Registros::with('programas')->where('programa_id', 4)->count();

        return response()->json([
            "cardiaco" => $cardiaco,
            "pulmonar" => $pulmonar,
            "acondici"   => $acondici,
            "pisopelv" => $pisopelv,
        ], 200);

    }

    public function getProgramas(Request $request)
    {
        $programas = Programa::all();

        return response()->json([
            "programas"   => $programas,
        ], 200);
    }

    public function getProgramasPerAfi(Request $request)
    {
        $afiliado = Afiliados::where('Documento', $request['nro_doc'])->first();
        //$afiliado = DB::table('ESCALAS.afiliado')->where('idAfiliado', $request['nro_doc'])->first();

        $registrosPerAfi = Registros::with(['programas','afiliado'])->where('afiliado_id', $request['nro_doc'])->where('abandono_id', 11)->get();
        $programasDisponibles = [];
        foreach ($registrosPerAfi as $p) {
            if ($p['abandono_id'] == 11) {
                $contadorRegistros = Historial::where('registro_id', $p['idRegistro'])->where('estado_id', 2)->count();
                if ($contadorRegistros > 0) {
                }else{
                    array_push($programasDisponibles, $p);
                }
            }
        }

        return response()->json([
            "programas"   => $programasDisponibles,
            "afiliado"   => $afiliado,
        ], 200);
    }

    public function getAbandonos(Request $request)
    {
        $abandonos = Abandonos::where('activo', 1)->get();

        return response()->json([
            "abandonos"   => $abandonos,
        ], 200);
    }

    public function getEscalasPerPrograma(Request $request)
    {
        $programas = Programa::with([
            'escalas' => function($q) use ($request){
                $q->with([
                    'detalleEscalas',
                    'atributos' => function($q)use ($request){
                        $q->where('estado_idestado', $request['tipoRegistro'])->get();
                    },
                    'resultados' => function($q)use ($request){
                        $q->where('estado_idestado', $request['tipoRegistro'])->get();
                    },
                ]);
            }
        ])->where('idPrograma', $request['idPrograma'])->first();

        return response()->json([
            "programas"   => $programas,
        ], 200);
    }

    public function saveRegistroAfi(Request $request)
    {

        $abandonoDesc =  null;

        if (COUNT($request['motivo_abandono']) > 0) {
            $abandonoDesc = array([
                'id'    => $request['motivo_abandono']['idAbandono'],
                'desc'  => $request['motivo_abandono']['descripcion']
            ]);
        } else {
            $abandonoDesc = array([
                'id'    => $request['detalleAfi']['abandono_id'],
                'desc'  => $request['detalleAfi']['abandono']
            ]);
        }

        //return $abandonoDesc;

        $registro = Registros::where('idRegistro', $request['detalleAfi']['idRegistro'])->update([
            'fecha_fin'         => $request['fecha_final'],
            'tiempo_atencion'   => $request['tiempoAtencion'],
            'sesiones'          => $request['sesiones'],
            'abandono'          => $abandonoDesc[0]['desc'],
            'abandono_id'       => $abandonoDesc[0]['id']
        ]);

        $idRegistro = Registros::where('idRegistro', $request['detalleAfi']['idRegistro'])->first();

            $historial = Historial::create([
                'registro_id'   => $idRegistro->idRegistro,
                'fecha' => date('Y-m-d'),
                'unidad_id' => $request["unidad"],
                'estado_id' => 2,
                'usuario_idusuario' => Auth::user()->nro_doc,
            ]);

            $idHistorial = Historial::latest('idhistorial')->first();

            $justificacion = Justificacion::create([
                'idHistoria'        => $idHistorial->idhistorial,
                'descripcion'       => $request["detalleAfi"]["justificacion"],
            ]);

            foreach ($request["detalleAfi"]["programas"]["escalas"]["escalas"] as $escala) {
                foreach ($escala["atributos"] as $atributo) {
                    if ($atributo["estado_idestado"] == 2) {
                        $insert = DB::table('ESCALAS.historial_atributo')->insert([
                            'historial_idhistorial' => $idHistorial->idhistorial,
                            'atributo_idatributo'   => $atributo['idAtributo'],
                            'valor'                 => isset($atributo['valorAsignado']) ? $atributo['valorAsignado'] : 0,
                        ]);
                    }
                }
            }

            foreach ($request["detalleAfi"]["programas"]["escalas"]["escalas"] as $escala) {

                $result = DB::table('ESCALAS.resultado')->where('escala_id', $escala["escala_idescala"])->where('estado_idestado', 2)->first(); //2 porque es final

                foreach ($escala["resultados"] as $atributo) {
                    if ($atributo["estado_idestado"] == 2) {
                        $insert = DB::table('ESCALAS.historial_resultado')->insert([
                            'historial_idhistorial' => $idHistorial->idhistorial,
                            'resultado_idresultado' => $result->idResultado,
                            'valor'                 => isset($atributo['valorAsignado']) ? $atributo['valorAsignado'] : 0,
                        ]);
                    }
                }
            }

            //$datosHistorico = Historial::where('registro_id', $request['detalleAfi']['idRegistro'])->get();

        return response()->json([
            "registro"   => $request['detalleAfi']['idRegistro'],
        ], 200);
    }

    public function getDiagnosticos(Request $request)
    {
        $diagnosticos = DB::connection('sqlsrv_2')->table('Parametrico.TP_Diagnostico')->get();


        return response()->json([
            "diagnosticos"   => $diagnosticos,
        ], 200);
    }

    public function almacenarNewItem(Request $request)
    {
        //return $request->all();
        try {
            $registro = Registros::create([
                'afiliado_id'               => $request["docAfi"],
                'fecha_inicio'              => $request["fecha_inicial"],
                'fecha_fin'                 => $request["fecha_fin"],
                'tiempo_atencion'           => null,
                'sesiones'                  => $request["sesiones"],
                'programa_id'               => $request["idPrograma"],
                'abandono'                  => $request["abandono"],
                'abandono_id'               => 11,
                'diagnostico'               => $request["diag1"]["Nombre"],
                'diagnostico_secundario'    => $request["diag2"]["Nombre"],
                'IT'                        => $request["IT"],
            ]);

            $idRegistro = Registros::latest('idRegistro')->first();

            $historial = Historial::create([
                'registro_id'   => $idRegistro->idRegistro,
                'fecha' => date('Y-m-d'),
                'unidad_id' => $request["unidad"],
                'estado_id' => 1,
                'usuario_idusuario' => Auth::user()->nro_doc,
            ]);

            $idHistorial = Historial::latest('idhistorial')->first();

            $justificacion = Justificacion::create([
                'idHistoria'        => $idHistorial->idhistorial,
                'descripcion'       => $request["justificacion"],
            ]);

            foreach ($request["escalas"] as $escala) {
                foreach ($escala["atributos"] as $atributo) {
                    if ($atributo["estado_idestado"] == 1) {
                        $insert = DB::table('ESCALAS.historial_atributo')->insert([
                            'historial_idhistorial' => $idHistorial->idhistorial,
                            'atributo_idatributo'   => $atributo['idAtributo'],
                            'valor'                 => isset($atributo['valorAsignado']) ? $atributo['valorAsignado'] : 0,
                        ]);
                    }
                }
            }

            foreach ($request["escalas"] as $escala) {

                $result = DB::table('ESCALAS.resultado')->where('escala_id', $escala["escala_idescala"])->where('estado_idestado', 1)->first(); //1 porque es inicial

                foreach ($escala["resultados"] as $atributo) {
                    if ($atributo["estado_idestado"] == 1) {
                        $insert = DB::table('ESCALAS.historial_resultado')->insert([
                            'historial_idhistorial' => $idHistorial->idhistorial,
                            'resultado_idresultado' => $result->idResultado,
                            'valor'                 => isset($atributo['valorAsignado']) ? $atributo['valorAsignado'] : 0,
                        ]);
                    }
                }
            }

        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json([
            "registro"   => $registro,
        ], 200);
    }

    public function getResultados(Request $request)
    {

        if ($request['registro']['detalleAfi']['programa_id'] == 1) { //CARDIACO

            $catInicial     = 0;
            $catFinal       = 0;
            $detalleRes     = "";
            $tipoRes        = "QLMI MCID 5 PUNTOS";

            $registroInicial = historial::where('registro_id', $request['idRegistro']["registro"])->where('estado_id', 1)->first(); //HISTORIA INICIAL
                $atributosIniciales = DB::table('ESCALAS.historial_resultado')->where('historial_idhistorial', $registroInicial->idhistorial)->get();

            $registroFinal = historial::where('registro_id', $request['idRegistro']["registro"])->where('estado_id', 2)->first(); //HISTORIA FINAL
                $atributosFinales = DB::table('ESCALAS.historial_resultado')->where('historial_idhistorial', $registroFinal->idhistorial)->get();

                foreach ($atributosIniciales as $attIni) {
                    if ($attIni->resultado_idresultado == 3) {
                        $catInicial = floatval($attIni->valor);
                    }
                }

                foreach ($atributosFinales as $attFin) {
                    if ($attFin->resultado_idresultado == 4) {
                        $catFinal = floatval($attFin->valor);
                    }
                }

            $resultado = $catFinal - $catInicial;

            if (floatval($resultado) >= 5 || floatval($resultado) == 189) {
                $detalleRes = "Mejoró";
           }
           else if (floatval($resultado) == 0) {
                $detalleRes = "Igual";
           }
           else if (floatval($resultado) < 0) {
                $detalleRes = "Desmejoró";
           }
           else if (floatval($resultado) < 5 || floatval($resultado) > 0 ) {
                $detalleRes = "Mejoró - No alcanza MCID";
           }

           return response()->json([
               "catInicial"        => $catInicial,
               "catFinal"          => $catFinal,
               "detalleRes"        => $detalleRes,
               "tipoRes"           => $tipoRes,
               'registroInicial'   => $registroInicial,
               'registroFinal'     => $registroFinal,
               'idRegistro'        => $registroFinal->idhistorial

           ], 200);
        }

        if ($request['registro']['detalleAfi']['programa_id'] == 3 || $request['registro']['detalleAfi']['programa_id'] == 2) { //ACONDICIONAMIENTO O PULMONAR

            $catInicial     = 0;
            $catFinal       = 0;
            $detalleRes     = "";
            $tipoRes        = "CAT MCID-3 PUNTOS";

            $registroInicial = historial::where('registro_id', $request['idRegistro']["registro"])->where('estado_id', 1)->first(); //HISTORIA INICIAL
                $atributosIniciales = DB::table('ESCALAS.historial_atributo')->where('historial_idhistorial', $registroInicial->idhistorial)->get();

            $registroFinal = historial::where('registro_id', $request['idRegistro']["registro"])->where('estado_id', 2)->first(); //HISTORIA FINAL
                $atributosFinales = DB::table('ESCALAS.historial_atributo')->where('historial_idhistorial', $registroFinal->idhistorial)->get();

                foreach ($atributosIniciales as $attIni) {
                    if ($attIni->atributo_idatributo == 25) {
                        $catInicial = floatval($attIni->valor);
                    }
                }

                foreach ($atributosFinales as $attFin) {
                    if ($attFin->atributo_idatributo == 26) {
                        $catFinal = floatval($attFin->valor);
                    }
                }

            $resultado = $catFinal - $catInicial ;

            if (floatval($resultado) <= -3) {
                 $detalleRes = "Mejoró";
            }
            else if (floatval($resultado) == 0) {
                 $detalleRes = "Igual";
            }
            else if (floatval($resultado) >= 1) {
                 $detalleRes = "Desmejoró";
            }
            else if (floatval($resultado) < 0 || floatval($resultado) >= -2 ) {
                 $detalleRes = "Mejoró - No alcanza MCID";
            }

            return response()->json([
                "catInicial"        => $catInicial,
                "catFinal"          => $catFinal,
                "detalleRes"        => $detalleRes,
                "tipoRes"           => $tipoRes,
                'registroInicial'   => $registroInicial,
                'registroFinal'     => $registroFinal,
                'idRegistro'        => $registroFinal->idhistorial

            ], 200);
        }

        if ($request['registro']['detalleAfi']['programa_id'] == 4) { //PISO PELVICO
            $catInicial     = 0;
            $catFinal       = 0;
            $detalleRes     = "";
            $tipoRes        = "MCID 4 PUNTOS";

            $registroInicial = historial::where('registro_id', $request['idRegistro']["registro"])->where('estado_id', 1)->first(); //HISTORIA INICIAL
                $atributosIniciales = DB::table('ESCALAS.historial_resultado')->where('historial_idhistorial', $registroInicial->idhistorial)->get();

            $registroFinal = historial::where('registro_id', $request['idRegistro']["registro"])->where('estado_id', 2)->first(); //HISTORIA FINAL
                $atributosFinales = DB::table('ESCALAS.historial_resultado')->where('historial_idhistorial', $registroFinal->idhistorial)->get();

                foreach ($atributosIniciales as $attIni) {
                    if ($attIni->resultado_idresultado == 12) {
                        $catInicial = floatval($attIni->valor);
                    }
                }

                foreach ($atributosFinales as $attFin) {
                    if ($attFin->resultado_idresultado == 13) {
                        $catFinal = floatval($attFin->valor);
                    }
                }

            $resultado = $catInicial - $catFinal;

            if (floatval($resultado) >= 4) {
                $detalleRes = "Mejoró";
           }
           else if (floatval($resultado) == 0) {
                $detalleRes = "Igual";
           }
           else if (floatval($resultado) < 0) {
                $detalleRes = "No Mejoró";
           }
           else if (floatval($resultado) >= 1 && floatval($resultado) <= 3 ) {
                $detalleRes = "No significativo - Mejora";
           }

           return response()->json([
               "catInicial"        => $catInicial,
               "catFinal"          => $catFinal,
               "detalleRes"        => $detalleRes,
               "tipoRes"           => $tipoRes,
               'registroInicial'   => $registroInicial,
               'registroFinal'     => $registroFinal,
               'idRegistro'        => $registroFinal->idhistorial

           ], 200);
        }

    }

    public function finalizarRegistro(Request $request)
    {
        /*
            1: Finaliza
            2: Continua
        */
        if ($request->abandono == 'NO') {

            if ($request->finalizo == 2) {
                Historial::where('idhistorial', $request->idRegistroF)->update([
                    'estado_id' => 3
                ]);
            }
        }else{
            Historial::where('idhistorial', $request->idRegistroF)->update([
                'estado_id' => 4
            ]);

        }

        return response()->json([
            "informacion"               => "ajuste realizado...",
            "abandono"                  => $request['abandono'],
            "registrohistorialupdated"  => $request['idRegistroF'],
            "todo"                      => $request->all(),
        ], 200);
    }


    public function guardarEdicion(Request $request)
    {

        $abandonoUpdate = Abandonos::where('idAbandono', $request['item']['idAbandono'])->update([
            'activo'        => $request['item']['activo'],
            'descripcion'   => $request['item']['descripcion']
        ]);

        $abandonos = Abandonos::all();

        return response()->json([
            "abandonos"   => $abandonos,
        ], 200);

    }

    public function guardarNew(Request $request)
    {

        $abandonoNew = Abandonos::create([
            'activo'        => $request['activo'],
            'descripcion'   => $request['descripcion']
        ]);

        $abandonos = Abandonos::all();

        return response()->json([
            "abandonos"   => $abandonos,
        ], 200);

    }

    public function modificarEstadoUnico(Request $request)
    {
        $registro = Registros::where('idRegistro', $request['programa']['idRegistro'])->update([
            'abandono'      => "SI",
            'abandono_id'   => 8
        ]);

        $historial = Historial::where('registro_id', $request['programa']['idRegistro'])->update([
            'estado_id' => 4
        ]);

        return response()->json([
            "registro"   => $registro,
            "historial"   => $historial,
        ], 200);

    }



}
