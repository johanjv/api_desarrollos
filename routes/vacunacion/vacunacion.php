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
    Route::prefix("vacunacion")->group(function(){
        Route::get('getTiposDoc',       'vacunacion\VacunacionController@getTiposDoc');
        Route::get('getEsquemas',       'vacunacion\VacunacionController@getEsquemas');
        Route::post('saveEsquema',      'vacunacion\VacunacionController@saveEsquema');
        Route::post('saveUpdEsquema',   'vacunacion\VacunacionController@saveUpdEsquema');
    });
});
