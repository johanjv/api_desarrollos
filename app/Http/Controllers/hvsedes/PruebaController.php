<?php

namespace App\Http\Controllers\hvsedes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PruebaController extends Controller
{
    public function getPrueba(Request $request)
    {
        return "esta prueba debe funcionar";
    }
}
