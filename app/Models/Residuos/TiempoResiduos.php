<?php

namespace App\Models\Residuos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiempoResiduos extends Model
{
    use HasFactory;

    protected  $table = "RESIDUOS.tiempos_residuos";

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_tiempo',
        'id_residuo',
        'cantidad',
        'dia',
        'mes',
        'ano',
        'nro_doc_user',
        'unidad',
        'id_mes_ano',
    ];

}
