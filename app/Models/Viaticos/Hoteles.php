<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hoteles extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.hoteles";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idHoteles',
        'id_dep_ciudad',
        'nomHotel',
        'estado',
        'desCena',
    ];
}
