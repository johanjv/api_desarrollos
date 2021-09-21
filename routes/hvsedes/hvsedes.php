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
    Route::get('/getSucursales',        'Hvsedes\HVSedesController@getSucursales');
    Route::get('/getUnidades',          'Hvsedes\HVSedesController@getUnidades');
    Route::get('/loadData',             'Hvsedes\HVSedesController@loadData');
    Route::get('/loadservicios',        'Hvsedes\HVSedesController@loadservicios');
    Route::get('/getDataTable',         'Hvsedes\HVSedesController@getDataTable');
    Route::get('/getGrupos',            'Hvsedes\HVSedesController@getGrupos');
    Route::post('/saveGrupo',           'Hvsedes\HVSedesController@saveGrupo');
    Route::get('/getServicios',         'Hvsedes\HVSedesController@getServicios');
    Route::post('/saveServicio',        'Hvsedes\HVSedesController@saveServicio');
    Route::get('/getSed',               'Hvsedes\HVSedesController@getSed');
    Route::post('/saveVinculacion',     'Hvsedes\HVSedesController@saveVinculacion');
    Route::get('/getServHabs',          'Hvsedes\HVSedesController@getServHabs');
    Route::get('/getData',              'Hvsedes\HVSedesController@getData');
    Route::post('insertSedes',          'Hvsedes\HVSedesController@insertSedes');
    Route::post('getCodSucursales',     'Hvsedes\HVSedesController@getCodSucursales');
    Route::post('consultaSedes',        'Hvsedes\HVSedesController@consultaSedes');
    Route::post('estado',               'Hvsedes\HVSedesController@estado');
    Route::post('editarSedes',          'Hvsedes\HVSedesController@editarSedes');
    Route::get('getSedesPorSucursal',   'Hvsedes\HVSedesController@getSedesPorSucursal');
    Route::get('getSucursalesConSedes', 'Hvsedes\HVSedesController@getSucursalesConSedes');
    Route::post('getUserFilter',        'Hvsedes\HVSedesController@getUserFilter');
    Route::post('getPermisosUser',      'Hvsedes\HVSedesController@getPermisosUser');
    Route::post('savePermisosUser',     'Hvsedes\HVSedesController@savePermisosUser');
    Route::post('saveEditgrupo',        'Hvsedes\HVSedesController@saveEditgrupo');
    Route::post('saveEditServicio',     'Hvsedes\HVSedesController@saveEditServicio');
    Route::post('getGruposPorSede',     'Hvsedes\HVSedesController@getGruposPorSede');
    Route::post('cambiarEstadoSH',      'Hvsedes\HVSedesController@cambiarEstadoSH');
    Route::post('saveArea',             'Hvsedes\HVSedesController@saveArea');
    Route::get('getAreas',              'Hvsedes\HVSedesController@getAreas');
    Route::post('saveEditArea',         'Hvsedes\HVSedesController@saveEditArea');
    Route::post('saveServicioInfra',    'Hvsedes\HVSedesController@saveServicioInfra');
    Route::get('getServiciosInfra',     'Hvsedes\HVSedesController@getServiciosInfra');
    Route::post('saveEditServicioInfra','Hvsedes\HVSedesController@saveEditServicioInfra');
    Route::post('saveUnidad',           'Hvsedes\HVSedesController@saveUnidad');
    Route::get('getTiposUnidad',        'Hvsedes\HVSedesController@getTiposUnidad');
    Route::get('getUnidadesinfra',      'Hvsedes\HVSedesController@getUnidadesinfra');
    Route::post('saveEditUnidad',       'Hvsedes\HVSedesController@saveEditUnidad');
    Route::post('saveVinculacionInfra', 'Hvsedes\HVSedesController@saveVinculacionInfra');
    Route::get('getUnidadesPorSede',    'Hvsedes\HVSedesController@getUnidadesPorSede');
    Route::post('saveDocumentos',       'Hvsedes\HVSedesController@saveDocumentos');
    Route::get('getFilesPerTipo',       'Hvsedes\HVSedesController@getFilesPerTipo');
    Route::get('getPDF',                'Hvsedes\HVSedesController@getPDF');
    Route::post('deletePdf',             'Hvsedes\HVSedesController@deletePdf');
});
