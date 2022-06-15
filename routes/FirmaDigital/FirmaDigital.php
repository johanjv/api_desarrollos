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
    Route::prefix("firma-digital")->group(function(){
        Route::get('getDireccion',          'FirmaDigital\FirmaDigitalController@getDireccion');
        Route::get('getDettaleColaborador', 'FirmaDigital\FirmaDigitalController@getDettaleColaborador');
        Route::post('saveEdit',             'FirmaDigital\FirmaDigitalController@saveEdit');
        Route::post('saveNewImagen',        'FirmaDigital\FirmaDigitalController@saveNewImagen');
        Route::post('saveNew',              'FirmaDigital\FirmaDigitalController@saveNew');
        Route::get('getImage',              'FirmaDigital\FirmaDigitalController@getImage');
        Route::post('saveBit',              'FirmaDigital\FirmaDigitalController@saveBit');
    });
});
