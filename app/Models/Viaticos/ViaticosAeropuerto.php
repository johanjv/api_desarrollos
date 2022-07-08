<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViaticosAeropuerto extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.viaticosSucursal";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idViaticosSucursal',
        'recorridoUno',
        'recorridoDos',
        'totalRecorrido',
        'codSuc',
        'estado',
    ];
}
