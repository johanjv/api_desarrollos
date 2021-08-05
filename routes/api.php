<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\User;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    //return $request->user();
    return User::with('roles')->where('id', $request->user()->id)->first();
});

Route::post('/login',       'LoginController@login');
Route::post('/register',    'RegisterController@register');

/* Globals Routes */
Route::post('/check',                   'LoginController@check');
Route::middleware('auth:api')->group(function () {
    Route::post('/checkAutorizacion',       'LoginController@checkAutorizacion');
    Route::post('/logout',                  'LoginController@logout');
    Route::post('getMenuDash',              'GlobalsController@getMenuDash'); /* Carga el menu */
    Route::get('getCountDash',              'GlobalsController@getCountDash');
    Route::post('insertDesarrollo',         'GlobalsController@insertDesarrollo');
    Route::get('consultaDesarrollo',        'GlobalsController@consultaDesarrollo');
    Route::get('getAllUsers',               'GlobalsController@getAllUsers');
    Route::post('saveEditUser',             'GlobalsController@saveEditUser');
    Route::get('getRoles',                  'GlobalsController@getRoles');
    Route::post('saveRol',                  'GlobalsController@saveRol');
    Route::post('getModulosPerDesarrollo',  'GlobalsController@getModulosPerDesarrollo');
    Route::post('insertModulo',             'GlobalsController@insertModulo');
    Route::get('consultaModulos',           'GlobalsController@consultaModulos');
    Route::post('saveNewUser',              'LoginController@saveNewUser');
    Route::post('insertRol',                'GlobalsController@insertRol');
    Route::get('consultaRoles',             'GlobalsController@consultaRoles');
    Route::post('saveSubmodulo',             'GlobalsController@saveSubmodulo');
    Route::get('consultaRoles',             'GlobalsController@consultaRoles');    
});
