<?php

namespace App\Models\Escalas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaEscala extends Model
{
    use HasFactory;

    protected  $table = "ESCALAS.programa_escala";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'programa_idprograma',
        'escala_idescala',
        'requerido',
        'orden'
    ];

    public function detalleEscalas()
    {
        return $this->hasOne(Escalas::class, 'idEscala', 'escala_idescala');
    }

    public function atributos()
    {
        return $this->hasMany(Atributos::class, 'escala_idescala', 'escala_idescala' );
    }

}
