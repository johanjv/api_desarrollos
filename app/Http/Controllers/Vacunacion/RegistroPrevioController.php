<?php

namespace App\Http\Controllers\Vacunacion;

use App\Http\Controllers\Controller;
use App\Models\Vacunacion\Esquema;
use App\Models\Vacunacion\Paciente;
use App\Models\Vacunacion\Registro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegistroPrevioController extends Controller
{
    public function getRegistrosPrevios(Request $request)
    {
        $registros = Registro::where('nro_doc_pac', $request['nro_doc'])->where('estado_id', 0)->where('unidad', $request['unidad'])->count();

        return response()->json([
            "cantidad_registros" => $registros
        ], 200);
    }

    public function savePreRegistro(Request $request)
    {
        $validarPaciente = Paciente::where('numero_documento', $request['item']['Documento'])->count();

        $paciente = array(
            'tipo_documento_id' => $request['item']['ID_TP_TipoIdentificacion'],
            'numero_documento'  => $request['item']['Documento'],
            'primer_nombre'     => $request['item']['PrimerNombre'],
            'segundo_nombre'    => $request['item']['SegundoNombre'],
            'primer_apellido'   => $request['item']['PrimerApellido'],
            'segundo_apellido'  => $request['item']['SegundoApellido'],
            'fecha_nacimiento'  => $request['item']['FechaNacimiento'],
            'genero_id'         => $request['item']['genero']['id'],
            'sexo_id'           => $request['item']['sexo']['id'],
            /* 'orientacion_id'    => $request['item'][''], */
            'fecha_registro'    => date('Y-m-d'),
        );

        $resultadoPaciente = $validarPaciente == 0 ? Paciente::create($paciente) : Paciente::where('numero_documento', $request['item']['Documento'])->update($paciente);

        $validarRegistro = Registro::where('nro_doc_pac', $request['item']['Documento'])->where('estado_id', 0)->where('unidad', $request['unidad'])->count();

        $registro = array(
            'nro_doc_pac'       => $request['item']['Documento'],
            'estado_id'         => 0,
            'unidad'            => $request['item']['unidad'],
            'fecha_registro'    => date('Y-m-d h:m:s'),
        );

        $resultadoRegistro = $validarRegistro == 0 ? Registro::create($registro) : Paciente::where('numero_documento', $request['item']['Documento'])->update($paciente);

        return response()->json([
            "resultadoPaciente" => $resultadoPaciente,
            "resultadoRegistro" => $resultadoRegistro
        ], 200);
    }

}
