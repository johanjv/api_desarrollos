<?php

namespace App\Models\Api_Afiliados_Interna;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afiliados extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_2';
    protected  $table = "AutoVS.TM_Afiliado";

    const CREATED_AT = null;
    //const UPDATED_AT = 'FECHA_MODIFICACION';
    const UPDATED_AT = null;

    protected $fillable = [
        'ID',
        'ID_TP_TipoIdentificacion',
        'Documento',
        'Nombre_Completo',
        'ID_TP_Genero',
        'FechaNacimiento',
        'Contrato',
        'FechaActualizacion',
        'PrimerNombre',
        'SegundoNombre',
        'PrimerApellido',
        'SegundoApellido',
        'Direccion',
        'TelefonoFijo',
        'Celular',
        'ID_TP_SedePrimaria',
        'ID_TP_SedeOdontologica',
        'Email',

    ];
}
