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
    Route::post('getRegistrosdelDia',   'MamitasSeguras\MamitasController@getRegistrosdelDia');
    Route::post('saveGestante',         'MamitasSeguras\MamitasController@saveGestante');
    Route::post('getRegistros',         'MamitasSeguras\MamitasController@getRegistros');
    Route::post('saveEditGestante',      'MamitasSeguras\MamitasController@saveEditGestante');

});
