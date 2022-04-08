<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroSolicitud extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.Solicitud";

    const CREATED_AT = 'fechaSolicitud';
    const UPDATED_AT = null;

    protected $fillable = [
        'idSolicitud',
        'idCiudadOrigen',
        'idCiudadDestino',
        'idMotivoViaje',
        'idColaborador',
        'fechaSalida',
        'fechaRetorno',
        'horarioEstimado',
        'hospedaje',
        'obsMotivos',
        'observaciones',
        'estadoSolicitud',
        'docCreador',
        'aprobado',
        'docPerAprobacion',
        'fechaSolicitud',
    ];
}
