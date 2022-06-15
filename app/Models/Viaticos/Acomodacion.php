<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acomodacion extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.acomodacion";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idAcomodacion',
        'nomAcomodacion',
        'estado',
    ];
}
