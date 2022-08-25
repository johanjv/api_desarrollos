<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API HVSEDES Routes
|--------------------------------------------------------------------------
| Estas son las rutas para acceder al aplicativo HVSEDES
|
*/

Route::middleware(['auth:api'])->group(function () {
    Route::prefix("vacunacion")->group(function(){

        Route::get('getTiposDoc',       'vacunacion\ParametricosController@getTiposDoc');
        Route::get('getEsquemas',       'vacunacion\ParametricosController@getEsquemas');
        Route::get('getSexo',           'vacunacion\ParametricosController@getSexo');
        Route::post('saveEsquema',      'vacunacion\ParametricosController@saveEsquema');
        Route::post('saveUpdEsquema',   'vacunacion\ParametricosController@saveUpdEsquema');

        Route::get('getRegimen',            'vacunacion\ParametricosController@getRegimen');
        Route::get('getAseguradora',        'vacunacion\ParametricosController@getAseguradora');
        Route::get('getPoblacional',        'vacunacion\ParametricosController@getPoblacional');
        Route::get('getPaises',             'vacunacion\ParametricosController@getPaises');
        Route::get('getDepartamentos',      'vacunacion\ParametricosController@getDepartamentos');
        Route::get('getMunicipios',         'vacunacion\ParametricosController@getMunicipios');
        Route::get('getPertenencias',       'vacunacion\ParametricosController@getPertenencias');
        Route::get('getCondicionesSalud',   'vacunacion\ParametricosController@getCondicionesSalud');
        Route::get('getPreguntasRespuestas','vacunacion\ParametricosController@getPreguntasRespuestas');

        Route::get('getRegistrosPrevios',   'vacunacion\RegistroPrevioController@getRegistrosPrevios');
        Route::post('savePreRegistro',      'vacunacion\RegistroPrevioController@savePreRegistro');

        Route::get('getPrevios',      'vacunacion\RegistroVacunaController@getPrevios');
    });
});
