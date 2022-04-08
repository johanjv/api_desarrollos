<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colaboradores extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.COLABORADORES";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'DOC_COLABORADOR',
        'NOMB_COLABORADOR',
        'GENERO_COLABORADOR',
        'COD_EPS',
        'ID_UNIDAD',
        'ID_HAB_SEDE',
        'ESTADO',
        'FOTO',
    ];
}
