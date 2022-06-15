<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aerolineas extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.aerolineas";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idAreolineas',
        'nomAerolinea',
        'estado',
    ];
}
