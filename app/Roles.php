<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\User;

class Roles extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomb_rol', 
        'estado'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
