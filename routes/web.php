<?php

use App\Models\Corte;
use App\Models\Solicitud;

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

/* Ejemplo de ruta
Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/',function(){
  return view("auth.login");
});

Route::get('/usuarios','UserController@usuarios')
      ->name('users')
      ->middleware('auth');

Route::get('/usuarios/{user}','UserController@ver_usuario')
      ->where('user','[0-9]+')
      ->name('users.ver_usuario');

Route::get('/usuarios/{user}/editar',[
    'uses'=> 'UserController@editar_usuario',
    'as'=> 'users.editar_usuarios',
    'middleware' => 'roles',
    'roles' => ['Admin']
    ]);

Route::delete('/usuarios/{user}',[
      'uses' => 'UserController@destroy',
      'as'   => 'users.destroy'
    ]);

Route::get('/usuarios/nuevo',[
  'uses'=> 'UserController@crear_usuario',
  'as'=> 'users.crear_usuario',
  'middleware' => 'roles',
  'roles' => ['Admin']
  ]);

Route::put('/usuarios/{user}','UserController@update');

Route::post('/usuarios','UserController@store');

Auth::routes();

Route::get('/home', [
  'uses' => 'HomeController@index',
  'as'   => 'home'
]);

Route::get('/FacEsc/consulta_re', 'FacEscController@index')->name('consulta_re');

Route::post('/FacEsc/consulta_re', 'FacEscController@store');

Route::get('/m1',[
  'uses'=> 'RutasController@Menu1',
  'as'=> 'm1',
  'middleware' => 'roles',
  'roles' => ['FacEsc','Jud']
  ]);
Route::get('/m2',[
  'uses'=> 'RutasController@Menu1',
  'as'=> 'm2',
  'middleware' => 'roles',
  'roles' => ['Jud','Sria','JSecc']
  ]);
Route::get('/m3',[
  'uses'=> 'RutasController@Menu1',
  'as'=> 'm3',
  'middleware' => 'roles',
  'roles' => ['JArea','Ofisi','FacEsc']
  ]);
Route::get('/m4',[
  'uses'=> 'RutasController@Menu1',
  'as'=> 'm4',
  'middleware' => 'roles',
  'roles' => ['Ofisi','AgUnam']
  ]);
Route::get('/m5',[
  'uses'=> 'RutasController@Menu1',
  'as'=> 'm5',
  'middleware' => 'roles',
  'roles' => ['FacEsc','Jud','Sria']
  ]);
Route::get('/m6',[
  'uses'=> 'RutasController@Menu1',
  'as'=> 'm6',
  'middleware' => 'roles',
  'roles' => ['JArea']
  ]);
Route::get('/m7',[
  'uses'=> 'RutasController@Menu1',
  'as'=> 'm7',
  'middleware' => 'roles',
  'roles' => ['Jud','Sria','JSecc']
  ]);
Route::get('/m8',[
  'uses'=> 'RutasController@Menu1',
  'as'=> 'm8',
  'middleware' => 'roles',
  'roles' => ['FacEsc','Ofisi']
  ]);
Route::get('/m9',[
  'uses'=> 'RutasController@Menu1',
  'as'=> 'm9',
  'middleware' => 'roles',
  'roles' => ['Jud','Ofisi']
  ]);

  // Informes
  Route::get('/rev',[
    'uses'=> 'InformesController@Revisiones',
    'as'=> 'Rev',
    'middleware' => 'roles',
    'roles' => ['Admin']
    ]);

 Route::get('dropdowns',function(){
    return view('components/dropdowns');
  });

Route::get('/cortes',[
  'uses'=> 'InformesController@cortes',
  'as'=> 'cortes',
  'middleware' => 'roles',
  'roles' => ['Admin']
]);

Route::put('/creaListas',[
  'uses'=> 'InformesController@creaListas',
  'as'=> 'creaListas',
  'middleware' => 'roles',
  'roles' => ['Admin']
]);

Route::get('/listas',[
  'uses'=> 'ListadosController@listas',
  'as'=> 'listas',
  'middleware' => 'roles',
  'roles' => ['Admin']
]);

Route::get('solicitudes', function(){
  $data = DB::table('solicitudes')
           ->select(db::raw('DATE_FORMAT(created_at, "%d.%m.%Y") as listado_corte'),
             DB::raw('count(*) as total'))
           ->where('pasoACorte',false)
           ->where('cancelada',false)
           ->orderBy('created_at','asc')
           ->groupBy('listado_corte')
           ->pluck('total','listado_corte')->all();
  return $data;
});

Route::get('grupoListas', function(){
  $data = DB::table('cortes')
           ->select('listado_corte as corte',
                    DB::raw('count(*) as cuenta'),
                    DB::raw('count(DISTINCT listado_id) as listas'))
           ->groupBy('corte')
           ->get();
  return $data;
});

Route::get('fechaCorte',function(){
   $fCorte = Corte::all()->last()->listado_corte;
   return $fCorte;
});

Route::get('listax', function(){
  $data = DB::table('cortes')
           ->select('listado_corte', DB::raw('count(*) as total'))
           ->groupBy('listado_corte')
           ->pluck('total','listado_corte')->all();
  return $data;
});

Route::get('pdf', 'PdfController@invoice');

Route::get('imprimePDF',[
  'uses'=> 'ListadosController@Pdfs',
  'as'=> 'imprimePDF',
  'middleware' => 'roles',
  'roles' => ['Admin']
]);
