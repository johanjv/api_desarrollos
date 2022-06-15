<?php

namespace App\Models\Escalas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abandonos extends Model
{
    use HasFactory;

    protected  $table = "ESCALAS.abandono";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idAbandono',
        'descripcion',
        'activo',
    ];

    public function escalas()
    {
        return $this->hasOne('App\Models\Escalas\Programa', 'idPrograma', 'programa_id');
    }

}
