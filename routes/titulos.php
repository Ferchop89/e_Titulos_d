<?php
use App\Models\{Web_Service, Alumno};
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
Route::get('/buscar', [
   'uses' => 'SolicitudTituloeController@searchAlum',
   'as' => 'registroTitulos/buscar',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
]);
      // ->name('eSearch');
   Route::post('/buscar', [
      'uses' => 'SolicitudTituloeController@postSearchAlum',
      'middleware' => 'roles',
      'roles' => ['Admin', 'Jtit']
   ]);
   Route::get('/buscar/{num_cta}', [
      'uses' => 'SolicitudTituloeController@showInfo',
      'as' => 'eSearchInfo',
      'middleware' => 'roles',
      'roles' => ['Admin', 'Jtit']
   ])
      ->where('num_cta','[0-9]+');

   Route::get('/solicitud-sep/{num_cta}/{nombre}/{carrera}/{nivel}', [
      'uses' => 'SolicitudTituloeController@existRequest',
      'as' => 'solicitar_SEP',
      'middleware' => 'roles',
      'roles' => ['Admin', 'Jtit']
   ])
      ->where('num_cta','[0-9]+')
      ->where('carrera','[0-9]+');

   Route::get('lista-solicitudes/pendientes', [
      'uses' => 'SolicitudTituloeController@showPendientes',
      'as' => 'registroTitulos/lista-solicitudes/pendientes',
      'middleware' => 'roles',
      'roles' => ['Admin', 'Jtit']
   ]);
   // Route::get('prueba', [
   //    'uses' => 'PruebasController@showPendientes',
   //    'as' => 'Pruebas',
   //    'middleware' => 'roles',
   //    'roles' => ['Admin']
   // ]);
Route::get('/buscar/fecha', [
      'uses' => 'SolicitudTituloeController@searchAlumDate',
      'as' => 'registroTitulos/buscar/fecha',
      'middleware' => 'roles',
      'roles' => ['Admin', 'Jtit']
]);
Route::post('/buscar/fecha', [
   'uses' => 'SolicitudTituloeController@postSearchAlumDate',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
]);

Route::post('/firma', [
   'uses'=> 'SolicitudTituloeController@nameButton',
   'as' => 'postEnviaFirma',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
]);

Route::get('response/firma',[
  'uses' => 'PruebasController@showFirmasP',
  'as' => 'registroTitulos/response/firma',
  'middleware' => 'roles',
  'roles' => ['Director', 'SecGral', 'Rector', 'Jtit'] //DIRECTORA, SECRETARIO, RECTOR Y JEFE TITULOS
]);
Route::post('/response/firma', [
   'uses' => 'SelloController@recibeFirma',
   'as' => 'Postfirmas',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Director', 'SecGral', 'Rector', 'Jtit'] //DIRECTORA, SECRETARIO Y RECTOR
]);
Route::get('/firmas_busqueda/seleccion', [
   'uses' => 'FirmasCedulaController@showFirmasBusqueda',
   'as' => 'registroTitulos/firmas_busqueda/seleccion',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
]);
Route::post('firmas_busqueda/seleccion', [
   'uses' => 'FirmasCedulaController@postFirmasBusqueda',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
]);
Route::get('/firmas_progreso', [
   'uses' => 'FirmasCedulaController@showProgreso',
   'as' => 'registroTitulos/firmas_progreso',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit', 'Director']
]);
// Route::post('/firmas_progreso', [
//    'uses' => 'FirmasCedulaController@postProgreso',
//    'middleware' => 'roles',
//    'roles' => ['Admin', 'Jtit']
// ]);
Route::get('/firmadas', [
  'uses' => 'FirmasCedulaController@showFirmadas',
  'as' => 'registroTitulos/firmadas',
  'middleware' => 'roles',
  'roles' => ['Admin', 'Rector', 'Director', 'SecGral','Jtit']
]);
Route::post('/firmadas', [
   'uses' => 'FirmasCedulaController@postFirmadas',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Rector', 'Director', 'SecGral','Jtit']
]);

/************* Generación de PDF de Envios DGP en link**************/
Route::get('pdf_DGP',[
    'uses' => 'FirmasCedulaController@pdf_DGP',
    'roles' => ['Admin','Jtit']
])->name('pdf_DGP');
/***************************/

Route::get('/proceso', [
   'uses' => 'AlumnosLotesController@showprocesoAlumno',
   'as' => 'procesoAlumno',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
])->where('num_cta','[0-9]+')
  ->where('carrera','[0-9]+');
Route::post('/proceso/cancela', [
     'uses' => 'AlumnosLotesController@cancelaProcesoAlumno',
     'as' => 'cancelaProcesoAlumno',
     'middleware' => 'roles',
     'roles' => ['Admin', 'Jtit']
  ])->where('num_cta','[0-9]+')
    ->where('carrera','[0-9]+');
Route::post('create/session',[
  'uses' => 'FirmasCedulaController@lote_Session',
  'as' => 'ALGO',
  'middleware' => 'roles',
  'roles' => ['Admin', 'Director', 'SecGral', 'Rector']
]);
Route::get('algo', function(){
   dd($_POST);
})->name('antesFirmar');

Route::get('/home', [
  'uses' => 'HomeController@index',
  'as'   => 'home'
]);

Route::get('envioSep',[
   // Tres firmas son las definitivas: Directora/Secretario/Rector.
   // Adicionalmente firma Jud de titulos
   'uses'=> 'EnvioSep@envio3Firmas',
   'as' => 'envioSep',
   'middleware' => 'roles',
   'roles' => ['admin','Jtit']
]);

Route::get('/lista-solicitudes/feLote',function(){
   $data = DB::table('solicitudes_sep')
          ->select('fec_emision_tit as emision',DB::raw('count(*) as total'))
          ->where('status', 1)
          ->orwhereNull('status')
          ->orderBy('emision','desc')
          ->groupBy('emision')
          ->pluck('emision','total')->all();
    if (count($data)>0) {
      foreach ($data as $key => $value) {
          $fecha = $value;
      }
    } else {
     $fecha = Carbon::now()->format('Y/m/d');
    }
  return $fecha;
});
Route::get('/lista-solicitudes/cedulasPen', function(){
   // Fechas de emision con registros pendientes de validar (sin errores), status no nulo ni vacio
    $data = DB::table('solicitudes_sep')
            ->select(DB::raw("DATE_FORMAT(fec_emision_tit,'%d-%m-%Y') as emision, count(*) as total "))
            // ->select('fec_emision_tit as emision',DB::raw('count(*) as total'))
             ->where('status', 1)
             ->orderBy('total','asc')
             ->groupBy('emision')
             ->pluck('total','emision')->all();
    return $data;
  });
  Route::get('/lista-solicitudes/cedulasPen2', function(){
     // Fechas de emision con registros pendientes de validar (sin errores), status no nulo ni vacio

      $query = "SELECT DATE_FORMAT(fec_emision_tit,'%d-%m-%Y') AS emision, ";
      $query .= "SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS pendientes, ";
      $query .= "SUM(CASE WHEN status <> 1 THEN 1 ELSE 0 END) AS enviadas ";
      $query .= "FROM solicitudes_sep ";
      $query .= "GROUP BY emision ";
      $query .= "ORDER BY fec_emision_tit";
      $data = DB::select($query);
      $datos = array();
      foreach ($data as $key => $value) {
         $datos[$value->emision]=[$value->enviadas, $value->pendientes];
      }
      return $datos;
    });
   Route::get('/cedulasDGP', function(){
   // Route::get('/lista-solicitudes/cedulasDGP', function(){
      // Fechas de envio de solicitudes a la DGP
       $query  = "SELECT DISTINCT DATE_FORMAT(tit_fec_DGP,'%d-%m-%Y') AS lote ";
       $query .= "FROM solicitudes_sep ";
       $query .= "WHERE tit_fec_DGP IS NOT NULL ";
       $query .= "GROUP BY tit_fec_DGP ";
       // $query .= "ORDER BY lote DESC";
       $data = DB::select($query);
       $datos = array();
       foreach ($data as $key => $value) {
          $datos[$key] = $value->lote;
       }
       return $datos;
      });
    Route::get('/lotes', function(){
       // Fechas de emision con registros pendientes de validar (sin errores), status no nulo ni vacio
        $query = "SELECT DATE_FORMAT(fecha_lote,'%d-%m-%Y') AS lote ";
        // $query .= "SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS pendientes, ";
        // $query .= "SUM(CASE WHEN status <> 1 THEN 1 ELSE 0 END) AS enviadas ";
        $query .= "FROM lotes_unam ";
        $query .= "GROUP BY lote";
        $data = DB::select($query);
        $datos = array();
        foreach ($data as $key => $value) {
           $datos[$key] = $value->lote;
        }
        return $datos;
      });
    Route::get('/lotes_firmados', function(){
         $rol = Auth::user()->roles()->get();
         $roles_us = array(); //Obtenemos los roles del usuario actual
         foreach($rol as $actual){
           array_push($roles_us, $actual->nombre);
         }
         switch ($roles_us[0]) {
            case 'Jtit':
               $num = 0;
               break;
            case 'Director':
               $num = 1;
               break;
            case 'SecGral':
               $num = 2;
               break;
            case 'Rector':
               $num = 3;
               break;
         }
          $query = "SELECT DATE_FORMAT(fec_firma".$num.",'%d-%m-%Y') AS lote ";
          $query .= "FROM lotes_unam ";
          $query .= "WHERE firma".$num." = 1 ";
          $query .= "GROUP BY lote";
          $data = DB::select($query);
          $datos = array();
          foreach ($data as $key => $value) {
             $datos[$key] = $value->lote;
          }
          return $datos;
        });
    Route::get('/lista-solicitudes/cedulasPen3', function(){
       // Fechas de emision con registros pendientes de validar (sin errores), status no nulo ni vacio
       $query = " DATE_FORMAT(fec_emision_tit,'%d-%m-%Y') AS emision, ";
       $query .= "SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS pendientes, ";
       $query .= "SUM(CASE WHEN status <> 1 THEN 1 ELSE 0 END) AS enviadas ";
       // $query .= "FROM solicitudes_sep ";
       // $query .= "GROUP BY emision";
         $datos = DB::table('solicitudes_sep')
            ->select(DB::raw($query))->groupBy('emision')->get();
        return $datos;
      });

      Route::get('/reporteDG', function(){
         // Contenido de los lotes.
         $query  = "SELECT DATE_FORMAT(fecha_lote,'%Y%m%d%H%i%s') as lote,num_cta, nombre_completo, cve_carrera, datos ";
         $query .= " from solicitudes_sep ";
         $query .= " WHERE (";
         $query .= "fec_emision_tit='2018-12-13 00:00:00' OR ";
         $query .= "fec_emision_tit='2018-12-06 00:00:00' OR ";
         $query .= "fec_emision_tit='2018-11-29 00:00:00' OR ";
         $query .= "fec_emision_tit='2018-11-22 00:00:00' OR ";
         $query .= "fec_emision_tit='2018-11-15 00:00:00' OR ";
         $query .= "fec_emision_tit='2018-11-08 00:00:00' OR ";
         $query .= "fec_emision_tit='2018-10-25 00:00:00' OR ";
         $query .= "fec_emision_tit='2018-10-18 00:00:00' OR ";
         $query .= "fec_emision_tit='2018-10-11 00:00:00' OR ";
         $query .= "fec_emision_tit='2018-10-04 00:00:00' ";
         $query .= ") AND errores like '%sin errores%' AND status>1 ";
         $query .= "ORDER BY lote asc";
         $datos = DB::select($query);
         $cuenta = 0;
         foreach ($datos as $alumno) {
            $datos = unserialize($alumno->datos);
            $cadena = ++$cuenta.','.$alumno->lote.','.$alumno->num_cta.','.$alumno->nombre_completo.','.$datos['_09_cveCarrera'].','.$datos['_10_nombreCarrera'];
            echo "<pre>";
            echo $cadena;
            echo "</pre>";
         }
        });

   Route::get('/SIAE', function(){
      // echo shell_exec("cd j_WS && javac abc.java && java XYZ");
      // shell_exec('cd j_WS ');
      // $salida = shell_exec('cd j_WS && ls -lart');
      // $salida = shell_exec('cd j_WS && java XYZ');
      // echo "<pre>$salida</pre>";
      // dd('alto');
      // Fechas de emision con registros pendientes de validar (sin errores), status no nulo ni vacio
      // $query = " select * from solicitudes_sep where ";
      // $query .= "fec_emision_tit='2018-10-18 00:00:00' OR ";
      // $query .= "fec_emision_tit='2018-10-11 00:00:00' OR ";
      // $query .= "fec_emision_tit='2018-10-04 00:00:00' ";
      // $query .= "FROM solicitudes_sep ";
      // $query .= "GROUP BY emision";
      // ws
      //$contraseñaConHash = '$2y$10$03JKFzQWes0R8YMuBrcWdO7SQEIGjVUHA4Rdf7HxzQcOeSsFkfFE.';
      //$contraseñaConHash = '$2y$10$KrS09XnEbEG8r2qFnjNVB.amSjwbCW5gpjvLUDts6hImcAtQADB4a';
      //dd($2y$10$DIbUrVU2Xs1j3tTrHLVL5eI4Gsc3GZgCu/xgv5UXZdj1g6S/LFU.G)
      //dd(Hash::check('08081974', $contraseñaConHash));
      //dd(bcrypt('08081974')); //--- CASO ESPECIAL ---
      $ws_SIAE = Web_Service::find(2);
      $identidad = new WSController();
      /*Numero de cuenta con problemas al consumir WS identidad SIAE*/
      //503459419
      //503006594
      //517493614---
      $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, '402000864', $ws_SIAE->key);
      // $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, '513013249', $ws_SIAE->key); --- INT/STRING
      //$identidad = $identidad->ws_SIAE($ws_SIAE->nombre, '054051690', $ws_SIAE->key);
      //$identidad = $identidad->ws_SIAE($ws_SIAE->nombre, '517493614', $ws_SIAE->key);
      // $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, '402048477', $ws_SIAE->key);
      // $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, '098040607', $ws_SIAE->key);

      // $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, '517490039', $ws_SIAE->key);
      // $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, '503006594', $ws_SIAE->key);
      // $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, '503459419', $ws_SIAE->key);
      // $identidadVal = array();
      //array_push((int)$identidad->(entidad-nacimiento));
      //dd($identidad->{'entidad-nacimiento'});
      dd((array)$identidad);
      //dd((array)$identidad);
      return (array)$identidad;
     });
   Route::get('/DGIRE2', function(){
      $ws = new WSController();
      // $respuesta = $ws->ws_DGIRE('503459419');
      // $respuesta = $ws->ws_DGIRE2('306573396');
      $respuesta = $ws->ws_DGIRE2('402000864');
      dd($respuesta);
   });
   Route::get('/RENAPO', function(){
      $ws = new WSController();
      // $respuesta = $ws->ws_DGIRE('503459419');
      // $respuesta = $ws->ws_DGIRE2('306573396');
      $respuesta = $ws->ws_RENAPO('VIMC930212MDFLNN08');
      dd($respuesta);
   });

   Route::get('/analisis', function(){
     // Fechas de emision con registros pendientes de validar (sin errores), status no nulo ni vacio
     $query = " select * from solicitudes_sep where ";
     $query .= "(fec_emision_tit='2018-11-08 00:00:00' OR ";
     $query .= "fec_emision_tit='2018-10-25 00:00:00' OR ";
     $query .= "fec_emision_tit='2018-10-18 00:00:00' OR ";
     $query .= "fec_emision_tit='2018-10-11 00:00:00' OR ";
     $query .= "fec_emision_tit='2018-10-04 00:00:00' ) AND ";
     $query .= " NOT errores LIKE '%sin errores%'";

     $datos = DB::select($query);
     $cadena = '';
     foreach ($datos as $value) {
        $errores = unserialize($value->errores); $cadenaErrores='';
        asort($errores);
        foreach ($errores as $error) {
           $cadenaErrores .= $error.'/';
        }
        $cadena = $value->num_cta.','.$value->nivel.','.$value->fec_emision_tit.','.$cadenaErrores;
        echo "<pre>";
        echo $cadena;
        echo "</pre>";
     }
      // return $datos;
    });

  Route::get('/lista-solicitudes/filtraCedula',[
    'uses'=> 'SolicitudTituloeController@showPendientes',
    'as'=> 'filtraCedula',
    'middleware' => 'roles',
    'roles' => ['Admin', 'Jtit']
  ]);

Route::get('/cedulas_DGP',[
    'uses'=> 'FirmasCedulaController@showCedulasDGP',
    'as'=> 'registroTitulos/cedulas_DGP',
    'middleware' => 'roles',
    'roles' => ['Admin', 'Jtit']
]);
Route::post('/cedulas_DGP',[
    'uses'=> 'FirmasCedulaController@postCedulasDGP',
    'middleware' => 'roles',
    'roles' => ['Admin', 'Jtit']
]);

// Route::get('/enviados_DGP', [
//   'uses'=> 'AlumnosLotesController@showEnviadosDGP',
//   'as'=> 'registroTitulos/enviados_DGP',
//   'middleware' => 'roles',
//   'roles' => ['Admin', 'Jtit']
// ]);
   Route::get('/emisionTitulos/fechasCargadas', function(){
      $query = "SELECT DATE_FORMAT(fec_emision_tit,'%d-%m-%Y') AS emision, ";
      $query .= "SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS pendientes, ";
      $query .= "SUM(CASE WHEN status <> 1 THEN 1 ELSE 0 END) AS enviadas ";
      $query .= "FROM solicitudes_sep ";
      $query .= "GROUP BY emision";
      $data = DB::select($query);
      $datos = array();
      foreach ($data as $key => $value) {
         $datos[$value->emision]=[$value->enviadas, $value->pendientes];
      }
      return $datos;
   });
  // Route::get('/emisionTitulos/CONDOC', function(){
  //     $anio = 2018;
  //     $mes = 10;
  //     $where = " CAST (datepart(year, tit_fec_emision_tit) AS numeric) >= ".$anio;
  //     // $where .= " CAST (datepart(month, tit_fec_emision_tit) AS numeric) >= ".$mes;
  //     $query = " tit_fec_emision_tit AS emision, ";
  //     $query .= " COUNT(*) AS total ";
  //     $datos = DB::connection('sybase')
  //        ->table('Titulos')
  //        ->select(DB::raw($query))
  //        ->whereRaw($where)
  //        ->orderBy('tit_fec_emision_tit','DESC')
  //        ->groupBy('tit_fec_emision_tit')
  //        ->get();
  //     return $datos;
  //   });
Route::get('/emisionTitulos/CONDOC', 'SolicitudTituloeController@verTitulos');
Route::get('/informacionDetallada/lote','AlumnosLotesController@showDetalleLote')->name('detalleLote');
Route::get('/informacionDetallada/enviadas/lote','AlumnosLotesController@showDetalleEnviadas')->name('detalleLote');
Route::get('/informacionDetallada/firmadas/lote','AlumnosLotesController@showDetalleFirmadas')->name('detalleFirmas');
Route::get('/informacionDetallada/cuenta/lote', [
     'uses' => 'AlumnosLotesController@showDetalleCuenta',
     'as' => 'detalleCuenta'
  ]);

  Route::get('/informacionDetallada/enviadas',[
      'uses'=> 'AlumnosLotesController@showFiltradas',
      'as'=> 'DetallesEnvio'
    ]);

/*Graficas */
Route::get('/cedulasG' ,[
    'uses'=> 'GrafiController@cedulas',
    'as' => 'registroTitulos/cedulasG',
    'roles' => ['Admin', 'Jtit', 'Director']
]);

/* Solicitudes que hayan sido canceladas */
Route::get('/solicitudes_canceladas',[
    'uses'=> 'AlumnosLotesController@showSCanceladas', //Cambiar a FirmasCedulaController
    'as'=> 'registroTitulos/solicitudes_canceladas',
    'middleware' => 'roles',
    'roles' => ['Admin', 'Jtit']
]);
Route::post('/solicitudes_canceladas', [
   'uses' => 'AlumnosLotesController@postSCanceladas',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
]);
Route::get('/solicitudes_canceladas/{num_cta}', [
   'uses' => 'AlumnosLotesController@showInfoSC',
   'as' => 'solicitud_cancelada',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
])->where('num_cta','[0-9]+');

/* Cédulas que van a cancelarse */
Route::get('/cedulas_canceladas', [
  'uses'=> 'AlumnosLotesController@showCancelarC', //Cambiar a FirmasCedulaController
  'as'=> 'registroTitulos/cedulas_canceladas',
  'middleware' => 'roles',
  'roles' => ['Admin', 'Jtit']
]);
Route::post('/cedulas_canceladas', [
   'uses' => 'AlumnosLotesController@postCancelarC',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
]);
Route::get('/cedulas_canceladas/{num_cta}', [
   'uses' => 'AlumnosLotesController@showInfoCC',
   'as' => 'cedula_cancelada',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
])->where('num_cta','[0-9]+');
Route::post('/cancelaC', [
   'uses' => 'EnvioSep@showCancelaAccion',
   'as' => 'cancelaC',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
])->where('num_cta','[0-9]+')
  ->where('carrera','[0-9]+');
/* */
Route::any('/response/componenteFirma', [
   'uses' => 'SelloController@keySat',
   'as' => 'componenteFirma',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Director']
]);
Route::post('/autorizando/sat', [
   'uses' => 'SelloController@generaSello',
   'as' => 'firmaSat',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Director']
]);
Route::post('/firmado/sat', [
   'uses' => 'SelloController@generaSello',
   'as' => 'firmaSat',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Director']
]);
Route::get('/response/autorizaTitulos', [
   'uses' => 'SelloController@autorizando',
   'as' => 'autorizaTitulos',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
]);
Route::post('/response/autorizacionLotes', [
   'uses' => 'SelloController@postAutorizando',
   'as' => 'postAutoriza',
   'middleware' => 'roles',
   'roles' => ['Admin', 'Jtit']
]);
Route::get('/descarga/{lote}', function($lote){
   $wsDGP = new WSController();
   $response = $wsDGP->ws_Dgp_Descarga($lote);
   file_put_contents("dgpDescarga/$lote.zip", $response->titulosBase64);
   return 'dgpDescarga: '.$lote.'.zip';
});
