<?php

namespace App\Models\Hvsedes\TalentoHumano;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorariosColab extends Model
{
    use HasFactory;

    protected  $table = "HOJADEVIDASEDES.HORARIOS_COLABORADORES";
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'DIA',
        'COD_CARGO',
        'HORA_INI',
        'HORA_FIN',
        'DOC_COLABORADOR',
    ];

}
