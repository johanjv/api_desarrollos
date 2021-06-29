<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Modulos;

class Desarrollos extends Model
{
    use HasFactory;

    protected  $table = "MASTER.desarrollos";

    protected $fillable = [
        'nomb_desarrollo', 
    ];
}
