<?php

namespace App\Models\Api_Afiliados_Interna;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposDoc extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_2';
    protected  $table = "Parametrico.TP_TipoDocumento";

    const CREATED_AT = null;
    //const UPDATED_AT = 'FECHA_MODIFICACION';
    const UPDATED_AT = null;

    protected $fillable = [
    'ID',
    'TipoDocId',
    'Descripcion',
    'Codigo',
    'ID_TP_TipoIdentificacion',

    ];
}
