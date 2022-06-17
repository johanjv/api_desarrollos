<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivosViajes extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.MotivosViajes";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idMotivoViajes',
        'nomMotivo',
        'estado',
    ];
}
