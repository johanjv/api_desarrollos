<?php

namespace App\Http\Controllers\Vacunacion;

use App\Http\Controllers\Controller;
use App\Models\Vacunacion\Esquema;
use App\Models\Vacunacion\Paciente;
use App\Models\Vacunacion\Registro;
use App\Models\Vacunacion\Residencia;
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

    public function getSaveRegistrationVaccine(Request $request)
    {
        $registrationPatient = array(
            'nro_doc_pac'     => $request['item']['Documento'],
            'dosis'           => $request['vaccinationScheme']['nro_dosis']['id'],
            'lote'            => $request['vaccinationScheme']['lote'],
            'lote_diluyente'  => $request['vaccinationScheme']['loteDiluyente'],
            'lote_jeringa'    => $request['vaccinationScheme']['loteJeringa'],
            'unidad'          => $request['item']['unidad'],
        );
        $registros = Registro::create($registrationPatient);

        $homePatient = array(
            'nro_doc_pac'   => $request['item']['Documento'],
            'departamento'  => $request['item']['departamentoResidencia']['idDepartamento'],
            'municipio'     => $request['item']['municipioResidencia']['idMunicipio'],
            'area'          => $request['item']['area']['id'],
            /* 'detalle_area'  => $request['item']['loteJeringa'], */
            /* 'nomenclatura'  => $request['item']['loteJeringa'], */
            'direccion'     => $request['item']['direccion'],
            /* 'indicativo'    => $request['item']['loteJeringa'], */
            'telefono_fijo' => $request['item']['fijo'],
            'celular'       => $request['item']['telefono'],
            'pais_red'      => $request['item']['departamentoResidencia']['pais_id'],
            'localidad'     => $request['item']['localidad'],
            'barrio'        => $request['item']['barrio'],
            /* 'registro_id'   => $request['item']['loteJeringa'], */
            /* 'cargue'        => $request['item']['loteJeringa'], */
        );
        $registros = Residencia::create($homePatient);

        return response()->json([
            "registros" => $registros
        ], 200);
    }
}
