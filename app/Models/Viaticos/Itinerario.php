<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerario extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.itinerario";

    const CREATED_AT = 'fechaRegistro';
    const UPDATED_AT = null;

    protected $fillable = [
        'idItinerario',
        'solicitud_id',
        'aerolinea_id',
        'hotel_id',
        'viaticosSuc_id',
        'opcion_id',
        'grupo_id',
        'acomodacion_id',
        'seguro_id',
        'horaSalida',
        'horaRetorno',
        'tarifaAdminTrans',
        'tarifaAdminHosp',
        'valorTiquete',
        'otroValor',
        'valorHotelNoche',
        'docPerRegistra',
        'docAignacionValorViaticos',
        'valorViaticosAsignados',
    ];
}
