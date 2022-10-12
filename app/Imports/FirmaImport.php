<?php

namespace App\Imports;

use App\Models\FirmaDigital\Colaborador;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FirmaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Colaborador([
            'documento'       => $row['documento'],
            'nombreCompleto'      => $row['apellidos_y_nombres'],
        ]);
    }
}

