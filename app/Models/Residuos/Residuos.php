<?php

namespace App\Models\Residuos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Residuos extends Model
{
    use HasFactory;

    protected  $table = "RESIDUOS.residuos";

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_residuos',
        'nomb_residuos',
        'id_categoria',
        'fecha_creacion',
    ];

    public function categoria()
    {
        return $this->hasOne('App\Models\Residuos\Categoria', 'id_categoria', 'id_categoria');
    }

}
