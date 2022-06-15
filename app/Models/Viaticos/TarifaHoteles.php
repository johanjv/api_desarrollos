<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaHoteles extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.hotelTarifas";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idHotelTarifa',
        'hotel_id',
        'acomodacion_id',
        'tarifaSinImpuesto',
        'seguro',
        'habitacion',
    ];
}
