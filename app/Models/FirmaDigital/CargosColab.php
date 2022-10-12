<?php

namespace App\Models\FirmaDigital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargosColab extends Model
{
    use HasFactory;

    protected  $table = "FIRMA.cargosColab";
    public $timestamps = false;

    protected $fillable = [
        'id',
        'documento',
        'codCargo'
    ];

    public function cargoDetalle() {
        return $this->hasOne('App\Models\Hvsedes\TalentoHumano\Cargo', 'COD_CARGO', 'codCargo');
    }

}
