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
    Route::get('/getProveedor',    'Factucontrol\FactucontrolController@getProveedor');
    Route::post('/insertProveedor', 'Factucontrol\FactucontrolController@insertProveedor');
    Route::post('/editProveedor', 'Factucontrol\FactucontrolController@editProveedor');

    Route::post('/import', 'Factucontrol\FactucontrolController@import');
});
