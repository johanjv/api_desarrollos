<?php

namespace App\Models\FirmaDigital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected  $table = "FIRMA.Direcciones";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'Id',
        'Direccion'
    ];

}
