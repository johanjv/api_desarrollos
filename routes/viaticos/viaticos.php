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
        Route::get('/getSolicitudesAprobadas', 'Viaticos\ViaticosController@getSolicitudesAprobadas');
        Route::get('/getAerolineas', 'Viaticos\ViaticosController@getAerolineas');
        Route::get('/getOpciones', 'Viaticos\ViaticosController@getOpciones');
        Route::get('/getGrupos', 'Viaticos\ViaticosController@getGrupos');
        Route::post('/getHoteles', 'Viaticos\ViaticosController@getHoteles');
        Route::get('/getAcomodacion', 'Viaticos\ViaticosController@getAcomodacion');
        Route::post('/getAcomodacionTarifas', 'Viaticos\ViaticosController@getAcomodacionTarifas');
        Route::get('/getAlimentos', 'Viaticos\ViaticosController@getAlimentos');
        Route::post('/getCalculaDias', 'Viaticos\ViaticosController@getCalculaDias');
        Route::post('/getValorAeroSucursal', 'Viaticos\ViaticosController@getValorAeroSucursal');
        Route::get('/getSeguro', 'Viaticos\ViaticosController@getSeguro');
        Route::post('/insertItinerarios', 'Viaticos\ViaticosController@insertItinerarios');
        Route::post('/getDatosColaborador', 'Viaticos\ViaticosController@getDatosColaborador');
        Route::post('/cancelaRegistro', 'Viaticos\ViaticosController@cancelaRegistro');
        Route::get('/getViaticosDash', 'Viaticos\ViaticosController@getViaticosDash');
        Route::get('/getHotelesAdm', 'Viaticos\ViaticosController@getHotelesAdm');
        Route::post('/editHotel', 'Viaticos\ViaticosController@editHotel');
        Route::post('/agregaHoteInsert', 'Viaticos\ViaticosController@agregaHoteInsert');
        Route::post('/agregaHoteTarifa', 'Viaticos\ViaticosController@agregaHoteTarifa');
        Route::get('/getHotelesTarifas', 'Viaticos\ViaticosController@getHotelesTarifas');
        Route::post('/editaTarifa', 'Viaticos\ViaticosController@editaTarifa');
        Route::get('/getMillas', 'Viaticos\ViaticosController@getMillas');
        Route::post('/insertMillas', 'Viaticos\ViaticosController@insertMillas');
        Route::post('/editarMillas', 'Viaticos\ViaticosController@editarMillas');
        Route::post('/insertAerolineas', 'Viaticos\ViaticosController@insertAerolineas');
        Route::post('/editarAerolineas', 'Viaticos\ViaticosController@editarAerolineas');
        Route::post('/insertAcomodacion', 'Viaticos\ViaticosController@insertAcomodacion');
        Route::post('/insertAcomodacion', 'Viaticos\ViaticosController@insertAcomodacion');
        Route::post('/insertMotivo', 'Viaticos\ViaticosController@insertMotivo');
        Route::post('/editarMotivo', 'Viaticos\ViaticosController@editarMotivo');
        Route::post('/insertGrupos', 'Viaticos\ViaticosController@insertGrupos');
        Route::post('/editarGrupo', 'Viaticos\ViaticosController@editarGrupo');
        Route::post('/insertRuta', 'Viaticos\ViaticosController@insertRuta');
        Route::post('/editarRuta', 'Viaticos\ViaticosController@editarRuta');
        Route::get('/getTarifaViaticos', 'Viaticos\ViaticosController@getTarifaViaticos');
        Route::post('/insertTarifa', 'Viaticos\ViaticosController@insertTarifa');
        Route::post('/editarTarifa', 'Viaticos\ViaticosController@editarTarifa');
        Route::get('/getSolicitudesAdmin', 'Viaticos\ViaticosController@getSolicitudesAdmin');
        Route::post('/insertItinerariosNo', 'Viaticos\ViaticosController@insertItinerariosNo');
    });
});
