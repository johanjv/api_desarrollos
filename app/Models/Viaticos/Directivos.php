<?php

namespace App\Models\Viaticos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directivos extends Model
{
    use HasFactory;

    protected  $table = "VIATICOS.Directivos";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'idDirectivos',
        'nomDirectivo',
        'docDirectivo',
        'cargo',
        'proceso',
        'estado',
    ];
}
