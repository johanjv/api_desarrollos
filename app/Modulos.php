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
        'nomb_modulo',
        'desarrollo_id', 
    ];

    public function desarrollo()
    {
        return $this->belongsTo(Desarrollos::class);
    }
}