<?php

namespace App\Http\Controllers\Vacunacion;

use App\Http\Controllers\Controller;
use App\Models\Vacunacion\Esquema;
use App\Models\Vacunacion\Registro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParametricosController extends Controller
{
    public function getEsquemas(Request $request)
    {
        $esquemas = Esquema::all();

        return response()->json([
            "esquemas" => $esquemas
        ], 200);
    }

    public function saveEsquema(Request $request)
    {

        $esquema = Esquema::create([
            'nomb_esquema'  => strtoupper($request['params']['nomb_esquema']),
            'nro_dosis'     => $request['params']['nro_dosis']
        ]);

        $esquemas = Esquema::all();

        return response()->json([
            "esquemas" => $esquemas
        ], 200);
    }

    public function saveUpdEsquema(Request $request)
    {
        $esquema = Esquema::where('id', $request['params']['id'])->update([
            'nomb_esquema'  => strtoupper($request['params']['nomb_esquema']),
            'nro_dosis'     => $request['params']['nro_dosis'],
            'estado'     => $request['params']['estado']
        ]);

        $esquemas = Esquema::all();

        return response()->json([
            "esquemas" => $esquemas
        ], 200);
    }

    public function getSexo(Request $request)
    {
        $sexos          = DB::table('sexo')->get();
        $generos        = DB::table('genero')->get();
        $orientacion    = DB::table('OrientacionSexual')->get();

        return response()->json([
            "sexos"         => $sexos,
            "generos"       => $generos,
            "orientacion"   => $orientacion,
        ], 200);
    }

    public function getRegimen(Request $request)
    {
        $regimenes = DB::table('regimen')->get();

        return response()->json([
            "regimenes" => $regimenes
        ], 200);
    }

    public function getAseguradora(Request $request)
    {
        $aseguradoras = DB::table('EPS')->get();

        return response()->json([
            "aseguradoras" => $aseguradoras
        ], 200);
    }

    public function getPoblacional(Request $request)
    {
        $grupospoblacionales = DB::table('grupo_poblacional')->get();

        return response()->json([
            "grupospoblacionales" => $grupospoblacionales
        ], 200);
    }

    public function getPaises(Request $request)
    {
        $paises = DB::table('pais')->get();

        return response()->json([
            "paises" => $paises
        ], 200);
    }

    public function getDepartamentos(Request $request)
    {
        $departamentos = DB::table('Departamento')->where('pais_id', $request['pais'])->get();

        return response()->json([
            "departamentos" => $departamentos
        ], 200);
    }

    public function getMunicipios(Request $request)
    {
        $municipios = DB::table('VACUNACION.MUNICIPIO')->where('departamento_id', $request['departamento'])->get();

        return response()->json([
            "municipios" => $municipios
        ], 200);
    }

    public function getPertenencias(Request $request)
    {
        $pertenencias = DB::table('pertenencia_etnica')->get();

        return response()->json([
            "pertenencias" => $pertenencias
        ], 200);
    }

    public function getCondicionesSalud(Request $request)
    {
        $condiciones = DB::table('VACUNACION.condicionSalud')->get();

        return response()->json([
            "condiciones" => $condiciones
        ], 200);
    }


}
