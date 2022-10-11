<?php

namespace App\Models\Consentimientos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicios extends Model
{
    use HasFactory;

    protected  $table = "CONSENTIMIENTO.servicios";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'nombServicio',
        'estado',
        'tipo'
    ];

}
