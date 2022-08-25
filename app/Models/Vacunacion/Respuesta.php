<?php

namespace App\Models\Vacunacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    use HasFactory;

    protected  $table = "VACUNACION.respuesta";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idRespuesta',
        'nombre_respuesta',
        'pregunta_id',
        'estado',
    ];

}
