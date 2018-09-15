<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SOAPClient;

class WSController extends Controller
{
   public function ws_RENAPO($curp)
    {
        try {
            $wsdl = 'http://webser.dgae.unam.mx:8280/services/ConsultaRenapoPorCurp?wsdl';
            // $wsdl = 'http://webser.dgae.unam.mx:8280/services/ConsultaRenapoPorDetalle?wsdl';
            http://webser.dgae.unam.mx:8280/services/ConsultaRenapoPorCurp?wsdl
            $opts = array(
                // 'proxy_host' => "132.248.205.70",
                // 'proxy_port' => 8280,
                // 'proxy_host' => "132.248.205.132",
                // 'proxy_port' => 9443,
                'location'=> 'https://webser.dgae.unam.mx:8243/services/ConsultaRenapoPorCurp.ConsultaRenapoPorCurpHttpsSoap12Endpoint',
                // 'location'=> 'https://webser.dgae.unam.mx:8243/services/ConsultaRenapoPorDetalle.ConsultaRenapoPorDetalleHttpsSoap12Endpoint',
                'connection_timeout' => 10 ,
                'encoding' => 'ISO-8859-1',
                'trace' => true,
                'exceptions' => true
             );
             // $curp = "PAEF890101HDFCSR07@|305016614@|PACHECO@|ESTRADA@|FERNANDO@|H@|01/01/1989@|DF@|@|@|@|@|@|@|@|@|@|@|@|@|@|@|@|";
             $curp = '';
            $client = new SOAPClient($wsdl, $opts);
            // dd($client);
            // dd($client->__getFunctions());
            // dd($client->__getTypes());
            $response = $client->consultarPorCurp('A');
            // $response = $client->consultarCurpDetalle(['cveAlfaEntFedNac' => 'DF', 'fechaNacimiento' => '01/01/1989', 'nombre' => 'FERNANDO', 'primerApellido' => 'PACHECO', 'segundoApellido' => 'ESTRADA', 'sexo' => 'H']);
            dd($response, $curp);

        }
        catch (SoapFault $exception) {

            echo "<pre>SoapFault: ".print_r($exception, true)."</pre>\n";
            echo "<pre>faultcode: '".$exception->faultcode."'</pre>";
            echo "<pre>faultstring: '".$exception->getMessage()."'</pre>";
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
        // $cta = '410060533'; // con causa 72
        // $cta = '308010769'; //Foto
        // $cta = '305016614'; //Fenando
        // $cta = '079332938'; //Guillermo
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
          'key' => $key,
          'cta' => $num_cta
        );

        try {
            if($nombre == 'trayectoria')
                $wsdl = 'https://www.dgae-siae.unam.mx/ws/soap/ssre_try_srv.php?wsdl';
            elseif ($nombre == 'identidad') {
                $wsdl = 'https://www.dgae-siae.unam.mx/ws/soap/dgae_idn_srv.php?wsdl';
          }
          $opts = array(
            'proxy_host' => "132.248.205.1",
            'proxy_port' => 8080,
            //'proxy_login' => 'el_login',
            //'proxy_password' => 'el_password',
            'connection_timeout' => 10 , // tiempo de espera
            'encoding' => 'ISO-8859-1',
            'trace' => true,
            'exceptions' => true
          );
          $client = new SOAPClient($wsdl, $opts);
          // dd($client->__getFunctions());
          if($nombre == 'trayectoria')
          {
              // dd($client);
              // dd($parametros, SHA1("He seguido la trayectoria en la que he creido y he confiado en mi mismo / Antonio Saura"));
              $response = $client->return_trayectoria($parametros);
          }
          elseif ($nombre ==  'identidad') {
              $response = $client->return_identidad($parametros);
          }
        }
        catch (SoapFault $exception) {

            echo "<pre>SoapFault: ".print_r($exception, true)."</pre>\n";
            echo "<pre>faultcode: '".$exception->faultcode."'</pre>";
            echo "<pre>faultstring: '".$exception->getMessage()."'</pre>";
        }
        if(empty($response->cuenta))
        {
            return $response->mensaje;
        }
        return $response;
    }
    public function ws_DGIRE($num_cta)
    {
        try {
            $wsdl = 'http://webser.dgae.unam.mx:8280/services/ConsultaDgire?wsdl';
            $opts = array(
                'proxy_host' => "132.248.205.1",
                'proxy_port' => 8080,
                'location'=> 'http://webser.dgae.unam.mx:8280/services/ConsultaDgire.ConsultaDgireHttpSoap12Endpoint',
                'connection_timeout' => 10 ,
                'encoding' => 'ISO-8859-1',
                'trace' => true,
                'exceptions' => true
             );
            $client = new SOAPClient($wsdl, $opts);
            // dd($client->__getFunctions());
            // dd($client->__getTypes());
            $response = $client->consultaDatosAlumno(['numeroCuenta' => $num_cta]);
        }
        catch (SoapFault $exception) {

            echo "<pre>SoapFault: ".print_r($exception, true)."</pre>\n";
            echo "<pre>faultcode: '".$exception->faultcode."'</pre>";
            echo "<pre>faultstring: '".$exception->getMessage()."'</pre>";
        }
        return $response;
    }
}
