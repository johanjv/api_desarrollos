<?php

namespace App\Models\Citologias;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citologia extends Model
{
    use HasFactory;

    protected  $table = "CITOLOGIAS.REGISTRO_CITOLOGIAS";

    const CREATED_AT = null;
    const UPDATED_AT = 'FECHA_MODIFICACION';

    protected $fillable = [
        'ID',
        'NRO_DOC',
        'NAP',
        'PRIMER_NOMBRE',
        'SEGUNDO_NOMBRE',
        'PRIMER_APELLIDO',
        'SEGUNDO_APELLIDO',
        'EDAD',
        'ESQUEMA',
        'NRO_DOC_PROF',
        'SEDE',
        'FECHA_ATENCION',
    ];
}
