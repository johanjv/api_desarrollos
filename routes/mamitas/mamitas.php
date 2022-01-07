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
    Route::post('getRegistrosdelDiaMamitas',   'MamitasSeguras\MamitasController@getRegistrosdelDia');
    Route::post('saveGestante',         'MamitasSeguras\MamitasController@saveGestante');
    Route::post('getRegistrosMamitas',         'MamitasSeguras\MamitasController@getRegistros');
    Route::post('saveEditGestante',      'MamitasSeguras\MamitasController@saveEditGestante');

});
