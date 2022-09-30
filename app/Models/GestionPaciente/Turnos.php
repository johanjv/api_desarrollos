<?php

namespace App\Models\GestionPaciente;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turnos extends Model
{
    use HasFactory;

    protected  $table = "GESTIONPACIENTES.turnos";

    const CREATED_AT = 'fecha_turno';
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'docMedico',
        'ini_turno',
        'fin_turno',
        'meta',
        'cupo',
        'unidad',
        'horas_turno'
    ];


}
