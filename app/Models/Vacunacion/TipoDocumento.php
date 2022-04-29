<?php

namespace App\Models\Vacunacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;

    protected  $table = "tipos_documento";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'id_td',
        'descripcion_td',
        'codigo_td',
        'estado_td',
    ];

}
