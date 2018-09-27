<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use DB;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\{SolicitudSep, Web_Service, AutTransInfo, LotesUnam};
use App\Http\Controllers\Admin\WSController;
// Traits.
use App\Http\Traits\Consultas\XmlCadenaErrores;
use App\Http\Traits\Consultas\TitulosFechas;
use App\Http\Traits\Consultas\LotesFirma;

class SolicitudTituloeController extends Controller
{
   use TitulosFechas, XmlCadenaErrores, LotesFirma;

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
         $lists = SolicitudSep::where('status', '=', null)->get();
         // dd($lists);
      }
      $acordeon = $this->acordionTitulosUpdate($lists);
      // $acordeon = $this->acordionTitulos($lists);
      // total de registros
      $total = count($lists);
      $title = 'Solicitudes para Envio de Firma';
      // Lista de Errores
      $listaErrores = $this->listaErr();

      return view('menus/lista_solicitudes', compact('title','lists', 'total','listaErrores','acordeon'));
   }
   public function infoCedula($cuenta,$carrera)
   {
      // Actualizamos en solicitudes-Sep la fecha de emision, de titulo, Libro, Folio. Foja.
      $this->actualizaFLFF($cuenta,$carrera);
      return redirect()->route('filtraCedula');
   }

   public function acordionTitulos($data)
   {
     // Elaboracion del acordion con listas.
     $composite = "<div class='panel-group' id='accordion'>";

     for ($i=0; $i < count($data) ; $i++) {
      $x_list = $i + 1;


      $composite .=    "<div class='panel panel-default'>";
      $composite .=         "<div class='panel-heading'>";
      $composite .=            "<h4 class='panel-title'>";
      $composite .=              "<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."'>";

      $composite .=                 "<table class='table'>";
      $composite .=                    "<thead class='thead-dark'>";
      $composite .=                    "<tr>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->id."</th>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->num_cta."</th>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->nombre_completo."</th>";
      $composite .=                    "<th class='left' scope='col'>".substr($data[$i]->fec_emision_tit,0,10)."</th>";
      $lifofo    = $data[$i]->libro."-".$data[$i]->foja."-".$data[$i]->folio ;
      $composite .=                    "<th class='left' scope='col'>".$lifofo."</th>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->nivel."</th>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->cve_carrera."</th>";
      // desSerializamos la lista de errores para convertirla en array
      $listaErrores = unserialize($data[$i]->errores);
      // Cuenta de errores, si existe la llave "sin errores", ponemos en cero la cuenta de errores
      $cuentaE = (array_key_exists('sin errores',$listaErrores))? 0: count($listaErrores);
      $composite .=                    "<th class='left' scope='col'>".$cuentaE."</th>";
      $composite .=                    "</tr>";
      $composite .=                    "</thead>";
      $composite .=                 "</table>";

      // $composite .=              "Cuenta ".$data[$i]->num_cta."; Nombre ".$data[$i]->nombre_completo."; errores ".count(unserialize($data[$i]->errores));

      $composite .=              "</a>";
      $composite .=            "</h4>";
      $composite .=         "</div>";
      // solo el primer listado se despliega, los demas se colapsan.
      $collapse   =       (count($data)==1)? 'in': '';
      $composite .=       "<div id='collapse".$x_list."' class='panel-collapse collapse ".$collapse."'>";
      $composite .=       "<div class='panel-body'>";
      $link       =       'infoCedula/'.$data[$i]->num_cta."/".$data[$i]->cve_carrera;
      // $composite .=       "</br><a href=$link>ActualizaLink</a></br></br>";
      $composite .=       "<form action='infoCedula/".$data[$i]->num_cta."/".$data[$i]->cve_carrera."'>";
      $composite .=       "<input type='submit' value='actualiza' />";
      $composite .=       "</form>";
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
      $composite .=     "</div>"; // cierra el panel-default
     }
     $composite .= "<div>"; // cierra el acordeon

     return $composite;
   }
   public function acordionTitulosUpdate($data)
   {
      // Elaboracion del acordion con listas.
     $composite = "<div class='panel-group' id='accordion'>";
     for ($i=0; $i < count($data) ; $i++) {
      $x_list = $i + 1;
      $composite .=    "<div class='panel panel-default'>";
      $composite .=         "<div class='panel-heading'>";
      $composite .=            "<h4 class='panel-title'>";
      $composite .=              "<input type='checkbox' name='check_list[]' value='".$data[$i]->id."'>";
      $composite .=              "<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."'>";

      $composite .=                 "<table class='table'>";
      $composite .=                    "<thead class='thead-dark'>";
      $composite .=                    "<tr>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->id."</th>";
      // $composite .= "<option value="{{$place->id}}" @foreach($job->places as $p) @if($place->id == $p->id)selected="selected"@endif @endforeach>{{$place->name}}</option>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->num_cta."</th>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->nombre_completo."</th>";
      $composite .=                    "<th class='left' scope='col'>".substr($data[$i]->fec_emision_tit,0,10)."</th>";
      $lifofo    = $data[$i]->libro."-".$data[$i]->foja."-".$data[$i]->folio ;
      $composite .=                    "<th class='left' scope='col'>".$lifofo."</th>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->nivel."</th>";
      $composite .=                    "<th class='left' scope='col'>".$data[$i]->cve_carrera."</th>";
      // desSerializamos la lista de errores para convertirla en array
      $listaErrores = unserialize($data[$i]->errores);
      // Cuenta de errores, si existe la llave "sin errores", ponemos en cero la cuenta de errores
      $cuentaE = (array_key_exists('sin errores',$listaErrores))? 0: count($listaErrores);
      $composite .=                    "<th class='left' scope='col'>".$cuentaE."</th>";
      $composite .=                    "</tr>";
      $composite .=                    "</thead>";
      $composite .=                 "</table>";

      // $composite .=              "Cuenta ".$data[$i]->num_cta."; Nombre ".$data[$i]->nombre_completo."; errores ".count(unserialize($data[$i]->errores));

      $composite .=              "</a>";
      $composite .=            "</h4>";
      $composite .=         "</div>";
      // solo el primer listado se despliega, los demas se colapsan.
      $collapse   =       (count($data)==1)? 'in': '';
      $composite .=       "<div id='collapse".$x_list."' class='panel-collapse collapse ".$collapse."'>";
      $composite .=       "<div class='panel-body'>";
      $link       =       'infoCedula/'.$data[$i]->num_cta."/".$data[$i]->cve_carrera;
      // $composite .=       "</br><a href=$link>ActualizaLink</a></br></br>";
      $composite .=       "<form action='infoCedula/".$data[$i]->num_cta."/".$data[$i]->cve_carrera."'>";
      $composite .=       "<input type='submit' value='actualiza' />";
      $composite .=       "</form>";
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
      $composite .=     "</div>"; // cierra el panel-default
   }
      $composite .= "<div>"; // cierra el acordeon
      // $composite .=       "<input type='submit' value='Enviar' />";



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
      $registros=0;
      // $fechaView  = Carbon::createFromDate($fecha);
      $fechaView = Carbon::createFromFormat('Y-m-d', $fecha);
      foreach ($datos as $key => $value) {
         $this->createSolicitudSep($value->num_cta, $value->dat_nombre,
                                   $value->tit_nivel,$value->tit_plancarr,
                                   trim($value->tit_libro),trim($value->tit_foja),trim($value->tit_folio),
                                   substr($value->tit_fec_emision_tit,0,10),
                                   Auth::id());
         $registros++;
      }
      $msj = "Se solicitaron ".$registros." registros con fecha ".$fechaView->format('d-m-Y');
      Session::flash('info', $msj);
      return view('/menus/search_eTitulosDate');
   }
   public function nameButton()
   {
      if(isset($_POST['check_list']))
      {
         if(isset($_POST['enviar']))
         {
            $date = Carbon::now();
            $date = $date->format('Y-m-d h:i:s');
            $this->enviarFirma($_POST['check_list'], $date);
            $msj = "Se enviaron ".count($_POST['check_list'])." registros.";
            Session::flash('info', $msj);
            return redirect()->route('solicitudesPendientes');
         }
         elseif (isset($_POST['actualizar'])) {
            $this->actualizaFLFFIds($_POST['check_list']);
            $msj = "Se actualizaron ".count($_POST['check_list'])." registros.";
            Session::flash('info', $msj);
            return redirect()->route('solicitudesPendientes');
         }
      }
      $msj = "No se selecciono ningún registro.";
      Session::flash('info', $msj);
      return redirect()->route('solicitudesPendientes');


   }

}
