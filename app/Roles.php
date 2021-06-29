<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\User;

class Roles extends Model
{
    use HasFactory;

    protected  $table = "MASTER.Roles";

    protected $fillable = [
        'nomb_rol', 
        'estado'
    ];

    /* public function rol()
    {
        return $this->belongsToMany('App\Roles', 'MASTER.rol_user' , 'rol_id')->withPivot('rol_id');
    } */
}
