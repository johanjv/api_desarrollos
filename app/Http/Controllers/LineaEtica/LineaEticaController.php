<?php

namespace App\Http\Controllers\LineaEtica;

use App\Http\Controllers\Controller;
use App\Models\Bitacora\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\LineaEtica\Registros;

class LineaEticaController extends Controller
{
    public function getRegistros(Request $request)
    {
        if (isset($request['inicio']) && isset($request['final'])){

        $registros=Registros::whereBetween('Fecha_radicacion', [$request['inicio'],$request['final']])->get();
        }else{
            $registros=Registros::all();
        }
        return response()->json([
            "registros"         => $registros,

        ], 200);
    }

    

}
