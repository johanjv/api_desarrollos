<?php

namespace App\Models\GestionPaciente;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected  $table = "GESTIONPACIENTES.citas";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'CODIGOIPS',
        'ESPECIALIDAD',
        'MedicoId',
        'NombreMedico',
        'identMedico',
        'regProfMedico',
        'FECHA',
        'Estado',
        'DocIden',
        'PACIENTEID',
        'NombreSolicit',
        'ApellidosSolicit',
        'Telefono',
        'NOMBREPLAN',
        'nap',
        'Inasistencia',
        'Multa',
        'PagoFinal',
        'grupoUsu',
        'empresaId',
        'Id_Movimiento',
        'Motivo_NotaCita',
        'desc_Motivo_NotaCita',
        'UsuarioNTID',
        'factura',
        'cajaid',
        'facturado',
        'estadoAtencion',
        'medicoAsignado',
        'observaciones'
    ];

    public function profesional()
    {
        return $this->hasOne(User::class, 'nro_doc', 'medicoAsignado');
    }

    public function consultorio()
    {
        return $this->hasOne(Consultorios::class, 'doc_prof', 'medicoAsignado');
    }

}
