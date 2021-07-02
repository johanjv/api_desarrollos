<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulos extends Model
{
    use HasFactory;

    protected  $table = "MASTER.modulos";
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nomb_modulo',
        'desarrollo_id', 
        'slug'
    ];

    public function desarrollo()
    {
        return $this->hasOne('App\Desarrollos', 'id', 'desarrollo_id');
    }
}