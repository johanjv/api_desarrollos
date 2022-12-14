<?php

namespace App\Models\Hvsedes\Sucursal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.SED_SEDE";

    protected $fillable = [
      'SED_CODIGO_HABILITACION_SEDE',
      'SED_CODIGO_HABILITACION',
      'SED_NOMBRE_SEDE',
      'SED_CODIGO_SEDE',
      'EST_CODIGO_ESTADO',
      'SUC_CODIGO_DANE',
      'SED_FECHA_MODIFICACION',
    ];


}
