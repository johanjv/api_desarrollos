<?php

namespace App\Models\Sucursal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.SUC_SUCURSAL";

    protected $fillable = [
        'SUC_ID',
        'SUC_DEPARTAMENTO',
        'SUC_CODIGO_DEPARTAMENTO',
        'SUC_MUNICIPIO',
        'SUC_CODIGO_MUNICIPIO',
        'SUC_CODIGO_DANE',
        'SUC_FECHA_MODIFICACION',
    ];

    public function sedes()
    {
        return $this->hasMany(SedSede::class, 'SED_CODIGO_DEPARTAMENTO', 'SUC_CODIGO_DEPARTAMENTO');
    }
}
