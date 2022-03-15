<?php

namespace App\Models\AdminGlobal;

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
        return $this->hasOne('App\Models\AdminGlobal\Desarrollos', 'id', 'desarrollo_id');
    }

    public function submodulos()
    {
        return $this->hasMany('App\Models\AdminGlobal\Submodulos', 'modulo_id' )->orderBy('orden', 'ASC');
    }
}
