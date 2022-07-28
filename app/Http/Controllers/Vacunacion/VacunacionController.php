<?php

namespace App\Http\Controllers\Vacunacion;

use App\Http\Controllers\Controller;
use App\Models\Vacunacion\Esquema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VacunacionController extends Controller
{
    public function getEsquemas(Request $request)
    {
        $esquemas = Esquema::all();

        return response()->json([
            "esquemas" => $esquemas
        ], 200);
    }

    public function saveEsquema(Request $request)
    {

        $esquema = Esquema::create([
            'nomb_esquema'  => strtoupper($request['params']['nomb_esquema']),
            'nro_dosis'     => $request['params']['nro_dosis']
        ]);

        $esquemas = Esquema::all();

        return response()->json([
            "esquemas" => $esquemas
        ], 200);
    }

    public function saveUpdEsquema(Request $request)
    {
        $esquema = Esquema::where('id', $request['params']['id'])->update([
            'nomb_esquema'  => strtoupper($request['params']['nomb_esquema']),
            'nro_dosis'     => $request['params']['nro_dosis'],
            'estado'     => $request['params']['estado']
        ]);

        $esquemas = Esquema::all();

        return response()->json([
            "esquemas" => $esquemas
        ], 200);
    }


}
