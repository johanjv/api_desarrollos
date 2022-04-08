<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.Solicitud";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

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
