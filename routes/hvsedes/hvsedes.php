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
    Route::get('/getSucursales',        'HvSedes\HVSedesController@getSucursales');
    Route::get('/getUnidades',          'HvSedes\HVSedesController@getUnidades');
    Route::get('/loadData',             'HvSedes\HVSedesController@loadData');
    Route::get('/loadservicios',        'HvSedes\HVSedesController@loadservicios');
    Route::get('/getDataTable',         'HvSedes\HVSedesController@getDataTable');
    Route::get('/getGrupos',            'HvSedes\HVSedesController@getGrupos');
    Route::post('/saveGrupo',           'HvSedes\HVSedesController@saveGrupo');
    Route::get('/getServicios',         'HvSedes\HVSedesController@getServicios');
    Route::post('/saveServicio',        'HvSedes\HVSedesController@saveServicio');
    Route::get('/getSed',               'HvSedes\HVSedesController@getSed');
    Route::post('/saveVinculacion',     'HvSedes\HVSedesController@saveVinculacion');
    Route::get('/getServHabs',          'HvSedes\HVSedesController@getServHabs');
    Route::get('/getData',              'HvSedes\HVSedesController@getData');
    Route::post('insertSedes',          'HvSedes\HVSedesController@insertSedes');
    Route::post('getCodSucursales',     'HvSedes\HVSedesController@getCodSucursales');
    Route::post('consultaSedes',        'HvSedes\HVSedesController@consultaSedes');
    Route::post('estado',               'HvSedes\HVSedesController@estado');
    Route::post('editarSedes',          'HvSedes\HVSedesController@editarSedes');
    Route::get('getSedesPorSucursal',   'HvSedes\HVSedesController@getSedesPorSucursal');
    Route::get('getSucursalesConSedes', 'HvSedes\HVSedesController@getSucursalesConSedes');
    Route::post('getUserFilter',        'HvSedes\HVSedesController@getUserFilter');
    Route::post('getPermisosUser',      'HvSedes\HVSedesController@getPermisosUser');
    Route::post('savePermisosUser',     'HvSedes\HVSedesController@savePermisosUser');
    Route::post('saveEditgrupo',        'HvSedes\HVSedesController@saveEditgrupo');
    Route::post('saveEditServicio',     'HvSedes\HVSedesController@saveEditServicio');
    Route::post('getGruposPorSede',     'HvSedes\HVSedesController@getGruposPorSede');
    Route::post('cambiarEstadoSH',      'HvSedes\HVSedesController@cambiarEstadoSH');
    Route::post('saveArea',             'HvSedes\HVSedesController@saveArea');
    Route::get('getAreas',              'HvSedes\HVSedesController@getAreas');
    Route::post('saveEditArea',         'HvSedes\HVSedesController@saveEditArea');
    Route::post('saveServicioInfra',    'HvSedes\HVSedesController@saveServicioInfra');
    Route::get('getServiciosInfra',     'HvSedes\HVSedesController@getServiciosInfra');
    Route::post('saveEditServicioInfra','HvSedes\HVSedesController@saveEditServicioInfra');
    Route::post('saveUnidad',           'HvSedes\HVSedesController@saveUnidad');
    Route::get('getTiposUnidad',        'HvSedes\HVSedesController@getTiposUnidad');
    Route::get('getUnidadesinfra',      'HvSedes\HVSedesController@getUnidadesinfra');
    Route::post('saveEditUnidad',       'HvSedes\HVSedesController@saveEditUnidad');
    Route::post('saveVinculacionInfra', 'HvSedes\HVSedesController@saveVinculacionInfra');
    Route::get('getUnidadesPorSede',    'HvSedes\HVSedesController@getUnidadesPorSede');
    Route::post('saveDocumentos',       'HvSedes\HVSedesController@saveDocumentos');
    Route::get('getFilesPerTipo',       'HvSedes\HVSedesController@getFilesPerTipo');
    Route::get('getPDF',                'HvSedes\HVSedesController@getPDF');
    Route::post('deletePdf',            'HvSedes\HVSedesController@deletePdf');
    Route::post('saveColaborador',      'HvSedes\HVSedesController@saveColaborador');
    Route::get('getCargos',             'HvSedes\HVSedesController@getCargos');
    Route::get('getEps',                'HvSedes\HVSedesController@getEps');
    Route::get('getPlantaAdm',          'HvSedes\HVSedesController@getPlantaAdm');
    Route::post('importPlanta',         'HvSedes\HVSedesController@importPlanta');
    Route::post('saveEditColaborador',  'HvSedes\HVSedesController@saveEditColaborador');
    Route::post('saveRetiroColaborador','HvSedes\HVSedesController@saveRetiroColaborador');
    Route::get('getRetiros',            'HvSedes\HVSedesController@getRetiros');
    Route::post('saveCargo',            'HvSedes\HVSedesController@saveCargo');
    Route::post('saveCargoEdit',        'HvSedes\HVSedesController@saveCargoEdit');
});
