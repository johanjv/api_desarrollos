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
        /* $dataAfiliado = AfiliadosEscolares::where('Documento', $request["doc"])->first();
        if ($dataAfiliado) {
            DB::connection('sqlsrv')->table('ESCOLAR.HISTORIAL_CERT_ESCOLAR')->insert([
                "DOC"   => $request["doc"],
                "IP"    => $request->ip(),
                "FECHA" => date("Y-m-d h:i:s")
            ]);
        } */

        $dataAfiliado = DB::table('UNIDADES_ESTANDAR')->whereIn('ID_UNIDAD', [505, 1001, 31585, 32300, 31586, 1354, 2040, 32531 , 31622, 1027 , 1032, 5133, 1128 , 1026])->get();

        return response()->json(["dataAfiliado" => $dataAfiliado], 200);
    }

}
