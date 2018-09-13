<?php
use App\Http\Controllers\Admin\WSController;

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

/* RUTAS PARA << AUTORIZACIÓN DE TRANSFERENCIA DE INFORMACIÓN >> */
Route::get('/ati', 'AutorizacionController@showATI');
Route::post('/ati', 'AutorizacionController@postATI');
Route::get('imprimePDF_ATI',[
    'uses'=> 'AutorizacionController@PdfAutTransInfo',
    'as'=> 'imprimePDF_ATI',
    'middleware' => 'roles',
    'roles' => ['Invit', 'Admin']
  ]);
