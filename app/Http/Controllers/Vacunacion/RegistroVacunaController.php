<?php

namespace App\Http\Controllers\Vacunacion;

use App\Http\Controllers\Controller;
use App\Models\Vacunacion\Esquema;
use App\Models\Vacunacion\Paciente;
use App\Models\Vacunacion\Registro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegistroVacunaController extends Controller
{
    public function getPrevios(Request $request)
    {
        $registros = Registro::with(['paciente'])->where('estado_id', 0)->where('unidad', $request['unidad'])->get();

        return response()->json([
            "registros" => $registros
        ], 200);
    }
}
