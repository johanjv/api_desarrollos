<?php

namespace App\Models\hvsedes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupos extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.GRU_GRUPOS";
    
    const CREATED_AT = null;
    const UPDATED_AT = 'GRU_FECHA_MODIFICACION';

    protected $fillable = [
        'GRU_NOMBRE_GRUPO_SERVICIO', 
    ];
}
