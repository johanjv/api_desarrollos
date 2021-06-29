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
    return User::with('roles')->where('email', $request->user()->email)->first();

});

Route::post('/login', 'LoginController@login');
Route::post('/register', 'RegisterController@register');

/* Globals Routes */
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', 'LoginController@logout');
    Route::get('getCountDash', 'GlobalsController@getCountDash');
    Route::post('insertDesarrollo', 'GlobalsController@insertDesarrollo');
    Route::get('consultaDesarrollo', 'GlobalsController@consultaDesarrollo'); 
    Route::get('getAllUsers', 'GlobalsController@getAllUsers');
    Route::post('saveEditUser', 'GlobalsController@saveEditUser');
    Route::get('getRoles', 'GlobalsController@getRoles');
    Route::post('saveRol', 'GlobalsController@saveRol');
    Route::post('getModulosPerDesarrollo', 'GlobalsController@getModulosPerDesarrollo');
    Route::post('insertModulo', 'GlobalsController@insertModulo');
    Route::get('consultaModulos', 'GlobalsController@consultaModulos');
});


