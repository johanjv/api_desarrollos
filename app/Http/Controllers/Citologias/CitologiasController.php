<?php

namespace App\Http\Controllers\Citologias;

use App\Http\Controllers\Controller;
use App\Models\Citologias\Citologia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitologiasController extends Controller
{
    public function getRegistrosdelDia(Request $request)
    {

        $prof = Auth()->user()->nro_doc;
        $citologiasHoy = Citologia::where('NRO_DOC_PROF', $prof)->whereDate('FECHA_ATENCION', Carbon::today())->get();

        $CountCitologiasHoy = Citologia::where('NRO_DOC_PROF', $prof)->whereDate('FECHA_ATENCION', Carbon::today())->count();
        $CountHistoricoCito = Citologia::where('NRO_DOC_PROF', $prof)->count();


        return response()->json([
            "citologiasHoy"         => $citologiasHoy,
            "CountCitologiasHoy"    => $CountCitologiasHoy,
            "CountHistoricoCito"    => $CountHistoricoCito
        ], 200);
    }

    public function saveCitologia(Request $request)
    {

        $prof = Auth()->user()->nro_doc;
        //return $request->all();

        Citologia::create([
            'NRO_DOC'           => $request['item']['nro_doc'],
            'NAP'               => $request['item']['nap'],
            'NOMBRES'           => strtoupper($request['item']['nombres']),
            'PRIMER_APELLIDO'   => strtoupper($request['item']['primer_apellido']),
            'SEGUNDO_APELLIDO'  => strtoupper($request['item']['segundo_apellido']),
            'EDAD'              => $request['item']['edad'],
            'ESQUEMA'           => $request['item']['esquema'],
            'NRO_DOC_PROF'      => $prof,
            'SEDE'              => $request['item']['sede'],
            'FECHA_ATENCION'    => date('Y-m-d h:i:s')
        ]);


        $citologiasHoy = Citologia::where('NRO_DOC_PROF', $prof)->whereDate('FECHA_ATENCION', Carbon::today())->get();

        $CountCitologiasHoy = Citologia::where('NRO_DOC_PROF', $prof)->whereDate('FECHA_ATENCION', Carbon::today())->count();
        $CountHistoricoCito = Citologia::where('NRO_DOC_PROF', $prof)->count();


        return response()->json([
            "citologiasHoy"         => $citologiasHoy,
            "CountCitologiasHoy"    => $CountCitologiasHoy,
            "CountHistoricoCito"    => $CountHistoricoCito
        ], 200);
    }

    public function saveEditCitologia(Request $request)
    {

        $prof = Auth()->user()->nro_doc;

        Citologia::where('ID', $request['item']['id'])->update([
            'NRO_DOC'           => $request['item']['nro_doc'],
            'NAP'               => $request['item']['nap'],
            'NOMBRES'           => strtoupper($request['item']['nombres']),
            'PRIMER_APELLIDO'   => strtoupper($request['item']['primer_apellido']),
            'SEGUNDO_APELLIDO'  => strtoupper($request['item']['segundo_apellido']),
            'EDAD'              => $request['item']['edad'],
            'ESQUEMA'           => $request['item']['esquema']
        ]);


        $citologiasHoy = Citologia::where('NRO_DOC_PROF', $prof)->whereDate('FECHA_ATENCION', Carbon::today())->get();

        $CountCitologiasHoy = Citologia::where('NRO_DOC_PROF', $prof)->whereDate('FECHA_ATENCION', Carbon::today())->count();
        $CountHistoricoCito = Citologia::where('NRO_DOC_PROF', $prof)->count();


        return response()->json([
            "citologiasHoy"         => $citologiasHoy,
            "CountCitologiasHoy"    => $CountCitologiasHoy,
            "CountHistoricoCito"    => $CountHistoricoCito
        ], 200);
    }

    public function getRegistros(Request $request)
    {

        $prof = Auth()->user()->nro_doc;
        $citologias = Citologia::where('NRO_DOC_PROF', $prof)->where('SEDE', $request['item']['sede'])
        ->whereBetween('FECHA_ATENCION', [$request['item']['fechaDesde']."T00:00:00.000", $request['item']['fechaHasta']."T23:59:59.999"])
        ->get();

        return response()->json([
            "citologias"         => $citologias,
        ], 200);
    }

}
