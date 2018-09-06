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
use GuzzleHttp\Client;



Route::get('/',function(){
  return view("auth.login");
});

Auth::routes();

Route::get('/home', [
  'uses' => 'HomeController@index',
  'as'   => 'home'
]);

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

Route::get('pdf', 'PdfController@invoice');

Route::get('imprimePDF',[
  'uses'=> 'ListadosController@Pdfs',
  'as'=> 'imprimePDF',
  'middleware' => 'roles',
  'roles' => ['Admin']
]);


Route::get('/buscar', 'EtitulosController@searchAlum')
      ->name('eSearch');

Route::post('/buscar', 'EtitulosController@postSearchAlum');

Route::get('/buscar/{num_cta}', 'EtitulosController@showInfo')
      ->where('num_cta','[0-9]+')
      ->name('eSearchInfo');

Route::get('/solicitud-sep/{num_cta}/{carrera}/{nivel}', 'EtitulosController@existRequest')
      ->where('num_cta','[0-9]+')
      ->where('carrera','[0-9]+')
      ->name('solicitar_SEP');
// Route::get('/resgitroTitulos/request/firma', function(Request $request){
//    dd($request);
// });
Route::get('prueba', function(){
   $data = '{
	"name": "Aragorn",
	"race": "Human"
}';

$character = json_decode($data);
dd($character);
echo $character->name;
});
Route::get('/registroTitulos/response/firma', function(){
      $client = new Client([
         'base_uri' => 'https://enigma.unam.mx/componentefirma/initSigningProcess',
         'timeout' => 10.0
      ]);
      $datos = "||1.0|3|MUOC810214HCHRCR00|Director de Articulación de Procesos|SECRETARÍA DE EDUCACIÓN|Departamento de Control Escolar|23DPR0749T|005|23|SOSE810201HDFRND05|EDGAR|SORIANO|SANCHEZ|2|7.8|2017-01-01T12:05:00||";
      $response = $client->request('POST', '/', ['datos' => $datos]);
      // dd(json_encode($response->getBody()->getContents()));
      // return json_decode($response->getBody()->getContents());
});
Route::get('/registroTitulos/response/firma', 'SelloController@sendingInfo');
Route::post('/registroTitulos/request/firma?feu=true', function(){
});

Route::get('test', 'CurpController@validacionCurp');

// Route::all();
