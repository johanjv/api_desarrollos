<?php

namespace App\Models\GestionPaciente;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultorios extends Model
{
    use HasFactory;

    protected  $table = "GESTIONPACIENTES.consultorios";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'nombre',
        'id_unidad',
        'doc_prof'
    ];

    public function profesional()
    {
        return $this->hasOne(User::class, 'nro_doc', 'doc_prof');
    }

}
