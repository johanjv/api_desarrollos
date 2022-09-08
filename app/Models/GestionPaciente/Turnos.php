<?php

namespace App\Models\GestionPaciente;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turnos extends Model
{
    use HasFactory;

    protected  $table = "GESTIONPACIENTES.turnos";

    const CREATED_AT = 'fecha_asignacion';
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'docMedico',
        'docPaciente',
        'estadoAtencion',
        'fecha_ini_atencion',
        'fecha_fin_atencion',
    ];

    /* public function profesional()
    {
        return $this->hasOne(User::class, 'nro_doc', 'doc_prof');
    } */

}
