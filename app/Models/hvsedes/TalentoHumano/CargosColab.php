<?php

namespace App\Models\Hvsedes\TalentoHumano;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargosColab extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.CARGOS_COLABORADOR";

    protected $fillable = [
        'ID',
        'DOC_COLABORADOR',
        'COD_CARGO',
        'HORAS_CONT',
        'HORAS_LAB',
        'HORAS_SEMANA',
    ];

    public function cargoDetalle() {
        return $this->hasOne('App\Models\Hvsedes\TalentoHumano\Cargo', 'COD_CARGO', 'COD_CARGO');
    }


}
