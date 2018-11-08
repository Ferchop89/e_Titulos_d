<?php
use App\Http\Controllers\Admin\WSController;
use App\Models\LotesUnam;
use App\Models\SolicitudSep;

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
use Illuminate\Http\Request;

// Route::get('/',function(){
//   return view("auth.login");
// });
//
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/home', function () {
    return redirect('/registroTitulos/home');
});
// Route::get('/login', function () {
//     return redirect('/registroTitulos/contactos/login');
// });

Auth::routes();


//Alumnos Login
Route::get('alumnos/login', 'Alumno\LoginController@showLoginForm')->name('alumno.login');
Route::post('alumnos/login', 'Alumno\LoginController@login');
Route::post('alumnos/logout', 'Alumno\LoginController@logout')->name('alumno.logout');



Route::get('/test', function(){
   $ws=new WSController();
   // $ws->ws_RENAPO('MIVL840216HMSRZR09');
   $ws->ws_RENAPO('PAEF890101HDFCSR07');

});
Route::get('/test2', function(){
   $ws=new WSController();
   $ws->ws_DGIRE('305016614');
});


// Route::get('/m1',[
//   'uses'=> 'RutasController@Menu1',
//   'as'=> 'm1',
//   'middleware' => 'roles',
//   'roles' => ['FacEsc','Jud']
//   ]);
// Route::get('/m2',[
//   'uses'=> 'RutasController@Menu1',
//   'as'=> 'm2',
//   'middleware' => 'roles',
//   'roles' => ['Jud','Sria','JSecc']
//   ]);
// Route::get('/m3',[
//   'uses'=> 'RutasController@Menu1',
//   'as'=> 'm3',
//   'middleware' => 'roles',
//   'roles' => ['JArea','Ofisi','FacEsc']
//   ]);
// Route::get('/m4',[
//   'uses'=> 'RutasController@Menu1',
//   'as'=> 'm4',
//   'middleware' => 'roles',
//   'roles' => ['Ofisi','AgUnam']
//   ]);
// Route::get('/m5',[
//   'uses'=> 'RutasController@Menu1',
//   'as'=> 'm5',
//   'middleware' => 'roles',
//   'roles' => ['FacEsc','Jud','Sria']
//   ]);
// Route::get('/m6',[
//   'uses'=> 'RutasController@Menu1',
//   'as'=> 'm6',
//   'middleware' => 'roles',
//   'roles' => ['JArea']
//   ]);
// Route::get('/m7',[
//   'uses'=> 'RutasController@Menu1',
//   'as'=> 'm7',
//   'middleware' => 'roles',
//   'roles' => ['Jud','Sria','JSecc']
//   ]);
// Route::get('/m8',[
//   'uses'=> 'RutasController@Menu1',
//   'as'=> 'm8',
//   'middleware' => 'roles',
//   'roles' => ['FacEsc','Ofisi']
//   ]);
// Route::get('/m9',[
//   'uses'=> 'RutasController@Menu1',
//   'as'=> 'm9',
//   'middleware' => 'roles',
//   'roles' => ['Jud','Ofisi']
//   ]);
//
// Route::get('pdf', 'PdfController@invoice');
//
// Route::get('imprimePDF',[
//   'uses'=> 'ListadosController@Pdfs',
//   'as'=> 'imprimePDF',
//   'middleware' => 'roles',
//   'roles' => ['Admin']
// ]);


Route::get('/buscar', 'EtitulosController@searchAlum')
      ->name('eSearch');

Route::post('/buscar', 'EtitulosController@postSearchAlum');

Route::get('/buscar/{num_cta}', 'EtitulosController@showInfo')
      ->where('num_cta','[0-9]+')
      ->name('eSearchInfo');

// Route::get('/registroTitulos/response/firma', 'SelloController@sendingInfo');





Route::get('/registroTitulos/verify/firma', 'SelloController@verifySignature');

// Route::get('test', 'CurpController@validacionCurp');

// Route::get('/contactos/ati', 'AutorizacionController@showATI');
// Route::post('/contactos/ati', 'AutorizacionController@postATI');
// Route::get('/contactos/imprimePDF_ATI',[
//     'uses'=> 'AutorizacionController@PdfAutTransInfo',
//     'as'=> 'imprimePDF_ATI',
//     // 'middleware' => 'roles',
//     // 'roles' => ['Invit', 'Admin']
//   ]);

// Route::get('/registroTitulos/contactos/login', 'AutTransInfo\LoginController@showLoginForm')->name('login');


Route::get('infoCedula/{cuenta}/{carrera}',[
   'uses'=> 'SolicitudTituloeController@infoCedula',
   'as'=> 'infoCedula',
   'middleware' => 'roles',
   'roles' => ['Admin']
]);
Route::get('infoCedula/{ids}',[
   'uses'=> 'SolicitudTituloeController@infoCedulaId',
   'middleware' => 'roles',
   'roles' => ['Admin']
]);

Route::get('registroTitulos/cadena/{lote}/{cargo}',[
   'uses'=> 'SolicitudTituloeController@loteCadena',
   'middleware' => 'roles',
   'roles' => ['admin']
]);
Route::post('test/firmas/{lote}/{cargo}',[
   'uses'=> 'SelloController@sendingInfo',
   'as' => 'sendingInfo',
   'middleware' => 'roles',
   'roles' => ['admin']
]);

  // Route::get('loteCedulas', function(){
  //     $data = DB::table('solicitudes_sep')
  //              ->select(DB::raw('fec_emision_tit as lote, count(*) as total'))
  //              ->where('fecha_lote','<>','NULL')
  //              ->orderBy('fecha_lote','asc')
  //              ->groupBy('lote')
  //              ->pluck('lote','total')->all();
  //     return $data;
  //   });

Route::get('test_wsdgp', 'EnvioSepController@index');

Route::get('ati-list', function(){
$alumnos = DB::connection('condoc_ati')->table('alumnos')->select('num_cta')->get();
// $alumnos = ['068081935'];
// dd($alumnos);
$datos = "";
foreach ($alumnos as $key => $value) {
   $cuenta = substr($value->num_cta, 0, 8);
   $verif = substr($value->num_cta, 8, 1);
   // $cuenta = substr($value, 0, 8);
   // $verif = substr($value, 8, 1);
   $query = "SELECT tit_fec_emision_tit FROM Titulos WHERE tit_ncta = '".$cuenta."' AND tit_dig_ver = '".$verif."'";
   $fechaE = DB::connection('sybase')->select($query);
   if($fechaE != [])
   {

      if($fechaE[0]->tit_fec_emision_tit!= null)
      {
         $datos = $value->num_cta.','.$fechaE[0]->tit_fec_emision_tit;
         // $datos = $value.','.$fechaE[0]->tit_fec_emision_tit;
         echo "<pre>
         $datos
         </pre>";
      }
   }

}
// dd($datos);
});

/*Graficas */
Route::get('/cedulasG' ,[
    'uses'=> 'GrafiController@cedulas',
    'as' => 'cedulasG',
    'roles' => ['Admin']
]);
/*Fin de procedimientos de graficas*/
Route::get('/nombreCarrera', 'PruebasController@carreras');
