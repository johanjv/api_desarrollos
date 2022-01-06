<?php

namespace App\Models\AdminGlobal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected  $table = "MUNICIPIO";
    public $timestamps = false;

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
      ,'FECHA_REGISTRO'
    ];

    public function localidades()
    {
        return $this->hasMany('App\Models\AdminGlobal\Localidad', 'MUNICIPIO_ID' );
    }
}
