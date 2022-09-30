<?php

namespace App\Models\Consentimientos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroConsentimiento extends Model
{
    use HasFactory;

    protected  $table = "CONSENTIMIENTO.registro_consentimientos";

    const CREATED_AT = 'fecha_envio';
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'doc_pac',
        'fecha_firma',
        'firma',
        'verificado',
        'doc_prof_verifico',
        'estado',
    ];

}
