<?php

namespace App\Models\CertificadosEscolares;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfiliadosEscolares extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_3';
    protected  $table = "Certificaciones";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [

        'Id',
        'Documento',
        'Nombre_Completo',
        'Fecha',
        'Nombre_Servicio',
        'Peso',
        'talla',
        'Email'

    ];



}
