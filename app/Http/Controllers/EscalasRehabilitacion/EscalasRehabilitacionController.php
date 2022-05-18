<?php

namespace App\Http\Controllers\EscalasRehabilitacion;

use App\Http\Controllers\Controller;
use App\Models\Escalas\Abandonos;
use App\Models\Escalas\Programa;
use App\Models\Escalas\Registros;
use Illuminate\Http\Request;
use DB;

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
        $programas = Registros::with(['programas','afiliado'])->where('afiliado_id', $request['nro_doc'])->where('abandono', '!=', 'SI')->get();

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

        $registro = Registros::where('idRegistro', $request['detalleAfi']['idRegistro'])->first();


        return response()->json([
            "registro"   => $registro,
        ], 200);
    }

    public function getDiagnosticos(Request $request)
    {
        $diagnosticos = DB::connection('sqlsrv_2')->table('Parametrico.TP_Diagnostico')->get();


        return response()->json([
            "diagnosticos"   => $diagnosticos,
        ], 200);
    }
}
