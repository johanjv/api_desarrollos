<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    protected  $table = "UNIDADES_ESTANDAR";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'ID_UNIDAD',
        'NOMBRE_UNIDAD',
        'SED_COD_DEP',
    ];
}
