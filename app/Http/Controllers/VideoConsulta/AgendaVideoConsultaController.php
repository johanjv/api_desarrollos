<?php

namespace App\Http\Controllers\VideoConsulta;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Api_Afiliados_Interna\Afiliados;
use App\Models\VideoConsulta\Agenda;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AgendaVideoConsultaController extends Controller
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

    public function getAgenda(Request $request)
    {
        $agenda = Agenda::whereBetween('FECHA', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidad'])->get();

        return response()->json([
            "agenda"   => $agenda,
        ], 200);

    }

}
