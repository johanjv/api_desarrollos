<?php

namespace App\Models\Api_Afiliados_Interna;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afiliados extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_2';
    protected  $table = "Asistencial.TM_Afiliado";

    const CREATED_AT = null;
    //const UPDATED_AT = 'FECHA_MODIFICACION';
    const UPDATED_AT = null;

    protected $fillable = [
        'ID',
        'Documento',
        'ID_TP_TipoDocumento',
        'Nombre',
        'Apellido1',
        'Apellido2',
        'ID_TP_TipoAfiliado',
        'FechaAfiliacion',
        'ID_TP_TipoDocumentoCot',
        'Documento_Cot',
        'Sexo',
        'Fecha_Nacimiento',
        'ID_TP_SedePrimaria',
        'RangoSalarial',
        'AntiguedadSemanas',
        'ExentoCp',
        'ExentoCm',
        'ID_TP_EstadoAfiliado',
        'Telefono',
        'TelefonoMovil',
        'Regimen',
        'ID_TP_Empresa',
        'UltimoPerCap',
        'PeriodoIngreso'

    ];
}
