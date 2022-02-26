<?php

namespace App\Models\Residuos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidarMes extends Model
{
    use HasFactory;

    protected  $table = "RESIDUOS.aprobacion_mes";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'aprobado',
        'fecha_revision',
        'nro_doc_user',
        'id_mes_ano',
        'unidad'
    ];



    public function registros()
    {
        return $this->hasMany('App\Models\Residuos\TiempoResiduos', 'id_mes_ano', 'id_mes_ano')->where('id_residuo', 1);
    }

}
