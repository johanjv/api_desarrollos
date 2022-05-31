<?php

namespace App\Models\Escalas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resultados extends Model
{
    use HasFactory;

    protected  $table = "ESCALAS.resultado";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idResultado',
        'nombre_resultado',
        'escala_id',
        'estado_idestado',
        'funcion',
        'orden',
        'totaliza',
    ];
}
