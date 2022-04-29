<?php

namespace App\Http\Controllers\EscalasRehabilitacion;

use App\Http\Controllers\Controller;
use App\Models\Escalas\Abandonos;
use App\Models\Escalas\Programa;
use App\Models\Escalas\Registros;
use Illuminate\Http\Request;

class EscalasRehabilitacionController extends Controller
{
    public function getProgramasPerAfi(Request $request)
    {
        $programas = Registros::with(['programas','afiliado'])->where('afiliado_id', $request['nro_doc'])->get();

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
}
