<?php

namespace App\Models\Hvsedes\Sucursal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SedSede extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.SED_SEDE";

    const CREATED_AT = null;
    const UPDATED_AT = 'SED_FECHA_MODIFICACION';

    protected $fillable = [
        'SED_CODIGO_HABILITACION_SEDE',
        'SED_CODIGO_HABILITACION',
        'SED_NOMBRE_SEDE',
        'SED_CODIGO_SEDE',
        'EST_CODIGO_ESTADO',
        'SUC_CODIGO_DANE',
        'SED_FECHA_MODIFICACION',
        'SED_CODIGO_DEPARTAMENTO',
        'SED_DIRECCION_SEDE',
        'SED_HORARIO_SEDE',
        'SED_POBLACION_SEDE',
        'SED_MTS2_SEDE'
    ];

    public function sucursal()
    {
        return $this->hasOne('App\Models\Hvsedes\Sucursal\Sucursal', 'SUC_CODIGO_DEPARTAMENTO', 'SED_CODIGO_DEPARTAMENTO');
    }

}
