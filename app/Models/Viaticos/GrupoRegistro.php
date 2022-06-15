<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoRegistro extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.grupoRegistro";

    const CREATED_AT = 'fechaSolicitud';
    const UPDATED_AT = null;

    protected $fillable = [
        'idGrupoRegistro',
        'solicitud_id',
        'colaborador_id',
    ];
}
