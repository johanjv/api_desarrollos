<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opciones extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.opciones";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idOpcion',
        'nomOpcion',
        'estado',
    ];
}
