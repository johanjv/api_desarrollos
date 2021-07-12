<?php

namespace App\Models\ServHab;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioHabilitado extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.SHA_SERVICIOS_HABILITADOS";

    protected $fillable = [
        'SHA_CODIGO_HABILITACION_SEDE',
        'SHA_CODIGO_HABILITACION',
        'SHA_CODIGO_SEDE',
        'SHA_NOMBRE_GRUPO_SERVICIO',
        'SHA_CODIGO_SERVICIO',
        'SHA_NOMBRE_SERVICIO',
        'EST_CODIGO_ESTADO',
        'SHA_FECHA_MODIFICACION',
    ];


}
