<?php

namespace App\Models\Factucontrol\Proveedores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    use HasFactory;

    protected  $table = "FACTUCONTROL.proveedor";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id_proveedor',
        'nit',
        'razon_social',
        'descripcion',
        'dias_pago',
        'active',
        'descuento',
        'pronto_pago',
    ];

    /* public function sedes()
    {
        return $this->hasMany(SedSede::class, 'SED_CODIGO_DEPARTAMENTO', 'SUC_CODIGO_DEPARTAMENTO');
    } */
}
