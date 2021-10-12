<?php

namespace App\Models\Hvsedes\Sucursal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniUnidad extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.UNI_UNIDAD";

    const CREATED_AT = null;
    const UPDATED_AT = 'UNI_FECHA_MODIFICACION';

    protected $fillable = [
      'SED_CODIGO_HABILITACION_SEDE',
      'TXU_CODIGO_UNIDAD',
      'UNI_NOMBRE_UNIDAD',
      'EST_CODIGO_ESTADO',
      'UNI_FECHA_MODIFICACION',
    ];


}
