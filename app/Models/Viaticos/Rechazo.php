<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rechazo extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.historialRechazo";

    const CREATED_AT = 'fechaRechazo';
    const UPDATED_AT = null;

    protected $fillable = [
        'idHistorialRechazo',
        'solicitud_id',
        'observaciones',
    ];
}
