<?php

namespace App\Models\Escalas;

use App\Models\Unidad;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    protected  $table = "ESCALAS.historial";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'idhistorial',
        'registro_id',
        'fecha',
        'unidad_id',
        'estado_id',
        'usuario_idusuario',
    ];

    public function profesional()
    {
        return $this->hasOne(User::class, 'nro_doc', 'usuario_idusuario');
    }

    public function unidad()
    {
        return $this->hasOne(Unidad::class, 'ID_UNIDAD', 'unidad_id');
    }



}
