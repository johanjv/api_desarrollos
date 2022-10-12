<?php

namespace App\Http\Controllers\GestionPacientes;

use App\Models\GestionPaciente\AgendaPura;
use App\Models\GestionPaciente\Medicos;
use App\Models\GestionPaciente\Agenda;
use App\Models\GestionPaciente\Turnos;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use SplStack;
use App\User;

class AgendaController extends Controller
{
    public function getAgenda(Request $request)
    {
        $medico = null;
        $medicos = null;
        $fechaDesde = isset($request['fechaDesde']) ? $request['fechaDesde']."T00:00:00.000" : date('Y-m-d')."T00:00:00.000";
        $fechaHasta = isset($request['fechaHasta']) ? $request['fechaHasta']."T23:59:59.999" : date('Y-m-d')."T23:59:59.999";
        //return $fechaDesde;

        if ($request['tipoUser'] == 'front') {
            
            $medicos = Medicos::where('GESTIONPACIENTES.medicosDisponibles.estado', 1)
                ->join('GESTIONPACIENTES.turnos', 'GESTIONPACIENTES.turnos.docMedico', 'GESTIONPACIENTES.medicosDisponibles.docMedico')
                ->where('GESTIONPACIENTES.medicosDisponibles.unidad', $request['unidadActiva'])
                ->where('fecha_turno', date('Y-m-d'))
            ->get();

            //return $medicos[0]['docMedico'];
            
            $medicos->map(function($item) use($request, $fechaDesde, $fechaHasta){
                $item->cantidadAsignados  = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('medicoAsignado', $item->docMedico)->whereIn('Estado', ['127','131', '132'])->count();
                $item->cantidadEsperando  = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 0)->where('medicoAsignado', $item->docMedico)->whereIn('Estado', ['127','131', '132'])->count();
                $item->cantidadAtendiendo = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 1)->where('medicoAsignado', $item->docMedico)->whereIn('Estado', ['127','131', '132'])->count();
                $item->cantidadAtendidos  = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 2)->where('medicoAsignado', $item->docMedico)->whereIn('Estado', ['127','131', '132'])->count();
                $item->dataMedico         = User::where('nro_doc', $item->medicoAsignado)->first();
                $item->dashMedico         = Turnos::where('docMedico', $item->medicoAsignado)->whereBetween('fecha_turno', [$fechaDesde, $fechaHasta])->where('unidad', $request['unidadActiva'])->first();
            });

            $agendaOrden        = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['127','131', '132'])->orderBy('facturado', 'ASC')->get();
            $agendaPendiente    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->where('estadoAtencion', '!=', 2)->orderBy('FECHA', 'ASC')->get();
            $agendaAtendidos    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->where('estadoAtencion', 2)->get();
        }

        if ($request['tipoUser'] == 'medico' || $request['tipoUser'] == 'admin'){
            $medico             = Medicos::with('consultorio')->where('estado', 1)->where('unidad', $request['unidadActiva'])->where('docMedico', Auth::user()->nro_doc)->first();
            $agendaOrden        = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['127','131', '132'])->orderBy('facturado', 'ASC')->get();
            $agendaPendiente    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->whereIn('estadoAtencion', [0,1])->where('medicoAsignado', Auth::user()->nro_doc)->orderBy('FECHA', 'ASC')->get();
            $agendaAtendidos    = Agenda::with('profesional','consultorio')->where('medicoAsignado', Auth::user()->nro_doc)->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->where('estadoAtencion', 2)->get();
        }

        return response()->json([
            'agendaOrden'       => $agendaOrden,
            'agendaPendiente'   => $agendaPendiente,
            'agendaAtendidos'   => $agendaAtendidos,
            'medicosDisp'       => $medicos,
            'medico'            => $medico
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

                        $agendaOrden        = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['127','131', '132'])->orderBy('facturado', 'ASC')->get();
                        $agendaPendiente    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->whereIn('estadoAtencion', [0,1])->orderBy('FECHA', 'ASC')->get();
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



        $agendaOrden        = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['127','131', '132'])->orderBy('facturado', 'ASC')->get();
        $agendaPendiente    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->whereIn('estadoAtencion', [0,1])->orderBy('FECHA', 'ASC')->get();
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

        $agendaOrden        = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['127','131', '132'])->orderBy('facturado', 'ASC')->get();
        $agendaPendiente    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->whereIn('estadoAtencion', [0,1])->where('medicoAsignado', Auth::user()->nro_doc)->orderBy('FECHA', 'ASC')->get();
        $agendaAtendidos    = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [date('Y-m-d 00:00:00.000'), date('Y-m-d 23:59:59.999')])->where('CODIGOIPS', $request['unidadActiva'])->where('facturado', 1)->where('estadoAtencion', 2)->get();

        return response()->json([
            'agendaOrden'       => $agendaOrden,
            'agendaPendiente'   => $agendaPendiente,
            'agendaAtendidos'   => $agendaAtendidos,
        ], 200);
    }

    public function addExtra(Request $request)
    {
        $statusInsert = 0;

        $paciente = Agenda::where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', [127,131,132])->where('DocIden', $request['afiliado']['Documento'])->get();
        /* return $paciente; */

        if (COUNT($paciente) <= 0) {
            $insert = Agenda::create([
                'CODIGOIPS'         => $request['unidadActiva'],
                'FECHA'             => date('Y-m-d H:i:s'),
                'Estado'            => '132',
                'DocIden'           => $request['afiliado']['Documento'],
                'NombreSolicit'     => $request['afiliado']['PrimerNombre'] . " " . $request['afiliado']['SegundoNombre'],
                'ApellidosSolicit'  => $request['afiliado']['PrimerApellido'] . " " . $request['afiliado']['SegundoApellido'],
                'nap'               => $request['nap']
            ]);
            $statusInsert = 1;
        }


        return response()->json([
            'statusInsert'       => $statusInsert,
        ], 200);


    }

    public function cambiarEstadoPaciente(Request $request)
    {
        $registroAgenda = Agenda::where('CODIGOIPS', $request['unidadActiva'])
            ->where('DocIden', $request['afiliado']['DocIden'])
            ->where('nap', $request['afiliado']['nap'])
        ->update([
            'estadoAtencion' => $request['estado'],
            'observaciones'  => $request['observaciones']
        ]);


        return $registroAgenda;
    }



}
