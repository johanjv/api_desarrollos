<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupos extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.grupos";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idGrupos',
        'nomGrupo',
        'estado',
    ];
}
