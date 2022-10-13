<?php

namespace App\Models\FirmaDigital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colaborador extends Model
{
    use HasFactory;

    protected  $table = "FIRMA.colaboradores";
    public $timestamps = false;

    protected $fillable = [
        'documento',
        'nombreCompleto',
    ];

    public function cargos() {
        return $this->hasMany(CargosColab::class, 'documento', 'documento');
    }

}
