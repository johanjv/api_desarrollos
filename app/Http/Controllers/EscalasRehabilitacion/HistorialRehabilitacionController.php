<?php

namespace App\Http\Controllers\EscalasRehabilitacion;

use App\Http\Controllers\Controller;
use App\Models\Escalas\Atributos;
use App\Models\Escalas\Historial;
use App\Models\Escalas\HistorialAtributo;
use App\Models\Escalas\ProgramaEscala;
use App\Models\Escalas\Registros;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class HistorialRehabilitacionController extends Controller
{
    public function getHistorial(Request $request)
    {
        $registros = null;

        if ($request['fechaDesde'] != null && $request['fechaHasta'] != null) {
            $registros = Registros::with(['programas', 'afiliado','abandono'])->whereBetween('fecha_inicio', [$request['fechaDesde'] . "T00:00:00.000",$request['fechaHasta'] . "T23:59:59.999"])->orderBy('fecha_inicio', 'DESC')->get();
        }
        if ($request['fechaDesde'] != null && $request['fechaHasta'] != null && $request['nro_doc'] != null) {
            $registros = Registros::with(['programas', 'afiliado','abandono'])->whereBetween('fecha_inicio', [$request['fechaDesde'] . "T00:00:00.000",$request['fechaHasta'] . "T23:59:59.999"])
                ->where('afiliado_id', $request['nro_doc'])
                ->orderBy('fecha_inicio', 'DESC')
            ->get();
        }
        if ($request['fechaDesde'] != null && $request['fechaHasta'] != null && $request['nro_doc'] != null && $request['programa'] != 0) {
            $registros = Registros::with(['programas', 'afiliado','abandono'])->whereBetween('fecha_inicio', [$request['fechaDesde'] . "T00:00:00.000",$request['fechaHasta'] . "T23:59:59.999"])
                ->where('afiliado_id', $request['nro_doc'])->where('programa_id', $request['programa'])
                ->orderBy('fecha_inicio', 'DESC')
            ->get();
        }

        if ($request['fechaDesde'] == null && $request['fechaHasta'] == null && $request['nro_doc'] != null && $request['programa'] == 0) {
            $registros = Registros::with(['programas', 'afiliado','abandono'])->where('afiliado_id', $request['nro_doc'])->orderBy('fecha_inicio', 'DESC')->get();
        }

        if ($request['fechaDesde'] == null && $request['fechaHasta'] == null && $request['nro_doc'] == null && $request['programa'] != 0) {
            $registros = Registros::with(['programas', 'afiliado','abandono'])->where('programa_id', $request['programa'])->orderBy('fecha_inicio', 'DESC')->get();
        }

        if ($request['fechaDesde'] == null && $request['fechaHasta'] == null && $request['nro_doc'] != null && $request['programa'] != 0) {
            $registros = Registros::with(['programas', 'afiliado','abandono'])->where('programa_id', $request['programa'])
            ->where('afiliado_id', $request['nro_doc'])->orderBy('fecha_inicio', 'DESC')->get();
        }

        return response()->json([
            "registros"   => $registros,
        ], 200);
    }

    public function getDetalleHistorial(Request $request)
    {

        $programa = Registros::where('idRegistro', $request['idRegistro'])->first();
        $escalas = ProgramaEscala::with(['atributos'])->where('programa_idprograma', $programa->programa_id)->get();

        $escalasPerProg = []; //almacenara todas las escalas de acuerdo al programa seleccionado.

        foreach ($escalas as $escala) {
            foreach ($escala['atributos'] as $atributosEscala) {
                array_push($escalasPerProg, $atributosEscala);
            }
        }

        $historial = Historial::with(['profesional', 'unidad'])->where('registro_id', $request['idRegistro'])->get();

        $historial->map(function($item) use($escalasPerProg){
            $item->escalasDisp = $escalasPerProg;
            $item->hist_att = HistorialAtributo::with('atributo')->where('historial_idhistorial', $item->idhistorial)->get();
        });


        if ($programa->programa_id == 1) { //CARDIACO

            $catInicial     = 0;
            $catFinal       = 0;
            $detalleRes     = "";
            $tipoRes        = "QLMI MCID 5 PUNTOS";

            $registroInicial = historial::where('registro_id', $request['idRegistro'])->where('estado_id', 1)->first(); //HISTORIA INICIAL
                $atributosIniciales = DB::table('ESCALAS.historial_resultado')->where('historial_idhistorial', $registroInicial->idhistorial)->get();

            $registroFinal = historial::where('registro_id', $request['idRegistro'])->where('estado_id', 2)->first(); //HISTORIA FINAL

            if ($registroFinal) {
                $atributosFinales = DB::table('ESCALAS.historial_resultado')->where('historial_idhistorial', $registroFinal->idhistorial)->get();
            }else{
                $registroFinal = historial::where('registro_id', $request['idRegistro'])->where('estado_id', 4)->first(); //HISTORIA FINAL
                if ($registroFinal) {
                    $atributosFinales = DB::table('ESCALAS.historial_resultado')->where('historial_idhistorial', $registroFinal->idhistorial)->get();
                }else{
                    $atributosFinales = [];
                }
            }

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

        }

        if ($programa->programa_id == 3 || $programa->programa_id == 2) { //ACONDICIONAMIENTO O PULMONAR

            $catInicial     = 0;
            $catFinal       = 0;
            $detalleRes     = "";
            $tipoRes        = "CAT MCID-3 PUNTOS";

            $registroInicial = historial::where('registro_id', $request['idRegistro'])->where('estado_id', 1)->first(); //HISTORIA INICIAL
                $atributosIniciales = DB::table('ESCALAS.historial_atributo')->where('historial_idhistorial', $registroInicial->idhistorial)->get();

            $registroFinal = historial::where('registro_id', $request['idRegistro'])->where('estado_id', 2)->first(); //HISTORIA FINAL

            if ($registroFinal) {
                $atributosFinales = DB::table('ESCALAS.historial_atributo')->where('historial_idhistorial', $registroFinal->idhistorial)->get();
            }else{
                $registroFinal = historial::where('registro_id', $request['idRegistro'])->where('estado_id', 4)->first(); //HISTORIA FINAL
                if ($registroFinal) {
                    $atributosFinales = DB::table('ESCALAS.historial_atributo')->where('historial_idhistorial', $registroFinal->idhistorial)->get();
                }else{
                    $atributosFinales = [];
                }
            }



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
        }

        if ($programa->programa_id == 4) { //PISO PELVICO
            $catInicial     = 0;
            $catFinal       = 0;
            $detalleRes     = "";
            $tipoRes        = "MCID 4 PUNTOS";

            $registroInicial = historial::where('registro_id', $request['idRegistro'])->where('estado_id', 1)->first(); //HISTORIA INICIAL
                $atributosIniciales = DB::table('ESCALAS.historial_resultado')->where('historial_idhistorial', $registroInicial->idhistorial)->get();

                $registroFinal = historial::where('registro_id', $request['idRegistro'])->where('estado_id', 2)->first(); //HISTORIA FINAL

                if ($registroFinal) {
                    $atributosFinales = DB::table('ESCALAS.historial_resultado')->where('historial_idhistorial', $registroFinal->idhistorial)->get();
                }else{
                    $registroFinal = historial::where('registro_id', $request['idRegistro'])->where('estado_id', 4)->first(); //HISTORIA FINAL
                    if ($registroFinal) {
                        $atributosFinales = DB::table('ESCALAS.historial_resultado')->where('historial_idhistorial', $registroFinal->idhistorial)->get();
                    }else{
                        $atributosFinales = [];
                    }
                }

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

        }

        return response()->json([
            'historial'         => $historial,
            'escalas'           => $escalasPerProg,
            "catInicial"        => $catInicial,
            "catFinal"          => $catFinal,
            "detalleRes"        => $detalleRes,
            "tipoRes"           => $tipoRes,
            'registroInicial'   => $registroInicial,
            'registroFinal'     => $registroFinal,
            'idRegistro'        => $registroFinal != null ? $registroFinal->idhistorial : null
        ], 200);
    }
}
