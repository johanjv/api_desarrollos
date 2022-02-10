<?php

namespace App\Models\Residuos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected  $table = "RESIDUOS.categoria_residuos";

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_categoria',
        'nomb_categoria',
        'id_clasif_residuos',
        'fecha_creacion',
    ];

    public function clasificacion()
    {
        return $this->hasOne('App\Models\Residuos\Clasificacion', 'id_clasif_residuos', 'id_clasif_residuos');
    }

    public function residuos()
    {
        return $this->hasMany('App\Models\Residuos\Residuos', 'id_categoria', 'id_categoria');
    }

}
