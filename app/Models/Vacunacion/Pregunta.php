<?php

namespace App\Models\Vacunacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    use HasFactory;

    protected  $table = "VACUNACION.pregunta";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idPregunta',
        'descripcion',
        'nombre_check',
        'estado_pregunta',
    ];

    public function respuestas()
    {
        return $this->hasMany('App\Models\Vacunacion\Respuesta', 'pregunta_id', 'idPregunta');
    }

}
