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
            $opts = array(
                'proxy_host' => "132.248.205.1",
                'proxy_port' => 8080,
                'location'=> 'https://webser.dgae.unam.mx:8243/services/ConsultaRenapoPorCurp.ConsultaRenapoPorCurpHttpsSoap12Endpoint',
                'connection_timeout' => 10 ,
                'encoding' => 'ISO-8859-1',
                'trace' => true,
                'exceptions' => true
             );
            $client = new SOAPClient($wsdl, $opts);
            // dd($client);
            // dd($client->__getFunctions());
            // dd($client->__getTypes());
            $response = $client->consultarPorCurp(['cveCurp' => $curp]);
        }
        catch (SoapFault $exception) {

            echo "<pre>SoapFault: ".print_r($exception, true)."</pre>\n";
            echo "<pre>faultcode: '".$exception->faultcode."'</pre>";
            echo "<pre>faultstring: '".$exception->getMessage()."'</pre>";
        }
        return $response;
    }
}
