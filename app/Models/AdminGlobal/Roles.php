<?php

namespace App\Models\AdminGlobal;

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

    public function users()
    {
        return $this->belongsToMany('App\User', 'MASTER.rol_user', 'rol_id', 'user_id');
    }
}
