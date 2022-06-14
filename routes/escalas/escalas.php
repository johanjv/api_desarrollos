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
        Route::get('detalleGrafico',         'EscalasRehabilitacion\EscalasRehabilitacionController@detalleGrafico');
        Route::get('detalleStats',           'EscalasRehabilitacion\EscalasRehabilitacionController@detalleStats');

        Route::get('getProgramasPerAfi',     'EscalasRehabilitacion\EscalasRehabilitacionController@getProgramasPerAfi');
        Route::get('getAbandonos',           'EscalasRehabilitacion\EscalasRehabilitacionController@getAbandonos');
        Route::post('getEscalasPerPrograma', 'EscalasRehabilitacion\EscalasRehabilitacionController@getEscalasPerPrograma');
        Route::post('saveRegistroAfi',       'EscalasRehabilitacion\EscalasRehabilitacionController@saveRegistroAfi');
        Route::get('getProgramas',           'EscalasRehabilitacion\EscalasRehabilitacionController@getProgramas');
        Route::get('getDiagnosticos',        'EscalasRehabilitacion\EscalasRehabilitacionController@getDiagnosticos');
        Route::post('almacenarNewItem',      'EscalasRehabilitacion\EscalasRehabilitacionController@almacenarNewItem');
        Route::post('getResultados',         'EscalasRehabilitacion\EscalasRehabilitacionController@getResultados');
        Route::post('finalizarRegistro',     'EscalasRehabilitacion\EscalasRehabilitacionController@finalizarRegistro');

        Route::post('getHistorial',          'EscalasRehabilitacion\HistorialRehabilitacionController@getHistorial');
        Route::post('getDetalleHistorial',   'EscalasRehabilitacion\HistorialRehabilitacionController@getDetalleHistorial');

        Route::post('guardarEdicion',        'EscalasRehabilitacion\EscalasRehabilitacionController@guardarEdicion');
        Route::post('guardarNew',            'EscalasRehabilitacion\EscalasRehabilitacionController@guardarNew');
    });
});
