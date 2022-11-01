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
    Route::prefix("video-consulta")->group(function(){
        Route::get('getAgenda', 'VideoConsulta\AgendaVideoConsultaController@getAgenda');
    });
});
