<?php

namespace App\Models\Vacunacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    use HasFactory;

    protected  $table = "VACUNACION.registro";

    const CREATED_AT = "fecha_registro";
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'nro_doc_pac',
        'dosis',
        'lote',
        'lote_diluyente',
        'lote_jeringa',
        'fecha_vencimiento',
        'vacuna_id',
        'vacunador',
        'unidad',
        'observacion',
        'responsable',
        'fecha_prox_cita',
        'fecha_registro',
        'id_regimen',
        'pertenencia_id',
        'aseguradora',
        'grupo_pob_id',
        'municipio_id',
        'reg_gest_id',
        'area_id',
        'acudiente_id',
        'estado_id'
    ];

    public function paciente()
    {
        return $this->hasOne('App\Models\Vacunacion\Paciente', 'numero_documento', 'nro_doc_pac');
    }

}
