<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API GESTIÓN DE PACIENTES Routes
|--------------------------------------------------------------------------
| Estas son las rutas para acceder al aplicativo GESTIÓN DE PACIENTES
|
*/

Route::middleware(['auth:api'])->group(function () {
    Route::prefix("gestion-pacientes")->group(function(){
        Route::get('getConsultoriosPerUnidad',  'GestionPacientes\UnidadesController@getConsultoriosPerUnidad');
        Route::post('asignarConsultorio',       'GestionPacientes\UnidadesController@asignarConsultorio');
        Route::post('saveConsultorio',          'GestionPacientes\UnidadesController@saveConsultorio');
        Route::get('getConteoConsultorios',    'GestionPacientes\UnidadesController@getConteoConsultorios');

        Route::get('getAgenda',             'GestionPacientes\AgendaController@getAgenda');
        Route::post('asignarPaciente',      'GestionPacientes\AgendaController@asignarPaciente');
        Route::get('getMedicosDisponibles', 'GestionPacientes\AgendaController@getMedicosDisponibles');
        Route::post('atenderPaciente',      'GestionPacientes\AgendaController@atenderPaciente');
    });
});
