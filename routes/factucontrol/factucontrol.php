<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API FACTUCONTROL Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware(['auth:api'])->group(function () {
    Route::get('/getProveedor',           'Factucontrol\FactucontrolController@getProveedor');
    Route::post('/insertProveedor',       'Factucontrol\FactucontrolController@insertProveedor');
    Route::post('/editProveedor',         'Factucontrol\FactucontrolController@editProveedor');
    Route::post('/import',                'Factucontrol\FactucontrolController@import');
    Route::get('/getTemas',               'Factucontrol\FactucontrolController@getTemas');
    Route::get('/getCategorias',          'Factucontrol\FactucontrolController@getCategorias');
    Route::get('/temasRol',               'Factucontrol\FactucontrolController@temasRol');
    Route::get('/proveedores',            'Factucontrol\FactucontrolController@proveedores');
    Route::get('/sucursales',             'Factucontrol\FactucontrolController@sucursales');
    Route::post('/insertRadicado',        'Factucontrol\FactucontrolController@insertRadicado');
    Route::post('/usuarioda',             'Factucontrol\FactucontrolController@usuarioda');
    Route::get('/getCasos',               'Factucontrol\FactucontrolController@getCasos');
    Route::get('/conceptos',              'Factucontrol\FactucontrolController@conceptos');
    Route::get('/tipodoc',                'Factucontrol\FactucontrolController@tipodoc');
    Route::post('/editCasoEstado',        'Factucontrol\FactucontrolController@editCasoEstado');
    Route::get('/getCasosProceso',        'Factucontrol\FactucontrolController@getCasosProceso');
    Route::get('/getEstado',              'Factucontrol\FactucontrolController@getEstado');
    Route::post('/insertCaso',            'Factucontrol\FactucontrolController@insertCaso');
    Route::get('/gethistorial',           'Factucontrol\FactucontrolController@gethistorial');
    Route::post('/asignaCasoEnProceso',   'Factucontrol\FactucontrolController@asignaCasoEnProceso');
    Route::post('/editCasoProcesoEstado', 'Factucontrol\FactucontrolController@editCasoProcesoEstado');
    Route::post('/insertAdjuntoFac',       'Factucontrol\FactucontrolController@insertAdjuntoFac');
    Route::post('/adjuntarArchivo',       'Factucontrol\FactucontrolController@adjuntarArchivo');
    Route::post('/insertCasoProceso',     'Factucontrol\FactucontrolController@insertCasoProceso');
    Route::get('/gethistorialTime',       'Factucontrol\FactucontrolController@gethistorialTime');
    Route::get('/getcasosMasivos',        'Factucontrol\FactucontrolController@getcasosMasivos');
    Route::get('/getProveedoresDash',     'Factucontrol\FactucontrolController@getProveedoresDash');
    Route::get('/getConcDevo',            'Factucontrol\FactucontrolController@getConcDevo');
    Route::get('/getConcPago',            'Factucontrol\FactucontrolController@getConcPago');
    Route::get('/getOtros',               'Factucontrol\FactucontrolController@getOtros');
    Route::get('/getcasosMasivosAdmin',   'Factucontrol\FactucontrolController@getcasosMasivosAdmin');
    Route::post('/cierraCasosProceso',    'Factucontrol\FactucontrolController@cierraCasosProceso');
    Route::post('/permisosFac',           'Factucontrol\FactucontrolController@permisosFac');
    Route::post('/usuariodaPermisos',     'Factucontrol\FactucontrolController@usuariodaPermisos');

});
