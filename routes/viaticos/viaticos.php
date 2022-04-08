<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::prefix("viaticos")->group(function () {
        Route::get('/getSucursal', 'Viaticos\ViaticosController@getSucursal');
        Route::get('/getMotivoViajes', 'Viaticos\ViaticosController@getMotivoViajes');
        Route::post('/insertSolicitud', 'Viaticos\ViaticosController@insertSolicitud');
        Route::post('/usuarioda', 'Viaticos\ViaticosController@usuarioda');
        Route::get('/getDirectivos', 'Viaticos\ViaticosController@getDirectivos');
        Route::get('/getSolicitudes', 'Viaticos\ViaticosController@getSolicitudes');
        Route::post('/aprobacion', 'Viaticos\ViaticosController@aprobacion');
        Route::post('/getRechazoSolicitud', 'Viaticos\ViaticosController@getRechazoSolicitud');
    });
});
