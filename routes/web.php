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
use Illuminate\Http\Request;

Route::get('/',function(){
  return view("auth.login");
});

Auth::routes();

Route::get('/home', [
  'uses' => 'HomeController@index',
  'as'   => 'home'
]);
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

Route::get('/registroTitulos/response/firma', 'SelloController@sendingInfo');
Route::post('/registroTitulos/request/firma', function(Request $request){
   define("PKCS7_HEADER", "-----BEGIN PKCS7-----");
   $result = "";
   if(isset($_POST['firmas']))
   {
      $result = $_POST['firmas'];
   }
   else {
      echo "Error: No se recibió el resultado de la firma";
   }
   if(substr($result, strpos($result, PKCS7_HEADER), strlen(PKCS7_HEADER)) == PKCS7_HEADER) {
      echo "Firma exitosa";
   }
   else {
      if($result == 102 || $result == 103){
         $errMsg = "error";
      }
      elseif($result >= 104 && $result <= 107){
         $errMsg = "error";
      }
   }
   dd($request, json_decode($request->firmas)->signatureResults[0], base64_encode(json_decode($request->firmas)->signatureResults[0]));
});

Route::get('/registroTitulos/verify/firma', 'SelloController@verifySignature');

// Route::get('test', 'CurpController@validacionCurp');
