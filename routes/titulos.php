<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/buscar', [
   'uses' => 'SolicitudTituloeController@searchAlum',
   'as' => 'buscar',
   'middleware' => 'roles',
   'roles' => ['Admin']
]);
      // ->name('eSearch');
   Route::post('/buscar', [
      'uses' => 'SolicitudTituloeController@postSearchAlum',
      'middleware' => 'roles',
      'roles' => ['Admin']
   ]);
   Route::get('/buscar/{num_cta}', [
      'uses' => 'SolicitudTituloeController@showInfo',
      'as' => 'eSearchInfo',
      'middleware' => 'roles',
      'roles' => ['Admin']
   ])
      ->where('num_cta','[0-9]+');

   Route::get('/solicitud-sep/{num_cta}/{nombre}/{carrera}/{nivel}', [
      'uses' => 'SolicitudTituloeController@existRequest',
      'as' => 'solicitar_SEP',
      'middleware' => 'roles',
      'roles' => ['Admin']
   ])
      ->where('num_cta','[0-9]+')
      ->where('carrera','[0-9]+');

   Route::get('lista-solicitudes/pendientes', [
      'uses' => 'SolicitudTituloeController@showPendientes',
      'as' => 'solicitudesPendientes',
      'middleware' => 'roles',
      'roles' => ['Admin']
   ]);
Route::get('/buscar/fecha', [
      'uses' => 'SolicitudTituloeController@searchAlumDate',
      'as' => 'eSearchDate',
      'middleware' => 'roles',
      'roles' => ['Admin']
]);
Route::post('/buscar/fecha', [
   'uses' => 'SolicitudTituloeController@postSearchAlumDate',
   'middleware' => 'roles',
   'roles' => ['Admin']
]);
Route::get('/buscar/fecha/{fecha}', [
   'uses' => 'SolicitudTituloeController@showInfoDate',
   'as' => 'eSearchInfoDate',
   'middleware' => 'roles',
   'roles' => ['Admin']
]);
