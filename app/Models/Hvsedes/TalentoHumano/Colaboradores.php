<?php

namespace App\Models\Hvsedes\TalentoHumano;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colaboradores extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.COLABORADORES";
    public $timestamps = false;

    protected $fillable = [
        'DOC_COLABORADOR',
        'NOMB_COLABORADOR',
        'GENERO_COLABORADOR',
        'COD_EPS',
        'ID_UNIDAD',
        'ID_HAB_SEDE',
        'FOTO',
        'CORREO'
    ];

    public function eps() {
        return $this->hasOne('App\Models\Hvsedes\TalentoHumano\Eps', 'COD_EPS', 'COD_EPS');
    }

    public function cargos() {
        return $this->hasMany('App\Models\Hvsedes\TalentoHumano\CargosColab', 'DOC_COLABORADOR', 'DOC_COLABORADOR')->where("ESTADO", 1);
    }

    public function cargos2() {
        return $this->hasMany('App\Models\Hvsedes\TalentoHumano\CargosColab', 'DOC_COLABORADOR', 'DOC_COLABORADOR')->where("ESTADO", 0);
    }


}
