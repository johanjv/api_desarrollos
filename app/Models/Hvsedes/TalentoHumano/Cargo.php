<?php

namespace App\Models\Hvsedes\TalentoHumano;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected  $table = "CARGOS";
    public $timestamps = false;

    protected $fillable = [
        'COD_CARGO',
        'NOMBRE_CARGO',
    ];

}
