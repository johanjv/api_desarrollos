<?php

namespace App\Models\GestionPaciente;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicos extends Model
{
    use HasFactory;

    protected  $table = "GESTIONPACIENTES.medicosDisponibles";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'docMedico',
        'nombMedico',
        'estado',
        'cupo',
        'unidad'
    ];

    public function consultorio()
    {
        return $this->hasOne(Consultorios::class, 'doc_prof', 'docMedico');
    }

}
