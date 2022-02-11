<?php

namespace App\Models\Factucontrol\Casos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialCasos extends Model
{
    use HasFactory;

    protected  $table = "FACTUCONTROL.historial_caso";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
       'id_hcaso'
      ,'id_caso'
      ,'fecha_movimiento'
      ,'observaciones'
      ,'id_user'
      ,'fecha_asignacion'
      ,'fecha_pasa_caso'
      ,'primerMovimiento'
      ,'devolucion'
      ,'docDevo'
      ,'nomDevo'
      ,'idConcepDevo'
      ,'nomConceDevo'
      ,'idPago'
      ,'nomConcePago'
      ,'idAnulado'
      ,'nomAnulado'
      ,'seguimiento'
      ,'fechaCierre'
      /* 'id_hcaso',
      'id_caso',
      'fecha_movimiento',
      'observaciones',
      'id_user',
      'fecha_asignacion',
      'fecha_pasa_caso',
      'primerMovimiento',
      'seguimiento' */
    ];

}
