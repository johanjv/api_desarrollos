<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolUser extends Model
{
    use HasFactory;

    protected  $table = "MASTER.rol_user";

    protected $fillable = ['id', 'user_id','rol_id'];

    /* public function rol_user()
    {
        return $this->belongsToMany('App\User', 'MASTER.rol_user', 'user_id')
                        ->withPivot('user_id', 'rol_id');
    } */
}
