<?php

namespace App\Models\Bitacora;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected  $table = "BITACORA.REGISTRO_BITACORA";

    const CREATED_AT = null;
    const UPDATED_AT = 'FECHA';

    protected $fillable = [
        'ID_APP',
        'USER_ACT',
        'ACCION',
        'FECHA',
        'USER_EMPRESA',
    ];

}
