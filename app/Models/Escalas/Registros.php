<?php

namespace App\Models\Escalas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registros extends Model
{
    use HasFactory;

    protected  $table = "ESCALAS.registro";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'afiliado_id',
        'fecha_inicio',
        'fecha_fin',
        'tiempo_atencion',
        'sesiones',
        'programa_id',
        'abandono',
        'abandono_id',
        'diagnostico',
        'diagnostico_secundario',
        'IT',
    ];

    public function programas()
    {
        return $this->hasOne('App\Models\Escalas\Programa', 'idPrograma', 'programa_id');
    }

    public function afiliado()
    {
        return $this->hasOne('App\Models\Api_Afiliados_Interna\Afiliados', 'Documento', 'afiliado_id');
    }

}
