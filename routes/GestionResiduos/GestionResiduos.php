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
        Route::post('getDataCalendar',          'GestionResiduos\GestionResiduosController@getDataCalendar');
        Route::get('getClasif',                 'GestionResiduos\GestionResiduosController@getClasif');
        Route::post('saveRegistroDiario',       'GestionResiduos\GestionResiduosController@saveRegistroDiario');
        Route::post('aRevision',                'GestionResiduos\GestionResiduosController@aRevision');
        Route::get('getPendientes',             'GestionResiduos\GestionResiduosController@getPendientes');
        Route::get('getDetallePeriodo',         'GestionResiduos\GestionResiduosController@getDetallePeriodo');
        Route::post('updatedStatus',            'GestionResiduos\GestionResiduosController@updatedStatus');
        Route::post('getDatosDia',              'GestionResiduos\GestionResiduosController@getDatosDia');
        Route::post('editarRegistro',           'GestionResiduos\GestionResiduosController@editarRegistro');
        Route::post('rechazarPeriodo',          'GestionResiduos\GestionResiduosController@rechazarPeriodo');
    });
});
