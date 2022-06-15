<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViaticosAeropuerto extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.viaticosSucursal";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'idViaticosSucursal',
        'recorridoUno',
        'recorridoDos',
        'totalRecorrido',
        'codSuc',
        'estado',
    ];
}
