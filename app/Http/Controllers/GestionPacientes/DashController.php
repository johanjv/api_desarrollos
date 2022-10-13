<?php

namespace App\Http\Controllers\GestionPacientes;
use App\Http\Controllers\Controller;
use App\Models\GestionPaciente\Agenda;
use App\Models\GestionPaciente\Medicos;
use App\Models\GestionPaciente\Turnos;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashController extends Controller
{
    public function getDetalleDash(Request $request)
    {

        $fechaDesde = isset($request['fechaDesde']) ? $request['fechaDesde']."T00:00:00.000" : date('Y-m-d h:i:s');
        $fechaHasta = isset($request['fechaHasta']) ? $request['fechaHasta']."T23:59:59.999" : date('Y-m-d h:i:s');

        /* return $fechaDesde; */

        /* PESTAÑA  1 DEL DASHBOARD */

        $medicosDisponibles = Medicos::where('GESTIONPACIENTES.medicosDisponibles.estado', 1)
            ->join('GESTIONPACIENTES.turnos', 'GESTIONPACIENTES.turnos.docMedico', 'GESTIONPACIENTES.medicosDisponibles.docMedico')
            ->where('GESTIONPACIENTES.medicosDisponibles.unidad', $request['unidadActiva'])
            ->whereBetween('fecha_turno', [$fechaDesde, $fechaHasta])
        ->get();

        $agendasDisponibles = Agenda::with('profesional','consultorio')
            ->whereBetween('Fecha', [$fechaDesde, $fechaHasta])
            ->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['127','131', '132'])
            ->orderBy('FECHA', 'ASC')
            ->orderBy('facturado', 'DESC')
            ->orderBy('estadoAtencion', 'ASC')
        ->get();

        $medicos = DB::table('GESTIONPACIENTES.citas')->select('medicoAsignado')->where('CODIGOIPS', $request['unidadActiva'])
            ->whereBetween('Fecha', [$fechaDesde, $fechaHasta])
            ->join('users', 'users.nro_doc', 'GESTIONPACIENTES.citas.medicoAsignado')->distinct()
        ->get();

        $medicos->map(function($item) use($request, $fechaDesde, $fechaHasta){
            $item->cantidadAsignados  = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('medicoAsignado', $item->medicoAsignado)->whereIn('Estado', ['127','131', '132'])->count();
            $item->cantidadEsperando  = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 0)->where('medicoAsignado', $item->medicoAsignado)->whereIn('Estado', ['127','131', '132'])->count();
            $item->cantidadAtendiendo = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 1)->where('medicoAsignado', $item->medicoAsignado)->whereIn('Estado', ['127','131', '132'])->count();
            $item->cantidadAtendidos  = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 2)->where('medicoAsignado', $item->medicoAsignado)->whereIn('Estado', ['127','131', '132'])->count();
            $item->cantidadInasistentes  = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 5)->whereIn('Estado', ['127','131', '132'])->count();
            $item->dataMedico         = User::where('nro_doc', $item->medicoAsignado)->first();
            $item->dashMedico         = Turnos::where('docMedico', $item->medicoAsignado)->whereBetween('fecha_turno', [$fechaDesde, $fechaHasta])->where('unidad', $request['unidadActiva'])->first();
        });


        $medicosDisponibles->map(function($item) use($request, $fechaDesde, $fechaHasta){
            $item->cantidadAsignados  = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('medicoAsignado', $item->docMedico)->whereIn('Estado', ['127','131', '132'])->count();
            $item->cantidadEsperando = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 0)->where('medicoAsignado', $item->docMedico)->whereIn('Estado', ['127','131', '132'])->count();
            $item->cantidadAtendiendo = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 1)->where('medicoAsignado', $item->docMedico)->whereIn('Estado', ['127','131', '132'])->count();
            $item->cantidadAtendidos  = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 2)->where('medicoAsignado', $item->docMedico)->whereIn('Estado', ['127','131', '132'])->count();
            $item->cantidadInasistentes  = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 5)->whereIn('Estado', ['127','131', '132'])->count();
            $item->dataMedico         = User::where('nro_doc', $item->docMedico)->first();
            $item->dashMedico         = Turnos::where('docMedico', $item->docMedico)->whereBetween('fecha_turno', [$fechaDesde, $fechaHasta])->where('unidad', $request['unidadActiva'])->first();
        });

        $dataGrafico = array([
            'cantidadAsignados'  => Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['127','131', '132'])->where('estadoAtencion', 0)->where('facturado', 1)->count(),
            'cantidadEsperando'  => Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 0)->whereIn('Estado', ['127','131', '132'])->count(),
            'cantidadAtendiendo' => Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 1)->whereIn('Estado', ['127','131', '132'])->count(),
            'cantidadAtendidos'  => Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 2)->whereIn('Estado', ['127','131', '132'])->count(),
            'cantidadInasistentes'  => Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 5)->whereIn('Estado', ['127','131', '132'])->count(),
        ]);

        /* FIN DE PESTAÑA 1 */

        /* PESTAÑA  1 DEL DASHBOARD */

        $agenda     = Agenda::with('profesional','consultorio')->where('isAgendado', 1)->whereBetween('Fecha', [$fechaDesde, $fechaHasta])->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['127','131', '132'])->orderBy('facturado', 'DESC')->orderBy('estadoAtencion', 'ASC')->count();

        $atendidos  = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])
            ->where('CODIGOIPS', $request['unidadActiva'])->whereIn('estadoAtencion', [2])
            ->whereIn('Estado', ['127','131', '132'])
        ->count();

        $inasistentes = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])
            ->where('CODIGOIPS', $request['unidadActiva'])->where('estadoAtencion', 5)
            ->whereIn('Estado', ['127','131', '132'])
        ->count();

        $estadoAgenda = $atendidos != null ? (($atendidos/$agenda)*100) : 0;

        $inicioAgenda     = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])
            ->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['127','131', '132'])->orderBy('Fecha', 'DESC')->first();

        $finalAgenda     = Agenda::with('profesional','consultorio')->whereBetween('Fecha', [$fechaDesde, $fechaHasta])
            ->where('CODIGOIPS', $request['unidadActiva'])->whereIn('Estado', ['127','131', '132'])->orderBy('Fecha', 'ASC')->first();

        $fechaActual = date('h');


        /* FIN DE PESTAÑA 1 */

        return response()->json([
            "medicosDisponibles"    => $medicosDisponibles,
            "agendasDisponibles"    => $agendasDisponibles,
            "medicos"               => $medicos,
            "dataGrafico"           => $dataGrafico,

            "estadoAgenda"          => $estadoAgenda,

            "fechaActual"           => $fechaActual,
            'inicioAgenda'          => $inicioAgenda,
            'finalAgenda'           => $finalAgenda,
            'inasistentes'          => $inasistentes

        ], 200);
    }

    public function updateVariablesMed(Request $request)
    {
        $fechaDesde = isset($request['fechaDesde']) ? $request['fechaDesde']."T00:00:00.000" : date('Y-m-d h:i:s');
        $fechaHasta = isset($request['fechaHasta']) ? $request['fechaHasta']."T23:59:59.999" : date('Y-m-d h:i:s');

        $actualizar = Turnos::where('docMedico', $request['item']['medico']['dashMedico']['docMedico'])->whereBetween('fecha_turno', [$fechaDesde, $fechaHasta])
            ->where('unidad', $request['item']['unidadActiva'])
        ->update([
            'meta'          => $request['item']['medico']['dashMedico']['meta'],
            'ini_turno'     => $request['item']['medico']['dashMedico']['ini_turno'],
            'fin_turno'     => $request['item']['medico']['dashMedico']['fin_turno'],
            'horas_turno'   => (intval($request['item']['medico']['dashMedico']['fin_turno']) - intval($request['item']['medico']['dashMedico']['ini_turno']))
        ]);

        return response()->json([
            "actualizar"    => $actualizar
        ], 200);
    }
}
