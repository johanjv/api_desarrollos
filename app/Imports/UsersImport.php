<?php

namespace App\Imports;

use App\Models\Factucontrol\Proveedores\Proveedores;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Proveedores([
            'nit'           => $row["nit"],
            'razon_social'  => $row["razon_social"],
            'descripcion'   => $row["descripcion"],
            'dias_pago'     => $row["dias_pago"],
            'descuento'     => $row["descuento"],
            'active'        => $row["active"],
            'pronto_pago'   => $row["pronto_pago"],
        ]);
    }
}
