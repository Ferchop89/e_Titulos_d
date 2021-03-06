<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use SOAPClient;
use App\Exceptions\RenapoException;
use File;
use DB;

class WSController extends Controller
{
   public function ws_Dgp_Carga($fileEnvioDGP,$fecha_lote_id)
   {
      // WS para el envio de títulos electrónicos
      // Acceso WS de la DGP via php.
      $nombreArchivo=$fileEnvioDGP[0];
      // ws via java
      exec("java -jar jar/TitulosElectronicos.jar $nombreArchivo", $respuestaDgp);
      // Registro del numero de lote que devuelve la DGP
      $registro = $this->registraLoteDgp($respuestaDgp,$fecha_lote_id);
      return;
   }
   public function registraLoteDgp($respuestaDgp,$fecha_lote_id)
   {
      // dd($respuestaDgp,$fecha_lote_id);
      $registros = DB::table('lotes_dgp')->
               where('lote_unam_id',$fecha_lote_id)->
               update([
                     'lote_dgp'=>$respuestaDgp[0],
                     ]);
      // Actualizamos en la tabla solicitudes_sep el status a 5 "05.Enviado DGP"
      $registros = DB::connection('condoc_eti')->
               table('solicitudes_sep')->
               where('fecha_lote_id',$fecha_lote_id)->
               update([
                     'status'=>5,
                     ]);
      return;
   }
   public function ws_Dgp_Descarga($loteDgp)
   {
      // Descarga del archivo Zip Codificado en base64
      // que contiene la evaluación de los xml enviados
      $usuario = 'usuariomet.qa362';
      $contrasena = 'YfZqnDIw';
      try {
            $wsdl = 'https://metqa.siged.sep.gob.mx/met-ws/services/TitulosElectronicos.wsdl';
            $opts = array(
                'location'=> 'https://metqa.siged.sep.gob.mx:443/met-ws/services/',
                'connection_timeout' => 10 ,
                'encoding' => 'UTF-8',
                'trace' => true,
                'exceptions' => true
             );
            $client = new SOAPClient($wsdl, $opts);
            $parametros = [
               'numeroLote' => (string)$loteDgp,
               'autenticacion' => ['usuario' => $usuario,
                                    'password' => $contrasena]
            ];
            $response = $client->descargaTituloElectronico($parametros);
            return $response;
        }
        catch (SoapFault $exception) {

            echo "<pre>SoapFault: ".print_r($exception, true)."</pre>\n";
            echo "<pre>faultcode: '".$exception->faultcode."'</pre>";
            echo "<pre>faultstring: '".$exception->getMessage()."'</pre>";
        }
        dd('finaliza lotes');
        return $response;
   }
   public function ws_DGPback($fileEnvioDGP)
   {
      // header('Content-Type: text/plain; charset=UTF-8');
      // WS para el envio de títulos electrónicos
      $nombreArchivo = explode('/',$fileEnvioDGP[0])[1];
      $nombreArchivo = 'unam.zip';
      // $varArchivo = File::get($fileEnvioDGP[0]);
      $varArchivo = File::get('xml/unam.zip');
      $varArchivo64 = base64_encode((string)$varArchivo);
      // dd($nombreArchivo,$varArchivo64);
      $usuario = 'usuariomet.qa362';
      $contrasena = 'YfZqnDIw';
      // dd($varArchivo64,$nombreArchivo);

      exec("java -jar jar/TitulosElectronicos.jar $nombreArchivo $varArchivo64",$output);
      dd('salida:', $output);


      try {
            $wsdl = 'https://metqa.siged.sep.gob.mx/met-ws/services/TitulosElectronicos.wsdl';
            $opts = array(
                'location'=> 'https://metqa.siged.sep.gob.mx:443/met-ws/services/',
                'connection_timeout' => 10 ,
                'encoding' => 'UTF-8',
                'trace' => true,
                'exceptions' => true
             );
            $client = new SOAPClient($wsdl, $opts);
            // dd($client);
            // dd($client, $client->__getTypes());
            // dd($client->__getFunctions());
            $parametros = [
               'nombreArchivo' => (string)$nombreArchivo,
               'archivoBase64' => (string)$varArchivo64,
               'autenticacion' => ['usuario' => $usuario,
                                    'password' => $contrasena]
            ];
            // dd($client, $client->__getTypes());
            $response = $client->cargaTituloElectronico($parametros);
            dd($response);
            dd($nombreArchivo,$varArchivo64);
            $parametros2 = [
               'numeroLote' => 53827,
               'autenticacion' => ['usuario' => $usuario,
                                 'password' => $contrasena]
            ];
            // dd($parametros, json_encode($parametros), $response);
            $response2 = $client->descargaTituloElectronico($parametros2);
            dd(base64_encode($response2->titulosBase64));

            return $response->numeroLote;

        }
        catch (SoapFault $exception) {

            echo "<pre>SoapFault: ".print_r($exception, true)."</pre>\n";
            echo "<pre>faultcode: '".$exception->faultcode."'</pre>";
            echo "<pre>faultstring: '".$exception->getMessage()."'</pre>";
        }
        return $response;
   }

    public function ws_RENAPO($curp)
    {
       error_reporting(E_ALL);
       ini_set("display_errors", 1);
       ini_set('soap.wsdl_cache_enabled', '0');
       ini_set('soap.wsdl_cache_ttl', '0');
       ini_set("default_socket_timeout", 1);
       $wsdl = 'http://webser.dgae.unam.mx:8280/services/ConsultaRenapoPorCurp?wsdl';
       $opts = array(
          'location'=> 'https://webser.dgae.unam.mx:8243/services/ConsultaRenapoPorCurp.ConsultaRenapoPorCurpHttpsSoap12Endpoint',
          'connection_timeout' => 10 ,
          'encoding' => 'ISO-8859-1',
          'trace' => true,
          'exceptions' => false
       );
       try {
         $sxe = simplexml_load_string(file_get_contents($wsdl));
       } catch (\Exception $e) {
          throw new RenapoException($e->getMessage());
       }
       $client = new SOAPClient($wsdl, $opts);
       // dd($client->__getFunctions());
       dd($client, $client->__getTypes());
       $datos=[
          'datos' => [
          'cveCurp' => $curp,
          ]
       ];
       $response = $client->consultarPorCurp($datos);
       //dd($response);
       if(is_soap_fault($response)){
          if(isset($response->faultcode))
          {
            throw new RenapoException('');
             // Log::error("WS_RENAPO {$datos['datos']['cveCurp']} SOAP Faul: (faultcode: {$response->faultcode}, faultstring: {$response->faultstring})");
             // session(['errorWS' => 'Servicio de validación de CURP temporalmente fuera de servicio. Intente más tarde.']);
          }
       }
       return $response;
     }

    public function ws_RENAPO_original($curp)
    {
       error_reporting(E_ALL);
       ini_set("display_errors", 1);
       ini_set('soap.wsdl_cache_enabled', '0');
       ini_set('soap.wsdl_cache_ttl', '0');
       ini_set("default_socket_timeout", 1);
       $wsdl = 'http://webser.dgae.unam.mx:8280/services/ConsultaRenapoPorCurp?wsdl';
       $opts = array(
          'location'=> 'https://webser.dgae.unam.mx:8243/services/ConsultaRenapoPorCurp.ConsultaRenapoPorCurpHttpsSoap12Endpoint',
          'connection_timeout' => 10 ,
          'encoding' => 'ISO-8859-1',
          'trace' => true,
          'exceptions' => false
       );
       $client = new SOAPClient($wsdl, $opts);
       $datos=[
          'datos' => [
          'cveCurp' => $curp,
          ]
       ];
       $response = $client->consultarPorCurp($datos);
       if(is_soap_fault($response)){
          if(isset($response->faultcode))
          {
             Log::error("WS_RENAPO {$datos['datos']['cveCurp']} SOAP Faul: (faultcode: {$response->faultcode}, faultstring: {$response->faultstring})");
             session(['errorWS' => 'Servicio de validación de CURP temporalmente fuera de servicio. Intente más tarde.']);
          }
       }
       return $response;
     }

   public function ws_SIAE($nombre, $num_cta, $key)
   {
      error_reporting(E_ALL);
      ini_set("display_errors", 1);
      ini_set('soap.wsdl_cache_enabled', '0');
      ini_set('soap.wsdl_cache_ttl', '0');
      ini_set("default_socket_timeout", 5);

      //$key = SHA1('He seguido la trayectoria en la que he creido y he confiado en mi mismo / Antonio Saura');
      // parametros de entrada para SOAP
      // $cta=request('trayectoria');
      // $cta = '313335127'; // con causa 72
      // $cta = '410060533'; // con causa$num_cta 72
      // $cta = '308010769'; //Foto
      // $cta = '305016614'; //Fenando
      // $cta = '081581988'; // defuncion
      // $cta = '414045101'; // expulsion
      // $cta = 317241309; // suspension temporal
      // $cta = '097157782'; // con sede
      // $cta = '096229688'; // normalizada por vigencia
      // $cta = '081360558'; // asignatura en plan nuevo
      // $cta = '300337895'; // cambio de area

      $key = iconv("UTF-8//TRANSLIT", "WINDOWS-1252", $key);
      $key=SHA1($key);

      $parametros = array(
         'key' => (string)$key,
         'cta' => (string)$num_cta
      );
      if($nombre == 'trayectoria')
      {
         $wsdl = 'https://www.dgae-siae.unam.mx/ws/soap/ssre_try_srv.php?wsdl';
      }
         elseif ($nombre == 'identidad')
      {
         // $wsdl = 'https://www.dgae-siae.unam.mx/ws/soap/dgae_idn_srv.php?wsdl';
         $wsdl = 'wsdl/wsdl_SIAE.xml';
      }

      $opts = array(
         'proxy_host' => "132.248.205.1",
         'proxy_port' => "8080",
           //'proxy_login' => 'el_login',
           //'proxy_password' => 'el_password',
         'connection_timeout' => "10" , // tiempo de espera
         'encoding' => 'ISO-8859-1',
         'trace' => true,
         'exceptions' => true
      );
      // dd($wsdl);
      $client = new SOAPClient($wsdl, $opts);
      // dd($client, $parametros);
      try {

          // dd($client->__getFunctions());
          if($nombre == 'trayectoria')
          {
              $response = $client->return_trayectoria($parametros);
          }
          elseif ($nombre ==  'identidad') {
              // $response = $client->return_identidad($parametros);
              $response = $client->__soapCall("return_identidad", array($parametros));
          }
        }
        catch (SoapFault $exception) {
           dd("catch");
            // dd($exception, "alto");
            // echo "<pre>SoapFault: ".print_r($exception, true)."</pre>\n";
            // echo "<pre>faultcode: '".$exception->faultcode."'</pre>";
            // echo "<pre>faultstring: '".$exception->getMessage()."'</pre>";
            if(empty($response->cuenta))
            {
                return $response->mensaje;
            }
        }
//los integramos a un zip todos los archivos xml de un lote,  y luego los elimianmso del directorio
        return $response;
    }

    public function ws_DGIRE($num_cta)
    {//strlen(base64_decode($encoded_data));
        try {
            $wsdl = 'http://webser.dgae.unam.mx:8280/services/ConsultaDgire?wsdl';
            $opts = array(
                'proxy_host' => "132.248.205.1",
                'proxy_port' => 8080,
                'location'=> 'http://webser.dgae.unam.mx:8280/services/ConsultaDgire.ConsultaDgireHttpSoap12Endpoint',
                'connection_timeout' => 30 ,
                'encoding' => 'ISOstrlen(base64_decode($encoded_data));-8859-1',
                'trace' => 1,
                'exceptions' => 1
             );
            $client = new SOAPClient($wsdl, $opts);
            // dd($client->__getFunctions());
            // dd($client->__getArrayTypes());
            $response = $client->consultaDatosAlumno(['numeroCuenta' => $num_cta]);
        }
        catch (SoapFault $exception) {

            echo "<pre>SoapFault: ".print_r($exception, true)."</pre>\n";
            echo "<pre>faultcode: '".$exception->faultcode."'</pre>";
            echo "<pre>faultstring: '".$exception->getMessage()."'</pre>";
        }
        return $response;
    }
    public function ws_DGIRE2($num_cta)
    {
        try {
            $wsdl = 'https://extranet.dgire.unam.mx/ws/dgae/dgaeAlum.php?wsdl';
            $opts = array(
                'proxy_host' => "132.248.205.1",
                'proxy_port' => 8080,
                'location'=> 'https://extranet.dgire.unam.mx:443/ws/dgae/dgaeAlum.php',
                'connection_timeout' => 30 ,
                'encoding' => 'ISO-8859-1',
                'trace' => 1,
                'exceptions' => 1
             );
            $client = new SOAPClient($wsdl, $opts);
            // dd($client->__getFunctions());
            // dd($client->__getTypes  ());
            $response = $client->getAlumno($num_cta);
        }
        catch (SoapFault $exception) {

            echo "<pre>SoapFault: ".print_r($exception, true)."</pre>\n";
            echo "<pre>faultcode: '".$exception->faultcode."'</pre>";
            echo "<pre>faultstring: '".$exception->getMessage()."'</pre>";
        }
        return $response;
    }
}
