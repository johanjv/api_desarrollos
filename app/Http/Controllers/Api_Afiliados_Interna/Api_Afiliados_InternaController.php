<?php

namespace App\Http\Controllers\Api_Afiliados_Interna;


use App\Http\Controllers\Controller;
use App\Models\Api_Afiliados_Interna\Afiliados;
use Illuminate\Http\Request;
use DB;

class Api_Afiliados_InternaController extends Controller
{
    public function getAfiliado(Request $request){

        $afiliado = Afiliados::where('Documento', $request['nro_doc'])->first();

        return response()->json([
            "afiliado"    => $afiliado
        ], 200);
    }
}
