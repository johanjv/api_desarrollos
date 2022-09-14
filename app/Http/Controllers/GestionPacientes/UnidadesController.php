<?php

namespace App\Http\Controllers\GestionPacientes;
use App\Http\Controllers\Controller;
use App\Models\GestionPaciente\Consultorios;
use App\Models\GestionPaciente\Medicos;
use App\Models\Unidad;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnidadesController extends Controller
{
    public function getConsultoriosPerUnidad(Request $request)
    {

        $consultorios = Consultorios::with('profesional')->where('id_unidad', $request['unidad'])->where('doc_prof', Auth::user()->nro_doc)->get();
        $seleccion = null;

        if (COUNT($consultorios) > 0) {
            $seleccion = $consultorios;
        }else{
            $seleccion = null;
        }

        $select = $seleccion == null ? null : $seleccion[0];

        $consultorios = Consultorios::with('profesional')->where('id_unidad', $request['unidad'])->where('doc_prof', null)->get();
        $consultoriosOcupados = Consultorios::with('profesional')->where('id_unidad', $request['unidad'])->where('doc_prof', '!=', null)->get();

        return response()->json([
            "seleccion"     => $select,
            "consultorios"  => $consultorios,
            "consultoriosOcupados"  => $consultoriosOcupados,
        ], 200);
    }

    public function asignarConsultorio(Request $request)
    {
        $asignacion = Consultorios::where('id_unidad', $request['item']['unidad'])->where('id', $request['item']['consultorio'])->update([
            'doc_prof' => Auth::user()->nro_doc
        ]);

        $consultorios = Consultorios::with('profesional')->where('id_unidad', $request['item']['unidad'])->where('doc_prof', Auth::user()->nro_doc)->get();
        $seleccion = null;

        if (COUNT($consultorios) > 0) {
            $seleccion = $consultorios;
        }else{
            $seleccion = null;
        }

        $select = $seleccion == null ? null : $seleccion[0];

        $consultorios = Consultorios::with('profesional')->where('id_unidad', $request['item']['unidad'])->where('doc_prof', null)->get();
        $consultoriosOcupados = Consultorios::with('profesional')->where('id_unidad', $request['item']['unidad'])->where('doc_prof', '!=', null)->get();

        Medicos::create([
            'docMedico'     => Auth::user()->nro_doc,
            'nombMedico'    => strtoupper(Auth::user()->name . ' ' . Auth::user()->last_name),
            'estado'        => 1,
            'unidad'        => $request['item']['unidad']
        ]);

        return response()->json([
            "asignacion"  => $asignacion,
            "seleccion"     => $select,
            "consultorios"  => $consultorios,
            "consultoriosOcupados"  => $consultoriosOcupados,
        ], 200);

    }


    public function saveConsultorio(Request $request)
    {

        Consultorios::create([
            'nombre' => strtoupper($request['nombreConsultorio']),
            'id_unidad' => $request['unidad']
        ]);

        $unidades = Unidad::all();

        $unidades->map(function($item){
            $item->cantConsultorios = Consultorios::where('id_unidad', $item->ID_UNIDAD)->count();
            $item->consultorios = Consultorios::where('id_unidad', $item->ID_UNIDAD)->get();
        });

        $conteoUnidad = [];

        foreach ($unidades as $unidad) {
            if ($unidad['cantConsultorios'] > 0) {
                array_push($conteoUnidad, $unidad);
            }
        }

        $consultorios = Consultorios::all();

        return response()->json([
            "conteoUnidad"  => $conteoUnidad,
            "consultorios"  => $consultorios
        ], 200);

    }

    public function getConteoConsultorios(Request $request)
    {
        $unidades = Unidad::all();

        $unidades->map(function($item){
            $item->cantConsultorios = Consultorios::where('id_unidad', $item->ID_UNIDAD)->count();
            $item->consultorios = Consultorios::where('id_unidad', $item->ID_UNIDAD)->get();
        });

        $conteoUnidad = [];

        foreach ($unidades as $unidad) {
            if ($unidad['cantConsultorios'] > 0) {
                array_push($conteoUnidad, $unidad);
            }
        }

        $consultorios = Consultorios::all();

        return response()->json([
            "conteoUnidad"  => $conteoUnidad,
            "consultorios"  => $consultorios,
        ], 200);

    }

    public function liberarConsultorio(Request $request)
    {

        $user = User::where('nro_doc', $request['medico']['docMedico'])->pluck('id')->first();

        DB::table('oauth_access_tokens')->where('user_id', intval($user))->delete();
        Medicos::where('docMedico', $request['medico']['docMedico'])->delete();

        $medicos = Medicos::with('consultorio')->where('estado', 1)->where('unidad', $request['unidadActiva'])->orderBy('cupo', 'ASC')->get();

        return response()->json([
            'user'    => $user,
            'medicos' => $medicos,
        ], 200);

    }

}
