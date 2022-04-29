<?php

namespace App\Models\Escalas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escalas extends Model
{
    use HasFactory;

    protected  $table = "ESCALAS.escala";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idEscala',
        'nombre'
    ];

    public function atributos()
    {
        return $this->hasMany(Atributos::class, 'escala_idescala', 'idAtributo' );
    }


}
