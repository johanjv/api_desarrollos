<?php

namespace App\Models\Hvsedes\Infraestructura;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServInfra extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.SXA_SERVICIO_X_AREA";

    const CREATED_AT = null;
    const UPDATED_AT = 'SXA_FECHA_MODIFICACION';

    protected $fillable = [
        'SXA_NOMBRE_SERVICIO',
    ];

}
