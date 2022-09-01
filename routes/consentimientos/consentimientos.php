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
    Route::prefix("consentimientos")->group(function(){
        Route::post('enviarLink', 'Consentimientos\ConsentimientosController@enviarLink');
        Route::get('getConsentimietoUser', 'Consentimientos\ConsentimientosController@getConsentimietoUser');
        Route::post('validarConsentimiento', 'Consentimientos\ConsentimientosController@validarConsentimiento');
        Route::get('getConsentimientosValidados', 'Consentimientos\ConsentimientosController@getConsentimientosValidados');
        Route::get('imprimir', 'Consentimientos\ConsentimientosController@imprimir');

    });
});
