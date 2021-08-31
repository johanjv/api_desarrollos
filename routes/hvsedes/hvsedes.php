<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API HVSEDES Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:api'])->group(function () {
    Route::get('/getSucursales',        'Hvsedes\HVSedesController@getSucursales');
    Route::get('/getUnidades',          'Hvsedes\HVSedesController@getUnidades');
    Route::get('/loadData',             'Hvsedes\HVSedesController@loadData');
    Route::get('/loadservicios',        'Hvsedes\HVSedesController@loadservicios');
    Route::get('/getDataTable',         'Hvsedes\HVSedesController@getDataTable');
    Route::get('/getGrupos',            'Hvsedes\HVSedesController@getGrupos');
    Route::post('/saveGrupo',           'Hvsedes\HVSedesController@saveGrupo');
    Route::get('/getServicios',         'Hvsedes\HVSedesController@getServicios');
    Route::post('/saveServicio',        'Hvsedes\HVSedesController@saveServicio');
    Route::get('/getSed',               'Hvsedes\HVSedesController@getSed');
    Route::post('/saveVinculacion',     'Hvsedes\HVSedesController@saveVinculacion');
    Route::get('/getServHabs',          'Hvsedes\HVSedesController@getServHabs');
    Route::get('/getData',              'Hvsedes\HVSedesController@getData');
    Route::post('insertSedes',          'Hvsedes\HVSedesController@insertSedes');
    Route::post('getCodSucursales',     'Hvsedes\HVSedesController@getCodSucursales');
    Route::post('consultaSedes',        'Hvsedes\HVSedesController@consultaSedes');
    Route::post('estado',               'Hvsedes\HVSedesController@estado');
    Route::post('editarSedes',          'Hvsedes\HVSedesController@editarSedes');
    Route::get('getSedesPorSucursal',   'Hvsedes\HVSedesController@getSedesPorSucursal');
    Route::get('getSucursalesConSedes', 'Hvsedes\HVSedesController@getSucursalesConSedes');
    Route::post('getUserFilter',        'Hvsedes\HVSedesController@getUserFilter');
    Route::post('getPermisosUser',      'Hvsedes\HVSedesController@getPermisosUser');
    Route::post('savePermisosUser',     'Hvsedes\HVSedesController@savePermisosUser');
    Route::post('saveEditgrupo',        'Hvsedes\HVSedesController@saveEditgrupo');
    Route::post('saveEditServicio',     'Hvsedes\HVSedesController@saveEditServicio');
});
