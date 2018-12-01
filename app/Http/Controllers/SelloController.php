<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Traits\Consultas\XmlCadenaErrores;
use Carbon\Carbon;
use DB;
use Session;

class SelloController extends Controller
{
   use XmlCadenaErrores;
   public function generaSello()
   {
      dd($_POST);
   }
   public function sendingInfo()
   {
      // $datos = "||1.0|3|MUOC810214HCHRCR00|Director de Articulación de Procesos|SECRETARÍA DE EDUCACIÓN|Departamento de Control Escolar|23DPR0749T|005|23|TSEP180817HRECTR|EDGAR|SORIANO|SANCHEZ|2|7.8|2017-01-01T12:05:00||";
      // $datos = "||1.0|201800001|TSEP180817HRECTR00|3|RECTOR|DR.|090001|UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO|103601|DOCTORADO EN CIENCIAS (BIOLOGÍA)|1979/01/01|2000/01/01|8|DECRETO DE CREACIÓN||SELIL890909FDFRL10|LUZ MARIA GRACIELA|Serrano|LIMON|biologia@gmail.com|2000-12-14|01|POR TESIS|2000-09-12|2000-09-12|1|2|ART. 55 LRART. 5 CONST|09|CIUDAD DE MÉXICO|FACULTAD DE CIENCIAS|1|MAESTRÍA|09|CIUDAD DE MÉXICO|1976/01/01|1978/01/01||";
      // $datos .= "'@_@'";
      // $datos .= "||1.0|201800001|TSEP180817HRECTR00|3|RECTOR|DR.|090001|UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO|103601|DOCTORADO EN CIENCIAS (BIOLOGÍA)|1979/01/01|2000/01/01|8|DECRETO DE CREACIÓN||SELIL890909FDFRL10|LUZ MARIA GRACIELA|Serrano|LIMON|biologia@gmail.com|2000-12-14|01|POR TESIS|2000-09-12|2000-09-12|1|2|ART. 55 LRART. 5 CONST|09|CIUDAD DE MÉXICO|FACULTAD DE CIENCIAS|1|MAESTRÍA|09|CIUDAD DE MÉXICO|1976/01/01|1978/01/01||";
      // $datos .= "'@_@'";
      // $datos .= "||1.0|201800001|TSEP180817HRECTR00|3|RECTOR|DR.|090001|UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO|103601|DOCTORADO EN CIENCIAS (BIOLOGÍA)|1979/01/01|2000/01/01|8|DECRETO DE CREACIÓN||SELIL890909FDFRL10|LUZ MARIA GRACIELA|Serrano|LIMON|biologia@gmail.com|2000-12-14|01|POR TESIS|2000-09-12|2000-09-12|1|2|ART. 55 LRART. 5 CONST|09|CIUDAD DE MÉXICO|FACULTAD DE CIENCIAS|1|MAESTRÍA|09|CIUDAD DE MÉXICO|1976/01/01|1978/01/01||";
      // $datos = "||1.0|201800001|UIES180831HDFSEP01|3|RECTOR|DR.|090001|UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO|103601|DOCTORADO EN CIENCIAS (BIOLOGÍA)|1979/01/01|2000/01/01|8|DECRETO DE CREACIÓN||SELIL890909FDFRL10|LUZ MARIA GRACIELA|Serrano|LIMON|biologia@gmail.com|2000-12-14|01|POR TESIS|2000-09-12|2000-09-12|1|2|ART. 55 LRART. 5 CONST|09|CIUDAD DE MÉXICO|FACULTAD DE CIENCIAS|1|MAESTRÍA|09|CIUDAD DE ";
      // $datos .= "MÉXICO|1976/01/01|1978/01/01||@_@||1.0|201800001|UIES180831HDFSEP01|3|RECTOR|DR.|090001|UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO|103601|DOCTORADO EN CIENCIAS (BIOLOGÍA)|1979/01/01|2000/01/01|8|DECRETO DE CREACIÓN||SELIL890909FDFRL10|LUZ MARIA GRACIELA|Serrano|LIMON|biologia@gmail.com|2000-12-14|01|POR TESIS|2000-09-12|2000-09-12|1|2|ART. 55 LRART. 5 CONST|09|CIUDAD DE MÉXICO|FACULTAD DE CIENCIAS|1|MAESTRÍA|09|CIUDAD DE ";
      // $datos .= "MÉXICO|1976/01/01|1978/01/01||@_@||1.0|201800001|UIES180831HDFSEP01|3|RECTOR|DR.|090001|UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO|103601|DOCTORADO EN CIENCIAS (BIOLOGÍA)|1979/01/01|2000/01/01|8|DECRETO DE CREACIÓN||SELIL890909FDFRL10|LUZ MARIA GRACIELA|Serrano|LIMON|biologia@gmail.com|2000-12-14|01|POR TESIS|2000-09-12|2000-09-12|1|2|ART. 55 LRART. 5 CONST|09|CIUDAD DE MÉXICO|FACULTAD DE CIENCIAS|1|MAESTRÍA|09|CIUDAD DE MÉXICO|1976/01/01|1978/01/01||";


      // $datos = "||||@_@||||@_@||||";
      // $curp = "TSEP180817HRECTR00";
      $url = "https://condoc.dgae.unam.mx/registroTitulos/response/firma";
      $datos = $this->loteCadena($_GET['fecha_lote'], "Directora");
      dd($datos);
      $curp = "UIES180831HDFSEP01";
      // dd($datos);

      // return redirect()->route();



      return view('/menus/sendingCadena', compact('url', 'datos', 'curp', 'user'));
   }

   public function verifySignature()
   {
      $url = "https://condoc.dgae.unam.mx/registroTitulos/verify/firma";

      $datos = "||1.0|201800001|UIES180831HDFSEP01|6|SECRETARIO GENERAL|DR.|090001|UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO|103601|DOCTORADO EN CIENCIAS (BIOLOGÍA)|1979/01/01|2000/01/01|8|DECRETO DE CREACIÓN||SELIL890909FDFRL10|LUZ MARIA GRACIELA|Serrano|LIMON|biologia@gmail.com|2000-12-14|01|POR TESIS|2000-09-12|2000-09-12|1|2|ART. 55 LRART. 5 CONST|09|CIUDAD DE MÉXICO|FACULTAD DE CIENCIAS|1|MAESTRÍA|09|CIUDAD DE MÉXICO|1976/01/01|1978/01/01||";

      $pkcs7 =
      "-----BEGIN PKCS7-----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-----END PKCS7-----";



         $data = [
         	"listaPkcs7" => [$pkcs7],
         	"listaDatos" => [base64_encode($datos)],
         ];

         $info = json_encode($data);
         $url = "https://condoc.dgae.unam.mx/registroTitulos/response/firma";
         // $curp = "UIES180831HDFSEP01";


    // $data = $request->json()->all();

         return view('/menus/verifySignature', compact('info', 'url', 'curp'));
   }
   public function recibeFirma()
   {
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
         // Lote.
         $lote = $_GET['lote'];
         // arreglo de numeros de cuenta correspondientes a la firma
         $cuentas = explode('*',$_GET['cuentas']);
         // arreglo de cadenas firmadas correspondientes a los numeros de cuente en el arreglo $cuentas
         $cadenas = json_decode($result)->signatureResults;
         // cuenta de cadenas regresadas
         $numCadRequest = count(json_decode($result)->signatureResults);
         // Almacenamiento de las firmas
         $this->guardaFirma($cuentas, $cadenas, $lote, $this->envioVSrecepcion($numCadRequest, $lote));
         return redirect()->route('registroTitulos/response/firma');
      }
      else {
         if($result == 102 || $result == 103){
            $errMsg = "error";
         }
         elseif($result >= 104 && $result <= 107){
            $errMsg = "error";
         }
      }
      // dd($result, $_GET);
      // dd($result, json_decode($result->firmas)->signatureResults, base64_encode(json_decode($result->firmas)->signatureResults[0]));
   }
   public function guardaFirma($cuentas, $cadenas, $lote, $estado)
   {
      // dd(unserialize($cadenas), $lote);
      $rol = Auth::user()->roles()->first()->nombre;
      $msj = "";
      switch ($rol) {
         case 'Jtit':
            $msj = $this->guardarTitulos($cuentas, $cadenas, $lote, $estado);
            break;
         case 'Director':
            $msj = $this->guardarDirectora($cuentas, $cadenas, $lote, $estado);
            break;
         case 'SecGral':
            $msj = $this->guardarSecretario($cuentas, $cadenas, $lote, $estado);
            break;
         case 'Rector':
            $msj = $this->guardarRector($cuentas, $cadenas, $lote, $estado);
            break;
         default:
            $msj = "Permisos insuficientes";
            break;
      }
      return $msj;
   }

   public function envioVSrecepcion($cadRequest, $lote){
      $enviaron = DB::table('solicitudes_sep')
                 ->where('fecha_lote', $lote)
                 ->count();
      if($cadRequest == $enviaron)
      {
         return true;
      }
      else {
         return false;
      }
   }
   public function guardarTitulos($cuentas, $cadenas, $lote, $estado){
      if($estado){
         // Se ha realizado la firma 1
         DB::table('lotes_unam')
                    ->where('fecha_lote', $lote)
                    ->update(['firma0' => true,
                              'fec_firma0'   => Carbon::now(),
                     ]);
         // Se actualizan las firmas0 en la tabla de solicituds
         for ($k=0; $k < count($cadenas); $k++) {
            DB::table('solicitudes_sep')
                        ->where('fecha_lote', $lote)
                        ->where('num_cta',$cuentas[$k])
                       ->update([
                                 'firma0' => $cadenas[$k],
                                 'status' => 3,
                        ]);
         }
         $msj = "Firma exitosa";
         Session::flash('info', $msj);
      }
      else {
         // Se regresa a un estado anterior los archivos de lotesUnam y Solcitudes_sep
         DB::table('lotes_unam')
                    ->where('fecha_lote', $lote)
                    ->update(['firma0' => false,
                              'fec_firma0'   => Carbon::now(),
                     ]);
         for ($k=0; $k < count($cadenas); $k++) {
            DB::table('solicitudes_sep')
                        ->where('fecha_lote', $lote)
                        ->where('num_cta',$cuentas[$k])
                       ->update([
                                 'firma0' => '',
                                 'status' => 2,
                        ]);
         }
         $msj = "Error: Inconsistencia en firmas enviadas y recibidas, lote -> ".$lote;
         Session::flash('error', $msj);
      }
      return $msj;

   }
   public function guardarDirectora($cuentas, $cadenas, $lote, $estado){
      if($estado){
         // Se ha realizado la firma 1
         DB::table('lotes_unam')
                    ->where('fecha_lote', $lote)
                    ->update(['firma1' => true,
                              'fec_firma1'   => Carbon::now(),
                     ]);
         // Se actualizan las firmas1 en la tabla de solicituds
         for ($k=0; $k < count($cadenas); $k++) {
            DB::table('solicitudes_sep')
                        ->where('fecha_lote', $lote)
                        ->where('num_cta',$cuentas[$k])
                       ->update([
                                 'firma1' => $cadenas[$k],
                                 'status' => 4,
                        ]);
         }
         $msj = "Firma exitosa";
         Session::flash('info', $msj);
      }
      else {
         // Se regresa a un estado anterior los archivos de lotesUnam y Solcitudes_sep
         DB::table('lotes_unam')
                    ->where('fecha_lote', $lote)
                    ->update(['firma1' => false,
                              'fec_firma1'   => Carbon::now(),
                     ]);
         for ($k=0; $k < count($cadenas); $k++) {
            DB::table('solicitudes_sep')
                        ->where('fecha_lote', $lote)
                        ->where('num_cta',$cuentas[$k])
                       ->update([
                                 'firma1' => '',
                                 'status' => 3,
                        ]);
         }
         $msj = "Error: Inconsistencia en firmas enviadas y recibidas, lote -> ".$lote;
         Session::flash('error', $msj);
      }
      return $msj;

   }
   public function guardarSecretario($cuentas, $cadenas, $lote, $estado){
      if($estado){
         DB::table('lotes_unam')
                    ->where('fecha_lote', $lote)
                    ->update(['firma2' => true,
                              'fec_firma2'   => Carbon::now(),
                     ]);
         // Se actualizan las firmas2 en la tabla de solicituds
         for ($k=0; $k < count($cadenas); $k++) {
            DB::table('solicitudes_sep')
                        ->where('fecha_lote', $lote)
                        ->where('num_cta',$cuentas[$k])
                        ->update([
                                 'firma2' => $cadenas[$k],
                                 'status' => 5,
                        ]);
         }
         $msj = "Firma exitosa";
         Session::flash('info', $msj);
      }
      else {
         // Se regresa a un estado anterior los archivos de lotesUnam y Solcitudes_sep
         DB::table('lotes_unam')
                    ->where('fecha_lote', $lote)
                    ->update(['firma2' => false,
                              'fec_firma2'   => Carbon::now(),
                     ]);
         for ($k=0; $k < count($cadenas); $k++) {
            DB::table('solicitudes_sep')
                        ->where('fecha_lote', $lote)
                        ->where('num_cta',$cuentas[$k])
                        ->update([
                                 'firma2' => '',
                                 'status' => 4,
                        ]);
         }
         $msj = "Error: Inconsistencia en firmas enviadas y recibidas, lote -> ".$lote;
         Session::flash('error', $msj);
      }
      return $msj;
   }
   public function guardarRector($cuentas, $cadenas, $lote, $estado){
      if($estado){
         DB::table('lotes_unam')
                    ->where('fecha_lote', $lote)
                    ->update(['firma3' => true,
                              'fec_firma3'   => Carbon::now(),
                     ]);
         // Se actualizan las firmas3 en la tabla de solicituds
         for ($k=0; $k < count($cadenas); $k++) {
            DB::table('solicitudes_sep')
                        ->where('fecha_lote', $lote)
                        ->where('num_cta',$cuentas[$k])
                       ->update([
                                 'firma3' => $cadenas[$k],
                                 'status' => 6,
                        ]);
         }
         $msj = "Firma exitosa";
         Session::flash('info', $msj);
      }
      else {
         // Se regresa a un estado anterior los archivos de lotesUnam y Solcitudes_sep
         DB::table('lotes_unam')
                    ->where('fecha_lote', $lote)
                    ->update(['firma3' => false,
                              'fec_firma3'   => Carbon::now(),
                     ]);
         for ($k=0; $k < count($cadenas); $k++) {
            DB::table('solicitudes_sep')
                        ->where('fecha_lote', $lote)
                        ->where('num_cta',$cuentas[$k])
                       ->update([
                                 'firma3' => '',
                                 'status' => 5,
                        ]);
         }
         $msj = "Error: Inconsistencia en firmas enviadas y recibidas, lote -> ".$lote;
         Session::flash('error', $msj);
      }
      return $msj;
   }
}
