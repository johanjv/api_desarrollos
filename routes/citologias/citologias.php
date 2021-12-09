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
    Route::post('getRegistrosdelDia',        'Citologias\CitologiasController@getRegistrosdelDia');
    Route::post('saveCitologia',             'Citologias\CitologiasController@saveCitologia');
    Route::post('saveEditCitologia',         'Citologias\CitologiasController@saveEditCitologia');
    Route::post('getRegistros',              'Citologias\CitologiasController@getRegistros');
});
