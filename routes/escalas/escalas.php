<?php

use App\Http\Controllers\EscalasRehabilitacion\EscalasRehabilitacion;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API HVSEDES Routes
|--------------------------------------------------------------------------
| Estas son las rutas para acceder al aplicativo HVSEDES
|
*/

Route::middleware(['auth:api'])->group(function () {
    Route::prefix("escalas-rehabilitacion")->group(function(){
        Route::get('getProgramasPerAfi',    'EscalasRehabilitacion\EscalasRehabilitacionController@getProgramasPerAfi');
        Route::get('getAbandonos',          'EscalasRehabilitacion\EscalasRehabilitacionController@getAbandonos');
        Route::post('getEscalasPerPrograma', 'EscalasRehabilitacion\EscalasRehabilitacionController@getEscalasPerPrograma');
        Route::post('saveRegistroAfi',       'EscalasRehabilitacion\EscalasRehabilitacionController@saveRegistroAfi');
        Route::get('getProgramas',           'EscalasRehabilitacion\EscalasRehabilitacionController@getProgramas');
        Route::get('getDiagnosticos',        'EscalasRehabilitacion\EscalasRehabilitacionController@getDiagnosticos');
        Route::post('almacenarNewItem',      'EscalasRehabilitacion\EscalasRehabilitacionController@almacenarNewItem');
    });
});
