<?php

namespace App\Http\Controllers\CertificadosEscolares;

use App\Http\Controllers\Controller;
use App\Models\CertificadosEscolares\AfiliadosEscolares;
use Illuminate\Http\Request;
use DB;

class CertificadosEscolaresController extends Controller
{
    public function generarPdf(Request $request)
    {
        $dataAfiliado = AfiliadosEscolares::where('Documento', $request["doc"])->first();
        DB::connection('sqlsrv')->table('ESCOLAR.HISTORIAL_CERT_ESCOLAR')->insert([
            "DOC"   => $request["doc"],
            "IP"    => $request->ip(),
            "FECHA" => date("Y-m-d h:i:s")
        ]);

        return response()->json(["dataAfiliado" => $dataAfiliado], 200);
    }

}
