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

Route::post('/v3/auth/login',     'v3\Auth\AuthController@login');


Route::middleware('auth:api')->group(function () {
    Route::prefix("v3")->group(function(){
        Route::get('auth/user',     'v3\Auth\AuthController@user');
        Route::post('auth/logout',  'v3\Auth\AuthController@logout');

    });
});
