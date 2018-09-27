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
Route::get('/hola', function(){
   dd("Hola");
});
Route::get('/algo', function(){
   dd("Algo");
});

// Route::get('/login', 'AutTransInfo\LoginController@showLoginForm')->name('AutTransInfo.login');
// Route::post('/login', 'AutTransInfo\LoginController@login')->name('AutTransInfoPost.login');
// Route::post('/logout', 'AutTransInfo\LoginController@logout')->name('AutTransInfo.logout');

// Authentication Routes...
// Route::get('login', 'AutTransInfo\LoginController@showLoginForm')->name('login');
// Route::post('login', 'AutTransInfo\LoginController@login');
// Route::post('logout', 'AutTransInfo\LoginController@logout')->name('logout');

// Registration Routes...
// Route::get('register', 'AutTransInfo\RegisterController@showRegistrationForm')->name('register');
// Route::post('register', 'AutTransInfo\RegisterController@register');

// Password Reset Routes...
// Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
// Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');

// Route::get('/login', 'AutTransInfo\LoginController@showLoginForm')->name('AutTransInfo.login');
// Route::post('/login', 'AutTransInfo\LoginController@login')->name('AutTransInfoPost.login');
// Route::post('/logout', 'AutTransInfo\LoginController@logout')->name('AutTransInfo.logout');

// Route::get('/ati', 'AutorizacionController@showATI');
