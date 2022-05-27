<?php

namespace App\Http\Controllers\EscalasRehabilitacion;

use App\Http\Controllers\Controller;
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
    public function getProgramas(Request $request)
    {
        $programas = Programa::all();

        return response()->json([
            "programas"   => $programas,
        ], 200);
    }

    public function getProgramasPerAfi(Request $request)
    {
        $programas = Registros::with(['programas','afiliado'])->where('afiliado_id', $request['nro_doc'])->where('abandono', 'NO')->get();

        return response()->json([
            "programas"   => $programas,
        ], 200);
    }

    public function getAbandonos(Request $request)
    {
        $abandonos = Abandonos::all();

        return response()->json([
            "abandonos"   => $abandonos,
        ], 200);
    }

    public function getEscalasPerPrograma(Request $request)
    {
        $programas = Programa::with([
            'escalas'=> function($q) {
                $q->with(['detalleEscalas', 'atributos']);
            }
        ])->where('idPrograma', $request['idPrograma'])->first();

        return response()->json([
            "programas"   => $programas,
        ], 200);
    }

    public function saveRegistroAfi(Request $request)
    {
        //return $request->all();
        $registro = Registros::where('idRegistro', $request['detalleAfi']['idRegistro'])->update([
            'fecha_fin'         => $request['fecha_final'],
            'tiempo_atencion'   => $request['tiempoAtencion'],
            'sesiones'          => $request['sesiones'],
            'abandono'          => $request['abandono']['id'],
            'abandono_id'       => $request['abandono']['id']
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

                $result = DB::table('ESCALAS.resultado')->where('escala_id', $escala["escala_idescala"])->where('estado_idestado', 1)->first(); //1 porque es inicial

                foreach ($escala["atributos"] as $atributo) {
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

                $result = DB::table('ESCALAS.resultado')->where('escala_id', $escala["escala_idescala"])->where('estado_idestado', 2)->first(); //1 porque es inicial

                foreach ($escala["atributos"] as $atributo) {
                    if ($atributo["estado_idestado"] == 2) {
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
            return "programa 1";
        }

        if ($request['registro']['detalleAfi']['programa_id'] == 2) { //PULMONAR
            return "programa 2";
        }

        if ($request['registro']['detalleAfi']['programa_id'] == 3) { //ACONDICIONAMIENTO

            $registros = historial::where('registro_id', $request['idRegistro']["registro"])->where('estado_id', 1)->get(); //HISTORIA INICIAL
            $registros = historial::where('registro_id', $request['idRegistro']["registro"])->where('estado_id', 2)->get(); //HISTORIA FINAL






            return "programa 3";
        }

        if ($request['registro']['detalleAfi']['programa_id'] == 4) { //PISO PELVICO
            return "programa 4";
        }


        return $request->all();

    }



}
