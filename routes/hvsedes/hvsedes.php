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
});