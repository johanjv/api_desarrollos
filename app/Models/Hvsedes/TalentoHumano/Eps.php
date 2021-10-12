<?php

namespace App\Models\Hvsedes\TalentoHumano;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eps extends Model
{
    use HasFactory;

    protected  $table = "EPS";

    protected $fillable = [
        'COD_EPS',
        'NOMBRE_EPS',
    ];

}
