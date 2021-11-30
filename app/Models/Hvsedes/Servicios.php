<?php

namespace App\Models\hvsedes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicios extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.SER_SERVICIOS";
    
    const CREATED_AT = null;
    const UPDATED_AT = 'SER_FECHA_MODIFICACION';

    protected $fillable = [
        'SER_CODIGO_SERVICIO', 
        'SER_NOMBRE_SERVICIO', 
    ];
}
