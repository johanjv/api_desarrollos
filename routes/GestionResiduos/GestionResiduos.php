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
        Route::post('saveDocumentosRes',        'GestionResiduos\GestionResiduosController@saveDocumentosRes');
        Route::post('getFileFTPResiduos',       'GestionResiduos\GestionResiduosController@getFileFTPResiduos');
        Route::post('saveClas',                 'GestionResiduos\GestionResiduosController@saveClas');
        Route::get('getCat',                    'GestionResiduos\GestionResiduosController@getCat');
        Route::post('saveCat',                  'GestionResiduos\GestionResiduosController@saveCat');
        Route::get('getRes',                    'GestionResiduos\GestionResiduosController@getRes');
        Route::post('saveRes',                  'GestionResiduos\GestionResiduosController@saveRes');
        Route::post('saveEditItem',             'GestionResiduos\GestionResiduosController@saveEditItem');
        Route::post('saveEditItemCat',             'GestionResiduos\GestionResiduosController@saveEditItemCat');
        Route::post('saveEditItemRes',             'GestionResiduos\GestionResiduosController@saveEditItemRes');
    });
});
