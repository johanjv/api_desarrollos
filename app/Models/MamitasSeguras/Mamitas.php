<?php

namespace App\Models\MamitasSeguras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mamitas extends Model
{
    use HasFactory;

    protected  $table = "MAMITAS.REGISTRO_MAMITAS";

    const CREATED_AT = 'FECHA_REGISTRO';
    const UPDATED_AT = null;

    protected $fillable = [
       'ID'
      ,'DOC'
      ,'NOMBRES'
      ,'FECHA_NAC'
      ,'CORREO'
      ,'CELULAR'
      ,'LOCALIDAD_ID'
      ,'MUNICIPIO_ID'
      ,'SEDE'
      ,'NRO_DOC_PROF'
      ,'EDAD_GEST'
      ,'FECHA_REGISTRO'
    ];

    public function municipio()
    {
        return $this->hasOne('App\Models\AdminGlobal\Municipio', 'ID', 'MUNICIPIO_ID');
    }

    public function localidad()
    {
        return $this->hasOne('App\Models\AdminGlobal\Municipio', 'ID', 'LOCALIDAD_ID' );
    }

}
