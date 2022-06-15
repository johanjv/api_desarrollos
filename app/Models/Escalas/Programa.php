<?php

namespace App\Models\Escalas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    use HasFactory;

    protected  $table = "ESCALAS.programa";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idPrograma',
        'nombre',
        'enlace',
        'icono',
    ];

    public function escalas()
    {
        return $this->hasMany(ProgramaEscala::class, 'programa_idprograma', 'idPrograma' );
    }
}
