<?php

namespace App\Imports;

use App\Models\Hvsedes\TalentoHumano\Colaboradores;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PlantaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Colaboradores([
            'DOC_COLABORADOR'       => $row["documento"],
            'NOMB_COLABORADOR'      => $row["apellidos_y_nombres"],
            'GENERO_COLABORADOR'    => $row["genero"],
            'COD_EPS'               => $row["codigo_eps"],
            'ID_UNIDAD'             => $row["unidad"],
            'ID_HAB_SEDE'           => $row["sede"],
            'COD_CARGO'             => $row["codigo_cargo"],
            'HORAS_CONT'            => $row["horas_contratadas"],
            'HORAS_LAB'             => $row["horas_laboradas"],
            'HORAS_SEMANA'          => $row["horas_semana"],
            'CORREO'                => $row["correo"]
        ]);

    }
}
