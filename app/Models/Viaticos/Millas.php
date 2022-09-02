<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Millas extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.registroMillas";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idMillas',
        'cantidadMillas',
        'Observaciones',
        'docRegistro',
        'restar',
    ];
}
