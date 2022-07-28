<?php

use App\Events\ChangeStatusPeriodoEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\User;
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

Route::post('/login',     'LoginController@login');
Route::post('/register',  'RegisterController@register');
Route::post('/check', 'LoginController@check');

/* Globals Routes */
Route::middleware('auth:api')->group(function () {

    Route::post('/statusPeriodo',             'GestionResiduos\GestionResiduosController@statusPeriodo');

    Route::post('/emitEventvalidado',         'GestionResiduos\GestionResiduosController@emitEventvalidado');

    Route::get('/checkSession',             'LoginController@check');
    Route::post('/checkAutorizacion',       'LoginController@checkAutorizacion');
    Route::post('/logout',                  'LoginController@logout');
    Route::post('saveNewUser',              'LoginController@saveNewUser');
    Route::post('getMenuDash',              'AdminGlobal\GlobalsController@getMenuDash'); /* Carga el menu */
    Route::get('getCountDash',              'AdminGlobal\GlobalsController@getCountDash');
    Route::post('insertDesarrollo',         'AdminGlobal\GlobalsController@insertDesarrollo');
    Route::get('consultaDesarrollo',        'AdminGlobal\GlobalsController@consultaDesarrollo');
    Route::get('getAllUsers',               'AdminGlobal\GlobalsController@getAllUsers');
    Route::post('saveEditUser',             'AdminGlobal\GlobalsController@saveEditUser');
    Route::get('getRoles',                  'AdminGlobal\GlobalsController@getRoles');
    Route::post('saveRol',                  'AdminGlobal\GlobalsController@saveRol');
    Route::post('getModulosPerDesarrollo',  'AdminGlobal\GlobalsController@getModulosPerDesarrollo');
    Route::post('insertModulo',             'AdminGlobal\GlobalsController@insertModulo');
    Route::get('consultaModulos',           'AdminGlobal\GlobalsController@consultaModulos');
    Route::post('insertRol',                'AdminGlobal\GlobalsController@insertRol');
    Route::get('consultaRoles',             'AdminGlobal\GlobalsController@consultaRoles');
    Route::post('saveSubmodulo',            'AdminGlobal\GlobalsController@saveSubmodulo');
    Route::get('consultaRoles',             'AdminGlobal\GlobalsController@consultaRoles');

    /* GET MUNICIPIOS */
    Route::get('getMunicipios',             'AdminGlobal\GlobalsController@getMunicipios');

    /* GET LOCALIDADES */
    Route::get('getLocalidades',             'AdminGlobal\GlobalsController@getLocalidades');

    /* API AFILIADOS */
    Route::get('getAfiliado',               'Api_Afiliados_Interna\Api_Afiliados_InternaController@getAfiliado');
    Route::get('getTiposDocs',              'Api_Afiliados_Interna\Api_Afiliados_InternaController@getTiposDocs');

    /* GET BITACORA */
    Route::get('getHistorial',               'Bitacora\BitacoraController@getHistorial');
    Route::get('getConteoBit',               'Bitacora\BitacoraController@getConteoBit');

    Route::post('getFileFTP',                'AdminGlobal\GlobalsController@getFileFTP');

});

/* Citologias get unidades para el login */
Route::get('getSedesLogin',  'LoginController@getSedesLogin');

/* Certificados Escolares generar PDF */
Route::post('generarPdf',  'CertificadosEscolares\CertificadosEscolaresController@generarPdf');


