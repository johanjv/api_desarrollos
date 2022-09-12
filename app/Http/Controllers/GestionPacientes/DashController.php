<?php

namespace App\Http\Controllers\GestionPacientes;
use App\Http\Controllers\Controller;
use App\Models\GestionPaciente\Agenda;
use App\Models\GestionPaciente\Medicos;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashController extends Controller
{
    public function getDetalleDash(Request $request)
    {

        $medicosDisponibles = Medicos::where('estado', 1)->where('unidad', $request['unidadActiva'])->get();
        $agendasDisponibles = Agenda::with('profesional','consultorio')->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['131', '132'])->orderBy('estadoAtencion', 'ASC')->get();
        //$registroDelDia     = Agenda::with('profesional','consultorio')->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['131', '132'])->get();

        //$medicos = Agenda::with('profesional','consultorio')->distinct('GESTIONPACIENTES.citas.medicoAsignado')->join('users', 'users.nro_doc', 'GESTIONPACIENTES.citas.medicoAsignado')->get();

        /* select distinct medicoAsignado from [GESTIONPACIENTES].[citas] inner join [users] on [users].[nro_doc] = [GESTIONPACIENTES].[citas].medicoAsignado */

        $medicos = DB::table('GESTIONPACIENTES.citas')->select('medicoAsignado')->where('CODIGOIPS', $request['unidadActiva'])
            ->join('users', 'users.nro_doc', 'GESTIONPACIENTES.citas.medicoAsignado')->distinct()->get();



        $medicos->map(function($item) use($request){
            $item->cantidadAsignados  = Agenda::with('profesional','consultorio')->where('CODIGOIPS', $request['unidadActiva'])->where('medicoAsignado', $item->medicoAsignado)->whereIn('Estado', ['131', '132'])->count();
            $item->cantidadEsperando = Agenda::with('profesional','consultorio')->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 0)->where('medicoAsignado', $item->medicoAsignado)->whereIn('Estado', ['131', '132'])->count();
            $item->cantidadAtendiendo = Agenda::with('profesional','consultorio')->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 1)->where('medicoAsignado', $item->medicoAsignado)->whereIn('Estado', ['131', '132'])->count();
            $item->cantidadAtendidos  = Agenda::with('profesional','consultorio')->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 2)->where('medicoAsignado', $item->medicoAsignado)->whereIn('Estado', ['131', '132'])->count();
            $item->dataMedico         = User::where('nro_doc', $item->medicoAsignado)->first();
        });


        $medicosDisponibles->map(function($item){
            $item->cantidadAtenciones = Agenda::where('medicoAsignado', $item->docMedico)->where('estadoAtencion', 2)->count();
        });

        return response()->json([
            "medicosDisponibles"    => $medicosDisponibles,
            "agendasDisponibles"    => $agendasDisponibles,
            "medicos"               => $medicos
        ], 200);
    }
}
