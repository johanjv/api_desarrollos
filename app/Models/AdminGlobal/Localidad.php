<?php

namespace App\Models\AdminGlobal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    use HasFactory;

    protected  $table = "LOCALIDAD";
    public $timestamps = false;

    protected $fillable = [
      'ID',
      'NOMBRE',
      'MUNICIPIO_ID',
    ];

}
