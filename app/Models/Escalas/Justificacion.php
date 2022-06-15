<?php

namespace App\Models\Escalas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Justificacion extends Model
{
    use HasFactory;

    protected  $table = "ESCALAS.justificacion";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idJustificacion',
        'idHistoria',
        'descripcion',
    ];

}
