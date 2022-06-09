<?php

namespace App\Http\Controllers\EscalasRehabilitacion;

use App\Http\Controllers\Controller;
use App\Models\Escalas\Historial;
use App\Models\Escalas\Registros;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class HistorialRehabilitacionController extends Controller
{
    public function getHistorial(Request $request)
    {
        $registros = null;

        if ($request['fechaDesde'] != null && $request['fechaHasta'] != null) {
            $registros = Registros::with(['programas', 'afiliado','abandono'])->whereBetween('fecha_inicio', [$request['fechaDesde'] . "T00:00:00.000",$request['fechaHasta'] . "T23:59:59.999"])->get();
        }
        if ($request['fechaDesde'] != null && $request['fechaHasta'] != null && $request['nro_doc'] != null) {
            $registros = Registros::with(['programas', 'afiliado','abandono'])->whereBetween('fecha_inicio', [$request['fechaDesde'] . "T00:00:00.000",$request['fechaHasta'] . "T23:59:59.999"])
                ->where('afiliado_id', $request['nro_doc'])
            ->get();
        }
        if ($request['fechaDesde'] != null && $request['fechaHasta'] != null && $request['nro_doc'] != null && $request['programa'] != 0) {
            $registros = Registros::with(['programas', 'afiliado','abandono'])->whereBetween('fecha_inicio', [$request['fechaDesde'] . "T00:00:00.000",$request['fechaHasta'] . "T23:59:59.999"])
                ->where('afiliado_id', $request['nro_doc'])->where('programa_id', $request['programa'])
            ->get();
        }

        if ($request['fechaDesde'] == null && $request['fechaHasta'] == null && $request['nro_doc'] != null && $request['programa'] == 0) {
            $registros = Registros::with(['programas', 'afiliado','abandono'])->where('afiliado_id', $request['nro_doc'])->get();
        }

        if ($request['fechaDesde'] == null && $request['fechaHasta'] == null && $request['nro_doc'] == null && $request['programa'] != 0) {
            $registros = Registros::with(['programas', 'afiliado','abandono'])->where('programa_id', $request['programa'])->get();
        }


        return response()->json([
            "registros"   => $registros,
        ], 200);
    }
}
