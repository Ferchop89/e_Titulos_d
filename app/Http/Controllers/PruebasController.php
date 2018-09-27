<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use DB;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\{SolicitudSep, Web_Service, AutTransInfo};
use App\Http\Controllers\Admin\WSController;
// Traits.
use App\Http\Traits\Consultas\XmlCadenaErrores;
use App\Http\Traits\Consultas\TitulosFechas;

class PruebasController extends Controller
{
   use TitulosFechas, XmlCadenaErrores;

   public function searchAlum()
   {
        return view('/menus/search_eTitulos');
   }
   public function postSearchAlum(Request $request)
   {
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
      $cuenta = substr($num_cta, 0, 8);
      $verif = substr($num_cta, 8, 1);
      $foto = $this->consultaFotos($cuenta);
      $identidad = $this->consultaDatos($cuenta, $verif);
      if($identidad == null)
      {
         $msj = "No se encontraron registros en Títulos, con el número de cuenta ".$num_cta;
         Session::flash('error', $msj);
      }
      $trayectorias = $this->consultaTitulos($cuenta, $verif);
      return view('/menus/search_eTitulosInfo', ['numCta' => $num_cta,'foto' => $foto, 'identidad' => $identidad, 'trayectorias' => $trayectorias]);
    }
   public function existRequest($num_cta, $nombre,$carrera, $nivel)
   {
      $solicitud = $this->consultaSolicitudSep($num_cta, $carrera);
      if($solicitud != false)
      {
         $msj = "Ya existe un registro del número de cuenta ".$num_cta." con la carrera ".$carrera;
         Session::flash('error', $msj);
      }
      else {
         $fecha_nac = $this->consultaFechaNac(substr($num_cta, 0, 8), substr($num_cta, 8, 1));
         //$pass = str_replace("-", "", $fecha_nac);
         $year = substr($fecha_nac, 0, 4);
         $month = substr($fecha_nac, 5, 2);
         $day = substr($fecha_nac, 8, 2);
         $pass = $day.$month.$year;
         //dd($pass);
         $curp = $this->consultaCURP(substr($num_cta, 0, 8), substr($num_cta, 8, 1));
         // dd($fecha_nac, $pass);
         $this->createSolicitudSep($num_cta, $nombre, $nivel, $carrera, Auth::id());

         $this->createUserLogin($num_cta, $pass, $nombre, $curp, $fecha_nac);
         $msj = "La solicitud con el número de cuenta ".$num_cta." y carrera ".$carrera." fue recibida";
         Session::flash('success', $msj);
      }
      return redirect()->route('eSearchInfo', ['numCta' => $num_cta]);
   }
   public function listaErr()
   {
      // Generacion de la lista de errores
      $lists = SolicitudSep::all();
      $total = count($lists);
      $listaErrores = array();
      // Para cada alumno, se revisa el arreglo de errores almacenados en cada registro
      foreach ($lists as $alumno) {
         $errores = unserialize($alumno->errores);
         // deserializamos los errores contenidos en cada campo
         foreach ($errores as $key => $error) {
            if (!array_key_exists($key,$listaErrores)) {
               $listaErrores[$key] = $error;
            }
         }
      }
      return $listaErrores;
   }
   public function actualiza()
   {
      $lists = SolicitudSep::all();
      $total = count($lists);
      $listaErrores = array();
      foreach ($lists as $key => $elemento)
      {
         $digito = substr($elemento->num_cta,8,1);
         $cuenta = substr($elemento->num_cta,0,8);
         $carrera = $elemento->cve_carrera;
         // $cuenta = '08140248';$carrera = '0025139'; $digito = '9';
         $datos = $this->integraConsulta($cuenta,$digito,$carrera);
         // En esta seccion se consultan los sellos del registro de usuario.
         $sello1 = 'Sello 1'; $sello2 = 'Sello2'; $sello3 = 'Sello3';
         $nodos = $this->IntegraNodos($datos[0],$sello1,$sello2,$sello3);
         // Obtención de XML
         // $toXml = $this->tituloXml($nodos);
         // Obtención de la cadena original
         // $cadenaOriginal = $this->cadenaOriginal($nodos);
         // Obtención de los Errores.
         if (isset($datos[1])==null) {
            $errores = 'sin errores';
            if (!in_array($errores,$listaErrores)) {
               $listaErrores[] = $value;
            }
         } else {
            $errores = serialize($datos[1]);
            foreach ($datos[1] as $value) {
               if (!in_array($value,$listaErrores)) {
                  $listaErrores[] = $value;
               }
            }
         }

         $errores = (isset($datos[1])==null)? 'Sin errores': serialize($datos[1]) ;
         // Consulta de la informacion
         $alumno = SolicitudSep::find($elemento->id);
         $alumno->datos = serialize($datos[0]);
         $alumno->errores = $errores;
         $alumno->save();
         // dd($cadenaOriginal,$toXml->xml(),$errores);
      }
      // sort($listaErrores);
      // dd($listaErrores);
      // // dd($lists);
      // $title = 'Solicitudes para Envio de Firma';
      // return view('menus/lista_solicitudes', compact('title','lists', 'total'));
   }
   public function showPendientes()
   {
      // $total = SolicitudSep::count();
      // $lists = SolicitudSep::paginate(10);
      // $this->actualiza()
      if (isset(request()->listaErrores)) {
         // parametros de filtrado
         $query = "SELECT * FROM solicitudes_sep  where ";
         foreach (request()->listaErrores as $key => $value) {
            $query .= "errores LIKE '%".$value."%' OR  ";
         }
         $query = substr($query,0,strlen($query)-5);
         $lists = DB::connection('condoc_eti')->select($query);
      } else {
         // sin parametro de filtrado.
         $lists = SolicitudSep::all();
      }
      $acordeon = $this->acordionTitulos3($lists);
      // total de registros
      $total = count($lists);
      $title = 'Solicitudes para Envio de Firma';
      // Lista de Errores
      $listaErrores = $this->listaErr();
      // dd($listaErrores);

      // return View::make('form')->with('targets',$targets)->with('target',$target);
      // return view('menus/lista_solicitudes')->with('targets',$targets)->with('target',$target);
      return view('menus/lista_solicitudesCopia', compact('title','lists', 'total','listaErrores','acordeon'));
   }
   public function acordionTitulosTable($data)
   {
      // dd($data[0]->num_cta);
     // Elaboracion del acordion con listas.
     $composite = "<div class='panel-group' id='accordion'>";

     $composite .= "<table class='table table-hover'>";
     $composite .= "         <thead class='thead-dark'>";
     $composite .= "             <tr>";
     $composite .= "                 <th class='center' scope='col'># Solicitud</th>";
     $composite .= "                 <th class='center' scope='col'>No. Cuenta</th>";
     $composite .= "                 <th class='center' scope='col'>Nombre</th>";
     $composite .= "                 <th class='center' scope='col'>Nivel</th>";
     $composite .= "                 <th class='center' scope='col'>Cve Carrera</th>";
     $composite .= "                 <th class='center' scope='col'>errores</th>";
     $composite .= "                 <th class='center' scope='col'>Acciones</th>";
     $composite .= "             </tr>";
     $composite .= "         </thead>";
     $composite .= "         <tbody>";

     for ($i=0; $i < count($data) ; $i++) {
      $x_list = $i + 1;

      $composite .=    "<div class='panel panel-default'>";
      $composite .=         "<div class='panel-heading'>";
      $composite .=            "<h4 class='panel-title'>";
      $composite .=              "<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."'>";

      $composite .=        "<tr>";
      $composite .=        "  <th scope='row'>".$data[$i]->id."</th>";
      $composite .=        "            <td>noCta</td>";
      $composite .=        "            <td>Nombre</td>";
      $composite .=        "            <td>Nivel</td>";
      $composite .=        "            <td>cveCarrera</td>";
      $composite .=        "            <td>lista de errores</td>";
      $composite .=        "            <td>acciones</td>";
      $composite .=        "</tr>";


      // $composite .=              "Cuenta ".$data[$i]->num_cta."; Nombre ".$data[$i]->nombre_completo."; errores ".count(unserialize($data[$i]->errores));
      $composite .=              "</a>";
      $composite .=            "</h4>";
      $composite .=         "</div>";
      // solo el primer listado se despliega, los demas se colapsan.
      $collapse   =       (count($data)==1)? 'in': '';
      $composite .=       "<div id='collapse".$x_list."' class='panel-collapse collapse ".$collapse."'>";
      $composite .=       "<div class='panel-body'>";
      $composite .=        "<div class='table-responsive'>";
      $composite .=         "<table class='table table-striped'>";
      $composite .=           "<thead>";
      $composite .=             "<tr>";
      $composite .=               "<th scope='col'>#</th>";
      $composite .=               "<th scope='col'><strong>Llave XML</strong></th>";
      $composite .=               "<th scope='col'><strong>Contendido</strong></th>";
      $composite .=               "<th scope='col'><strong>Observacion</strong></th>";
      $composite .=             "</tr>";
      $composite .=           "</thead>";
      $composite .=           "<tbody>";
      $regis = 0;
      foreach (unserialize($data[$i]->datos) as $key => $value) {
         $composite .=           "<tr>";
         $composite .=             "<th scope='row'>".($regis++)."</th>";
         $composite .=               "<td>".$key."</td>";
         $composite .=               "<td>".$value."</td>";
         $composite .=               "<td>"."Observacion"."</td>";
         $composite .=           "</tr>";
      }
      $composite .=            "</tbody>";
      $composite .=         "</table>";
      $composite .=        "</div>"; // cierra el table responsive
      $composite .=       "</div>"; // cierra el panel-body
      $composite .=      "</div>"; // cierra el collapse
      $composite .=     "</div>"; // cierra el panel-default
     }

     $composite .= "<div>"; // cierra el acordeon

     return $composite;
   }
   public function acordionTitulos3($data)
   {
      // dd($data[0]);
      $composite = "<div class='Heading'>";
      $composite .=  "<div class='Cell id'>";
      $composite .=     "<p># Solicitud</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell cta'>";
      $composite .=     "<p>No. Cuenta</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell name'>";
      $composite .=     "<p>Nombre</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell date'>";
      $composite .=     "<p>Fecha</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell book'>";
      $composite .=     "<p>Libro-Foja-Folio</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell level'>";
      $composite .=     "<p>Nivel</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell cve'>";
      $composite .=     "<p>Cve Carrera</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell nError'>";
      $composite .=     "<p>Errores</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell'>";
      $composite .=     "<p>Acciones</p>";
      $composite .=  "</div>";
      $composite .="</div>";
      for ($i=0; $i < count($data) ; $i++) {
         $x_list = $i + 1;
         $composite .=  "<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."'>";
         $composite .="<div class='Row'>";

         $composite .=    "<div class='Cell id right'>";
         $composite .=       "<p>".$data[$i]->id."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell cta'>";
         $composite .=       "<p>".$data[$i]->num_cta."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell name'>";
         $composite .=       "<p>".$data[$i]->nombre_completo."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell date'>";
         $composite .=       "<p>".Carbon::parse($data[$i]->fec_emision_tit)->format('d/m/Y')."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell book'>";
         $composite .=       "<p>".$data[$i]->libro."-".$data[$i]->foja."-".$data[$i]->folio."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell level'>";
         $composite .=       "<p>".$data[$i]->nivel."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell cve'>";
         $composite .=       "<p>".$data[$i]->cve_carrera."</p>";
         $composite .=    "</div>";

         // desSerializamos la lista de errores para convertirla en array
         $listaErrores = unserialize($data[$i]->errores);
         $composite .=    "<div class='Cell nError'>";
         $composite .=       "<p>".count($listaErrores)."</p>";
         $composite .=    "</div>";

         $composite .="</div>";
         $composite .=  "</a>";
         $composite .=    "<div class='Cell btns'>";
         $composite .=       "<p>Boton</p>";
         $composite .=    "</div>";
         // solo el primer listado se despliega, los demas se colapsan.
         $collapse   =       (count($data)==1)? 'in': '';
         $composite .=       "<div id='collapse".$x_list."' class='panel-collapse collapse ".$collapse."'>";
         $composite .=       "<div class='panel-body'>";
         $composite .=        "<div class='table-responsive'>";
         $composite .=         "<table class='table table-striped table-dark'>";
         $composite .=           "<thead>";
         $composite .=             "<tr>";
         $composite .=               "<th scope='col'>#</th>";
         $composite .=               "<th scope='col'><strong>Llave XML</strong></th>";
         $composite .=               "<th scope='col'><strong>Contendido</strong></th>";
         $composite .=               "<th scope='col'><strong>Observacion</strong></th>";
         $composite .=             "</tr>";
         $composite .=           "</thead>";
         $composite .=           "<tbody>";
         $regis = 0;
         // Creamos un arreglo de datos a partir del contenido del campo datos.
         $listaDatos = unserialize($data[$i]->datos);
         foreach ( $listaDatos as $key => $value) {
            $composite .=           "<tr>";
            $composite .=             "<th scope='row'>".($regis++)."</th>";
            $composite .=               "<td>".$key."</td>";
            $composite .=               "<td>".$value."</td>";
            // Determinamos si existe la llave en la lista de errores para desplegarlo como obsevacion
            $observa    = array_key_exists($key,$listaErrores)? $listaErrores[$key]: '';
            $composite .=               "<td>".$observa."</td>";
            $composite .=           "</tr>";
         }
         $composite .=            "</tbody>";
         $composite .=         "</table>";
         $composite .=        "</div>"; // cierra el table responsive
         $composite .=       "</div>"; // cierra el panel-body
         $composite .=      "</div>"; // cierra el collapse
         // $composite .=     "</div>"; // cierra el panel-default
      }
      return $composite;

   }
   public function acordionTitulos2($data)
   {
      $composite = "<div class='panel-group' id='accordion'>";
      $composite .=   "<table class='table'>";
      $composite .=      "<thead class='thead-dark'>";
      $composite .=         "<tr>";
      // $composite .=            "<th class='center' scope='col'># Solicitud</th>";
      // $composite .=            "<th class='center' scope='col'>No. Cuenta</th>";
      // $composite .=            "<th class='center' scope='col'>Nombre</th>";
      // $composite .=            "<th class='center' scope='col'>Nivel</th>";
      // $composite .=            "<th class='center' scope='col'>Cve Carrera</th>";
      // $composite .=            "<th class='center' scope='col'>errores</th>";
      $composite .=            "<th class='center' scope='col'>Acciones</th>";
      $composite .=         "</tr>";
      $composite .=      "</thead>";
      $composite .=      "<tbody class='thead-dark'>";
      for ($i=0; $i < count($data) ; $i++) {
         $x_list = $i + 1;
         $composite .=      "<tr>";
         $composite .=        "<div class='algo'>";
         $composite .=           "<div class='algo2'>";
         $composite .=              "<h4 class='panel-title'>";
         $composite .=                 "<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."'>";
         $composite .=                    "<th scope='row'>".$data[$i]->id."</th>";
         $composite .=                    "<td>".$data[$i]->num_cta."</td>";
         $composite .=                    "<td>".$data[$i]->nombre_completo."</td>";
         $composite .=                    "<td>".Carbon::parse($data[$i]->fec_emision_tit)->format('d/m/Y')."</td>";
         $composite .=                    "<td>".$data[$i]->libro."-".$data[$i]->foja."-".$data[$i]->folio."</td>";
         $composite .=                    "<td>".$data[$i]->nivel."</td>";
         $composite .=                    "<td>".$data[$i]->cve_carrera."</td>";

         $composite .=                    "<td>".$data[$i]->nivel."</td>";
         $composite .=                 "</a>";
         $composite .=              "</h4>";
         $composite .=           "</div>";
         $composite .=        "</div>";
         $composite .=      "</tr>";

      }
      $composite .=      "</tbody>";
      $composite .=   "</table>";
      $composite .= "</div>"; // cierra el acordeon
      return $composite;
   }
   public function acordionTitulos($data)
   {
      // dd($data[0]->num_cta);
     // Elaboracion del acordion con listas.
     $composite = "<div class='panel-group' id='accordion'>";
     $composite .=   "<table class='table'>";
     $composite .=      "<thead class='thead-dark'>";
     $composite .=         "<tr>";
     $composite .=            "<th class='center' scope='col'># Solicitud</th>";
     $composite .=            "<th class='center' scope='col'>No. Cuenta</th>";
     $composite .=            "<th class='center' scope='col'>Nombre</th>";
     $composite .=            "<th class='center' scope='col'>Nivel</th>";
     $composite .=            "<th class='center' scope='col'>Cve Carrera</th>";
     $composite .=            "<th class='center' scope='col'>errores</th>";
     $composite .=            "<th class='center' scope='col'>Acciones</th>";
     $composite .=         "</tr>";
     $composite .=      "</thead>";
     $composite .=      "<tbody class='thead-dark'>";

     for ($i=0; $i < 2 ; $i++) {
      $x_list = $i + 1;


      // $composite .=    "<div class='panel panel-default'>";
      $composite .=                    "<tr>";
      // $composite .=         "<div class='panel-heading'>";
      // $composite .=            "<h4 class='panel-title'>";
      $composite .=              "<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."'>";



      $composite .=                    "<th class='left' scope='col'>".$data[$i]->id."</th>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->num_cta."</th>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->nombre_completo."</th>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->nivel."</th>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->cve_carrera."</th>";

      // desSerializamos la lista de errores para convertirla en array
      $listaErrores = unserialize($data[$i]->errores);
      $composite .=                    "<th class='left' scope='col'>".count($listaErrores)."</th>";
      $composite .=                    "<th class='left' scope='col'>Acciones</th>";
      $composite .=                    "</tr>";
      // $composite .=                    "</thead>";


      // $composite .=              "Cuenta ".$data[$i]->num_cta."; Nombre ".$data[$i]->nombre_completo."; errores ".count(unserialize($data[$i]->errores));

      $composite .=              "</a>";
      // $composite .=            "</h4>";
      $composite .=         "</div>";
      // solo el primer listado se despliega, los demas se colapsan.
      $collapse   =       (count($data)==1)? 'in': '';
      $composite .=       "<div id='collapse".$x_list."' class='panel-collapse collapse ".$collapse."'>";
      $composite .=       "<div class='panel-body'>";
      $composite .=        "<div class='table-responsive'>";
      $composite .=         "<table class='table table-striped table-dark'>";
      $composite .=           "<thead>";
      $composite .=             "<tr>";
      $composite .=               "<th scope='col'>#</th>";
      $composite .=               "<th scope='col'><strong>Llave XML</strong></th>";
      $composite .=               "<th scope='col'><strong>Contendido</strong></th>";
      $composite .=               "<th scope='col'><strong>Observacion</strong></th>";
      $composite .=             "</tr>";
      $composite .=           "</thead>";
      $composite .=           "<tbody>";
      $regis = 0;
      // Creamos un arreglo de datos a partir del contenido del campo datos.
      $listaDatos = unserialize($data[$i]->datos);
      foreach ( $listaDatos as $key => $value) {
         $composite .=           "<tr>";
         $composite .=             "<th scope='row'>".($regis++)."</th>";
         $composite .=               "<td>".$key."</td>";
         $composite .=               "<td>".$value."</td>";
         // Determinamos si existe la llave en la lista de errores para desplegarlo como obsevacion
         $observa    = array_key_exists($key,$listaErrores)? $listaErrores[$key]: '';
         $composite .=               "<td>".$observa."</td>";
         $composite .=           "</tr>";
      }
      $composite .=            "</tbody>";
      $composite .=         "</table>";
      $composite .=        "</div>"; // cierra el table responsive
      $composite .=       "</div>"; // cierra el panel-body
      $composite .=      "</div>"; // cierra el collapse
      // $composite .=     "</div>"; // cierra el panel-default
     }
     $composite .=                 "</tbody>";
     $composite .=                 "</table>";
     $composite .= "</div>"; // cierra el acordeon

     return $composite;
   }
   public function searchAlumDate()
   {
        return view('/menus/search_eTitulosDate');
   }
   public function postSearchAlumDate(Request $request)
   {
        $request->validate([
            'fecha' => 'required'
        ],[
            'fecha.required' => 'El campo es obligatorio',
            // 'num_cta.numeric' => 'El campo debe contener solo números',
            // 'num_cta.digits'  => 'El campo debe ser de 9 dígitos',
        ]);
        return redirect()->route('eSearchInfoDate', ['fecha'=>$request->fecha]);
   }
   public function showInfoDate($fecha)
   {
      // dd($fecha);
      $datos = $this->consultaTitulosDate($fecha);
      // dd($datos);
      $registros=0;
      // $fechaView  = Carbon::createFromDate($fecha);
      $fechaView = Carbon::createFromFormat('Y-m-d', $fecha);
      foreach ($datos as $key => $value) {
         // dd($value);
         $fecha_nac = $this->consultaFechaNac(substr($value->num_cta, 0, 8), substr($value->num_cta, 8, 1));
         //$pass = str_replace("-", "", $fecha_nac);
         $year = substr($fecha_nac, 0, 4);
         $month = substr($fecha_nac, 5, 2);
         $day = substr($fecha_nac, 8, 2);
         $pass = $day.$month.$year;
         //dd($pass);
         $curp = $this->consultaCURP(substr($value->num_cta, 0, 8), substr($value->num_cta, 8, 1));
         // dd($fecha_nac, $pass);

         $this->createSolicitudSep($value->num_cta, $value->dat_nombre, $value->tit_nivel, $value->tit_plancarr, Auth::id());
         $registros++;
      }
      $msj = "Se solicitaron ".$registros." regitros con fecha ".$fechaView->format('d-m-Y');
      Session::flash('info', $msj);
      return view('/menus/search_eTitulosDate');
   }
}
