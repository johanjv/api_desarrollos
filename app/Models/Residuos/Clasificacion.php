<?php

namespace App\Models\Residuos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clasificacion extends Model
{
    use HasFactory;

    protected  $table = "RESIDUOS.clasif_residuos";

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_clasif_residuos',
        'nomb_clasif',
        'fecha_creacion',
    ];

    public function categoria()
    {
        return $this->hasMany('App\Models\Residuos\Categoria', 'id_clasif_residuos', 'id_clasif_residuos');
    }

}
