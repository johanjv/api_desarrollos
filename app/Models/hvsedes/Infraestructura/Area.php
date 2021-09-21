<?php

namespace App\Models\Hvsedes\Infraestructura;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.AXU_AREA_X_UNIDAD";

    const CREATED_AT = null;
    const UPDATED_AT = 'AXU_FECHA_MODIFICACION';

    protected $fillable = [
        'AXU_CODIGO_AREA',
        'AXU_NOMBRE_AREA',
    ];

}
