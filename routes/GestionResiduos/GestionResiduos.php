<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API HVSEDES Routes
|--------------------------------------------------------------------------
| Estas son las rutas para acceder al aplicativo HVSEDES
|
*/

Route::middleware(['auth:api'])->group(function () {
    Route::prefix("gestion-de-residuos-hospitalarios")->group(function(){
        Route::post('getDataCalendar',        'GestionResiduos\GestionResiduosController@getDataCalendar');
        Route::get('getClasif',               'GestionResiduos\GestionResiduosController@getClasif');
    });
});
