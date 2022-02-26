<?php

namespace App\Http\Controllers\MamitasSeguras;

use App\Http\Controllers\Controller;
use App\Models\Bitacora\Bitacora;
use App\Models\MamitasSeguras\Mamitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MamitasController extends Controller
{
    public function getRegistrosdelDiaMamitas(Request $request)
    {
        $prof = Auth()->user()->nro_doc;
        $registrosHoy = Mamitas::with('municipio', 'localidad')->where('NRO_DOC_PROF', $prof)->whereDate('FECHA_REGISTRO', Carbon::now())->where('SEDE', $request['item']['sede'])->get();

        $CountRegistrosHoy = Mamitas::with('municipio', 'localidad')->where('NRO_DOC_PROF', $prof)->whereDate('FECHA_REGISTRO', Carbon::now())->where('SEDE', $request['item']['sede'])->count();
        $CountHistorico = Mamitas::with('municipio', 'localidad')->where('NRO_DOC_PROF', $prof)->count();


        return response()->json([
            "registrosHoy"         => $registrosHoy,
            "CountRegistrosHoy"    => $CountRegistrosHoy,
            "CountHistorico"    => $CountHistorico
        ], 200);
    }

    public function saveGestante(Request $request)
    {
        $prof = Auth()->user()->nro_doc;
        $saveReg = Mamitas::create([
            'DOC'               => $request['item']['doc'],
            'NOMBRES'           => strtoupper($request['item']['nombre_completo']),
            'FECHA_NAC'         => $request['item']['fecha_nac'],
            'CORREO'            => strtoupper($request['item']['correo']),
            'CELULAR'           => $request['item']['tlf'],
            'LOCALIDAD_ID'      => $request['item']['localidad'] != null ? $request['item']['localidad']['ID'] : null,
            'MUNICIPIO_ID'      => $request['item']['municipio']['ID'],
            'SEDE'              => $request['item']['sede'],
            'NRO_DOC_PROF'      => $prof,
            'EDAD_GEST'         => $request['item']['edad_gest'],
            'FECHA_REGISTRO'    => Carbon::now()
        ]);

        $registrosHoy = Mamitas::with('municipio', 'localidad')->where('NRO_DOC_PROF', $prof)->whereDate('FECHA_REGISTRO', Carbon::now())->where('SEDE', $request['item']['sede'])->get();

        $CountRegistrosHoy = Mamitas::with('municipio', 'localidad')->where('NRO_DOC_PROF', $prof)->whereDate('FECHA_REGISTRO', Carbon::now())->where('SEDE', $request['item']['sede'])->count();
        $CountHistorico = Mamitas::with('municipio', 'localidad')->where('NRO_DOC_PROF', $prof)->count();


        /* REGISTRO EN BITACORA */
        Bitacora::create(['ID_APP' => $request['item']["idApp"],'USER_ACT' => $request->user()->nro_doc,'ACCION' => 'CREAR - NUEVA REGISTRO GESTANTE PARA EL DOCUMENTO ' . $request['item']['doc'],'FECHA' => date('Y-m-d h:i:s'),'USER_EMPRESA' => $request->user()->empresa]);

        return response()->json([
            "registrosHoy"         => $registrosHoy,
            "CountRegistrosHoy"    => $CountRegistrosHoy,
            "CountHistorico"    => $CountHistorico
        ], 200);
    }

    public function getRegistrosMamitas(Request $request)
    {
        $prof = Auth()->user()->nro_doc;
        $registros = Mamitas::with('municipio', 'localidad')->where('NRO_DOC_PROF', $prof)->where('SEDE', $request['item']['sede'])
        ->whereBetween('FECHA_REGISTRO', [$request['item']['fechaDesde']."T00:00:00.000", $request['item']['fechaHasta']."T23:59:59.999"])
        ->get();

        return response()->json([
            "registros"         => $registros,
        ], 200);
    }

    public function saveEditGestante(Request $request)
    {
        $itemEdit = Mamitas::where('ID', $request['item']["idItem"])->update([
            'NOMBRES'           => strtoupper($request['item']['nombres']),
            'FECHA_NAC'         => $request['item']['fechaNac'],
            'CORREO'            => strtoupper($request['item']['correo']),
            'CELULAR'           => $request['item']['tlf'],
            'EDAD_GEST'         => $request['item']['edadGest'],
        ]);

        $prof = Auth()->user()->nro_doc;
        $registrosHoy = Mamitas::with('municipio', 'localidad')->where('NRO_DOC_PROF', $prof)->whereDate('FECHA_REGISTRO', Carbon::now())->where('SEDE', $request['item']['sede'])->get();

        $CountRegistrosHoy = Mamitas::with('municipio', 'localidad')->where('NRO_DOC_PROF', $prof)->whereDate('FECHA_REGISTRO', Carbon::now())->where('SEDE', $request['item']['sede'])->count();
        $CountHistorico = Mamitas::with('municipio', 'localidad')->where('NRO_DOC_PROF', $prof)->count();

        return response()->json([
            "registrosHoy"      => $registrosHoy,
            "CountRegistrosHoy" => $CountRegistrosHoy,
            "CountHistorico"    => $CountHistorico
        ], 200);
    }

}
