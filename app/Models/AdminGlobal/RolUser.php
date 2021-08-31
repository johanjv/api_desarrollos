<?php

namespace App\Models\AdminGlobal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolUser extends Model
{
    use HasFactory;

    protected  $table = "MASTER.rol_user";

    protected $fillable = ['id', 'user_id','rol_id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function rol()
    {
        return $this->belongsTo('App\Models\AdminGlobal\Roles', 'rol_id');
    }
}
