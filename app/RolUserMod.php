<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolUserMod extends Model
{
    use HasFactory;

    protected  $table = "MASTER.rol_user_mod";
    public $timestamps = false;

    protected $fillable = ['id', 'modulo_id','rol_user_id'];

  
}
