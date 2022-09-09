<?php

namespace App\Models\GestionPaciente;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaPura extends Model
{
    use HasFactory;

    protected  $table = "AgendaPAD.Tb_Agenda_ContingenciaVS";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'Id',
        'ID_TP_Sede',
        'MedicoId',
        'EspecialidadId',
        'Fecha',
        'Estado',
        'ID_TM_Afiliado',
        'NombrePaciente',
        'ID_TP_Producto',
        'Nap',
        'PagoFinal',
        'ID_TP_EmpresaUsuario',
        'ID_TM_AutorizacionServicio',
    ];

    public function profesional()
    {
        return $this->hasOne(User::class, 'nro_doc', 'MedicoId');
    }

}
