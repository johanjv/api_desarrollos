<?php

namespace App\Models\Escalas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    protected  $table = "ESCALAS.historial";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idhistorial',
        'registro_id',
        'fecha',
        'unidad_id',
        'estado_id',
        'usuario_idusuario',
    ];

}
