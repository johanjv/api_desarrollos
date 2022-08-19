<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViaticosCiudadAeropuerto extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.viaticosCiudadAeropuerto";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'idDepartamento',
        'aeropuerto',
        'valorIda',
        'valorVuelta',
        'total',
        'estado',
    ];
}
