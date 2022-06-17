<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaViaticos extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.tarifaViaticos";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idTarifas',
        'alimentos',
        'valor',
        'estado',
    ];
}
