<?php

namespace App\Models\Residuos;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidarMes extends Model
{
    use HasFactory;

    protected  $table = "RESIDUOS.aprobacion_mes";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'aprobado',
        'fecha_revision',
        'nro_doc_user',
        'id_mes_ano',
        'unidad',
        'observacion'
    ];



    public function registros()
    {
        return $this->hasMany('App\Models\Residuos\TiempoResiduos', 'id_mes_ano', 'id_mes_ano')->where('id_residuo', 1);
    }

    public function userR()
    {
        return $this->hasOne(User::class, 'nro_doc', 'nro_doc_user');

    }

    public function histR()
    {
        return $this->hasMany(HistorialRechazo::class, 'id_aprobacion_mes', 'id')->orderBy('fecha_rechazo', 'asc');

    }
}
