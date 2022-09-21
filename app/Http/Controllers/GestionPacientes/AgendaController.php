<?php

namespace App\Http\Controllers\GestionPacientes;

use App\Http\Controllers\Controller;
use App\Models\GestionPaciente\Agenda;
use App\Models\GestionPaciente\AgendaPura;
use App\Models\GestionPaciente\Medicos;
use App\Models\GestionPaciente\Turnos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SplStack;

class AgendaController extends Controller
{
    public function getAgenda(Request $request)
    {
        if ($request['tipoUser'] == 'front') {

            $agendaOrden        = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['131', '132'])->orderBy('facturado', 'ASC')->get();
            $agendaPendiente    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->where('estadoAtencion', '!=', 2)->orderBy('fecha_asignado', 'ASC')->get();
            $agendaAtendidos    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->where('estadoAtencion', 2)->get();
        }

        if ($request['tipoUser'] == 'medico'){

            $agendaOrden        = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['131', '132'])->orderBy('facturado', 'ASC')->get();
            $agendaPendiente    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->whereIn('estadoAtencion', [0,1])->where('medicoAsignado', Auth::user()->nro_doc)->orderBy('fecha_asignado', 'ASC')->get();
            $agendaAtendidos    = Agenda::with('profesional','consultorio')->where('medicoAsignado', Auth::user()->nro_doc)->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->where('estadoAtencion', 2)->get();
        }

        return response()->json([
            'agendaOrden' => $agendaOrden,
            'agendaPendiente' => $agendaPendiente,
            'agendaAtendidos' => $agendaAtendidos
        ], 200);
    }

    public function asignarPaciente(Request $request)
    {

        $validarMedicos = Medicos::where('estado', 1)->where('unidad', $request['unidadActiva'])->get();

        if (COUNT($validarMedicos) > 0) {

            $paciente = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('DocIden', $request['infoModal']['DocIden'])->update([
                'facturado' => 1,
                'frontAsignador' => Auth::user()->nro_doc
            ]);

            $medicos = Medicos::where('estado', 1)->where('unidad', $request['unidadActiva'])->orderBy('cupo', 'ASC')->get();

            $asignarA = 0;

            if ($request['infoModal']['tipoAsignacion'] == 'auto') {

                foreach ($medicos as $medico) {
                    if ($medico->cupo == 0) {

                        Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('DocIden', $request['infoModal']['DocIden'])->update([
                            'medicoAsignado' => $medico['docMedico'],
                            'fecha_asignado' => date('Y-m-d h:i:s'),
                            'tipoAsignacion' => $request['infoModal']['tipoAsignacion']
                        ]);

                        Medicos::where('docMedico', $medico['docMedico'])->update([
                            'cupo' => (intval($medico['cupo']) + 1),
                        ]);

                        $medicoASignado = $medico['nombMedico'];

                        $agendaOrden        = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['131', '132'])->orderBy('facturado', 'ASC')->get();
                        $agendaPendiente    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->whereIn('estadoAtencion', [0,1])->orderBy('fecha_asignado', 'ASC')->get();
                        $agendaAtendidos    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->where('estadoAtencion', 2)->get();

                        return response()->json([
                            'agendaOrden'       => $agendaOrden,
                            'agendaPendiente'   => $agendaPendiente,
                            'agendaAtendidos'   => $agendaAtendidos,
                            'asignarA'          => $medicoASignado,
                        ], 200);
                    }else{
                        $asignarA = 1;
                    }
                }

                if ($asignarA == 1) {

                    Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('DocIden', $request['infoModal']['DocIden'])->update([
                        'medicoAsignado' => $medicos[0]['docMedico'],
                        'fecha_asignado' => date('Y-m-d h:i:s'),
                        'tipoAsignacion' => $request['infoModal']['tipoAsignacion']
                    ]);

                    Medicos::where('docMedico', $medicos[0]['docMedico'])->update([
                        'cupo' => (intval($medicos[0]['cupo']) + 1),
                    ]);

                    $medicoASignado = $medicos[0]['nombMedico'];
                }
            }

            if ($request['infoModal']['tipoAsignacion'] == 'manual') {

                Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('DocIden', $request['infoModal']['DocIden'])->update([
                    'medicoAsignado' => $request['infoModal']['medico'],
                    'fecha_asignado' => date('Y-m-d h:i:s'),
                    'tipoAsignacion' => $request['infoModal']['tipoAsignacion']
                ]);

                $asignado = Medicos::where('estado', 1)->where('unidad', $request['unidadActiva'])->where('docMedico', $request['infoModal']['medico'])->first();

                Medicos::where('docMedico', $request['infoModal']['medico'])->update([
                    'cupo' => (intval($asignado['cupo']) + 1),
                ]);

                $medicoASignado = $asignado['nombMedico'];

            }
        } else {
            $medicoASignado = null;
        }



        $agendaOrden        = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['131', '132'])->orderBy('facturado', 'ASC')->get();
        $agendaPendiente    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->whereIn('estadoAtencion', [0,1])->orderBy('fecha_asignado', 'ASC')->get();
        $agendaAtendidos    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->where('estadoAtencion', 2)->get();

        return response()->json([
            'agendaOrden'       => $agendaOrden,
            'agendaPendiente'   => $agendaPendiente,
            'agendaAtendidos'   => $agendaAtendidos,
            'asignarA'          => $medicoASignado,
        ], 200);
    }

    public function getMedicosDisponibles(Request $request)
    {
        $medicos = Medicos::with('consultorio')->where('estado', 1)->where('unidad', $request['unidadActiva'])->orderBy('cupo', 'ASC')->get();

        return response()->json([
            'medicos' => $medicos
        ], 200);
    }

    public function atenderPaciente(Request $request)
    {

        $fecha = $request['accion'] == 'ini' ? 'fecha_ini_atencion' : 'fecha_fin_atencion';

        Agenda::where('DocIden', $request['paciente']['DocIden'])->update([
            'estadoAtencion'    => $request['accion'] == 'ini' ? 1 : 2,
            $fecha              => date('Y-m-d h:i:s')
        ]);

        $agendaOrden        = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['131', '132'])->orderBy('facturado', 'ASC')->get();
        $agendaPendiente    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->whereIn('estadoAtencion', [0,1])->where('medicoAsignado', Auth::user()->nro_doc)->orderBy('fecha_asignado', 'ASC')->get();
        $agendaAtendidos    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->where('estadoAtencion', 2)->get();

        return response()->json([
            'agendaOrden'       => $agendaOrden,
            'agendaPendiente'   => $agendaPendiente,
            'agendaAtendidos'   => $agendaAtendidos,
        ], 200);
    }

}
