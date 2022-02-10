<?php

namespace App\Http\Controllers\GestionResiduos;

use App\Http\Controllers\Controller;
use App\Models\Residuos\Clasificacion;
use Illuminate\Http\Request;


class GestionResiduosController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getDataCalendar(Request $request)
    {
        return "ENTRO";
    }

    public function getClasif(Request $request)
    {
        $clasificacion = Clasificacion::with([
            'categoria' => function($q) {
                $q->with([
                    'residuos'
                    ]);
                }])->orderBy('id_clasif_residuos', 'DESC')->get();

        return response()->json([
            "clasificacion"    => $clasificacion,
        ], 200);
    }

}
