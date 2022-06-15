<?php

namespace App\Models\Escalas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialAtributo extends Model
{
    use HasFactory;

    protected  $table = "ESCALAS.historial_atributo";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'historial_idhistorial',
        'atributo_idatributo',
        'valor',
    ];

    public function atributo()
    {
        return $this->hasOne(Atributos::class, 'idAtributo', 'atributo_idatributo');
    }
}
