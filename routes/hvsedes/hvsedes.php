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

Route::middleware('auth:api')->group(function () {
    Route::get('/getSucursales',    'Hvsedes\HomeController@getSucursales');
    Route::get('/getUnidades',      'Hvsedes\HomeController@getUnidades');
    Route::get('/loadData',         'Hvsedes\HomeController@loadData');
    Route::get('/loadservicios',    'Hvsedes\HomeController@loadservicios');
    Route::get('/getDataTable',     'Hvsedes\HomeController@getDataTable');
    Route::get('/getGrupos',        'Hvsedes\HomeController@getGrupos');
    Route::post('/saveGrupo',       'Hvsedes\HomeController@saveGrupo');
    Route::get('/getServicios',     'Hvsedes\HomeController@getServicios');
    Route::post('/saveServicio',    'Hvsedes\HomeController@saveServicio');
    Route::get('/getSed',           'Hvsedes\HomeController@getSed');
    Route::post('/saveVinculacion', 'Hvsedes\HomeController@saveVinculacion');
    Route::get('/getServHabs',      'Hvsedes\HomeController@getServHabs');
    Route::get('/getData',          'Hvsedes\HomeController@getData');
    Route::post('insertSedes',      'Hvsedes\HomeController@insertSedes');
    Route::post('getCodSucursales', 'Hvsedes\HomeController@getCodSucursales');
    Route::post('consultaSedes',    'Hvsedes\HomeController@consultaSedes');
    Route::post('estado',           'Hvsedes\HomeController@estado');
    Route::post('editarSedes',      'Hvsedes\HomeController@editarSedes');
});