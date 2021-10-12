<?php

namespace App\Models\Hvsedes\Sucursal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.EST_ESTADO";

    protected $fillable = [
        'EST_ID',
        'EST_CODIGO_ESTADO',
        'EST_DESCRIPCION',
    ];

}
