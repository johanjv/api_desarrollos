<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API LINEAETICA Routes
|--------------------------------------------------------------------------
| Estas son las rutas para acceder al aplicativo LINEAETICA
|
*/

Route::middleware(['auth:api'])->group(function () {
    Route::prefix("linea-etica")->group(function(){
        Route::get('getRegistros',    'LineaEtica\LineaEticaController@getRegistros');
    });
});
