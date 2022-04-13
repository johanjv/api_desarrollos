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

Route::post('/v2/auth/login',     'v2\Auth\AuthController@login');


Route::middleware('auth:api')->group(function () {
    Route::prefix("v2")->group(function(){
        Route::get('auth/user',     'v2\Auth\AuthController@user');
        Route::post('auth/logout',  'v2\Auth\AuthController@logout');

    });
});
