<?php

namespace App\Models\Vacunacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Esquema extends Model
{
    use HasFactory;

    protected  $table = "VACUNACION.esquema";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'nomb_esquema',
        'nro_dosis'
    ];

}
