<?php

namespace App\Models\Vacunacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Residencia extends Model
{
    use HasFactory;

    protected  $table = "VACUNACION.residencia";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'nro_doc_pac',
        'departamento',
        'municipio',
        'area',
        'detalle_area',
        'nomenclatura',
        'direccion',
        'indicativo',
        'telefono_fijo',
        'celular',
        'pais_red',
        'localidad',
        'barrio',
        'registro_id',
        'cargue',
    ];
}
