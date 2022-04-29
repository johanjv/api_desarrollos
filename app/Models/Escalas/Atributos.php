<?php

namespace App\Models\Escalas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributos extends Model
{
    use HasFactory;

    protected  $table = "ESCALAS.atributo";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
         'idAtributo'
        ,'descripcion'
        ,'valor_maximo'
        ,'valor_minimo'
        ,'escala_idescala'
        ,'orden'
        ,'estado_idestado'
    ];
}
