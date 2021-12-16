<?php

namespace App\Http\Controllers\CertificadosEscolares;

use App\Http\Controllers\Controller;
use App\Models\CertificadosEscolares\AfiliadosEscolares;
use Illuminate\Http\Request;

class CertificadosEscolaresController extends Controller
{
    public function generarPdf(Request $request)
    {
        $dataAfiliado = AfiliadosEscolares::where('Documento', $request["doc"])->first();
        return response()->json(["dataAfiliado" => $dataAfiliado], 200);
    }
}
