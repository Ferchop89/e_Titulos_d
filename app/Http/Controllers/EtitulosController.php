<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Spatie\ArrayToXml\ArrayToXml;
use \FluidXml\FluidXml;
use App\Models\Estudio;
use App\Models\Entidad;
use App\Models\Modo;
use Carbon\Carbon;

use App\Http\Traits\Consultas\SharePost;
use App\Http\Traits\Consultas\XmlCadenaErrores;

class EtitulosController extends Controller
{
   use XmlCadenaErrores;

   public function searchAlum()
   {
        return view('/menus/search_eTitulosXml');
   }
   public function postSearchAlum(Request $request)
   {
      // dd("algo");
        $request->validate([
            'num_cta' => 'required|numeric|digits:9'
        ],[
            'num_cta.required' => 'El campo es obligatorio',
            'num_cta.numeric' => 'El campo debe contener solo números',
            'num_cta.digits'  => 'El campo debe ser de 9 dígitos',
        ]);
        return redirect()->route('eSearchInfo', ['num_cta'=>$request->num_cta]);
    }
   public function showInfo($num_cta)
   {
      // Presentacion de Datos
      $cuenta = substr($num_cta, 0, 8);
      $verif = substr($num_cta, 8, 1);
      // $cuenta = '06401471'; $digito='3'; $carrera='0030581';
      // $cuenta = '50845104'; $digito='7'; $carrera='0965010';
      $cuenta = '98801868'; $digito='0'; $carrera='01206'; // 0123356
      $datos = $this->integraConsulta($cuenta,$digito,$carrera);
      // En esta seccion se consultan los sellos del registro de usuario.
      $sello1 = 'Sello 1'; $sello2 = 'Sello2'; $sello3 = 'Sello3';
      $nodos = $this->IntegraNodos($datos[0],$sello1,$sello2,$sello3);
      // Obtención de XML
      $toXml = $this->tituloXml($nodos);
      // Obtención de la cadena origianl
      $cadenaOriginal = $this->cadenaOriginal($nodos);
      // Obención de los Errores.
      $errores = (isset($datos[1])==null)? 'Sin errores': $datos[1] ;
      // verificación de invasion de fechas.
      dd($cadenaOriginal,$toXml->xml(),$errores);
    }
    // Generacion de la Cadena Original
}
