<?php

namespace App\Models\LineaEtica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registros extends Model
{
    use HasFactory;

    protected $connection = 'mysql_1';
    protected  $table = "linea_etica_registros";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [

        'Id',
        'Fecha_radicacion',
        'Hora_radicacion',
        'Tipo_de_denunciante',
        'Nombres',
        'Apellidos',
        'Numero_documento',
        'Telefono_contacto',
        'Correo',
        'Correo2',
        'Grupo_de_interes',
        'Descripcion_hechos',
        'Dano_perjuicio',
        'Fecha_de_los_hechos',
        'Conocimiento_situacion_continua',
        'Numero_veces_reportado',
        'Medios_reportados',
        'Tratamiento_datos',
        'Documentos_adjuntos',
        'Documentos_adjuntos_2',
        'Documentos_adjuntos_3'
    ];

}
