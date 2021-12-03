<?php

namespace App\Imports;

use App\Models\Hvsedes\TalentoHumano\Colaboradores;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DotacionImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Colaboradores([
            'NOMBRE_EQUIPO'     => $row["nombre_del_equipo"],
            'CANTIDAD_EQUIPOS'  => $row["cantidad"],
            'ID_HAB_SEDE'       => $row["codigo_habilitacion_sede"],
            'UNIDAD'            => $row["id_unidad"],
            'NIVEL_DE_RIESGO'   => $row["nivel_de_riesgo"]
        ]);
    }
}

