<?php

namespace App\Models\AdminGlobal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AdminGlobal\Modulos;

class Desarrollos extends Model
{
    use HasFactory;

    protected  $table = "MASTER.desarrollos";

    protected $fillable = [
        'nomb_desarrollo',
    ];
}
