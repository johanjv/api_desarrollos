<?php

namespace App\Models\AdminGlobal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submodulos extends Model
{
    use HasFactory;

    protected  $table = "MASTER.submodulos";
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nomb_sub_modulo',
        'modulo_id',
        'slug'
    ];

/*     public function desarrollo()
    {
        return $this->hasOne('App\Desarrollos', 'id', 'desarrollo_id');
    } */
}
