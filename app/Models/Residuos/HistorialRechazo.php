<?php

namespace App\Models\Residuos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialRechazo extends Model
{
    use HasFactory;

    protected  $table = "RESIDUOS.hist_rechazos";

    const CREATED_AT = 'fecha_rechazo';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_hist',
        'id_aprobacion_mes',
        'observacion_rechazo',
        'fecha_rechazo',
        'nro_doc_user'
    ];

    public function user_h()
    {
        return $this->hasOne(User::class, 'nro_doc', 'nro_doc_user');

    }

}
