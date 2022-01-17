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
    Route::post('getRegistrosdelDiaMamitas',    'MamitasSeguras\MamitasController@getRegistrosdelDiaMamitas');
    Route::post('saveGestante',                 'MamitasSeguras\MamitasController@saveGestante');
    Route::post('getRegistrosMamitas',          'MamitasSeguras\MamitasController@getRegistrosMamitas');
    Route::post('saveEditGestante',             'MamitasSeguras\MamitasController@saveEditGestante');

});
