<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivosViajes extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.MotivosViajes";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'idMotivoViajes',
        'nomMotivo',
        'estado',
    ];
}
