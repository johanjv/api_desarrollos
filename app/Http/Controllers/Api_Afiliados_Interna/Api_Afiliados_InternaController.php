<?php

namespace App\Http\Controllers\Api_Afiliados_Interna;


use App\Http\Controllers\Controller;
use App\Models\Api_Afiliados_Interna\Afiliados;
use App\Models\Api_Afiliados_Interna\AfiliadosCapitacion;
use App\Models\Api_Afiliados_Interna\TiposDoc;
use Illuminate\Http\Request;
use DB;

class Api_Afiliados_InternaController extends Controller
{
    public function getAfiliado(Request $request){

        if (isset($request['tipoDoc'])) {
            $afiliado = Afiliados::where('Documento', $request['nro_doc'])->where('ID_TP_TipoIdentificacion', $request['tipoDoc'])->first();
        }else{
            $afiliado = Afiliados::where('Documento', $request['nro_doc'])->first();
        }

        if ($afiliado == null) {
            AfiliadosCapitacion::where('Documento', $request['nro_doc'])->first();
        }

        return response()->json([
            "afiliado"    => $afiliado
        ], 200);
    }

    public function getTiposDocs(Request $request){

        $tiposDocs = TiposDoc::all();

        return response()->json([
            "tiposDocs"    => $tiposDocs
        ], 200);
    }
}
