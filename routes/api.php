<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    return $request->user();
});

Route::post('/login', 'LoginController@login');
Route::post('/register', 'RegisterController@register');

/* Globals Routes */
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', 'LoginController@logout');
    Route::get('getCountDash', 'GlobalsController@getCountDash');
    Route::get('getAllUsers', 'GlobalsController@getAllUsers');
    Route::post('saveEditUser', 'GlobalsController@saveEditUser');
    Route::get('getRoles', 'GlobalsController@getRoles');
    
});


