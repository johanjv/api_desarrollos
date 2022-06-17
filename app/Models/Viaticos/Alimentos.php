<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alimentos extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.tarifaViaticos";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'idTarifas',
        'alimentos',
        'valor',
        'estado',
    ];
}
