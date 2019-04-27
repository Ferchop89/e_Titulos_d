<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use DB;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\{SolicitudSep, Web_Service, AutTransInfo, LotesUnam, SolicitudesCanceladas};
use App\Http\Controllers\Admin\WSController;
// Traits.
use App\Http\Traits\Consultas\XmlCadenaErrores;
use App\Http\Traits\Consultas\TitulosFechas;
use App\Http\Traits\Consultas\LotesFirma;

use App\Services\PayUService\Exception;

class SolicitudTituloeController extends Controller
{
   use TitulosFechas, XmlCadenaErrores, LotesFirma;

   public function loteCadena($fecha,$cargo)
   {
      // obtencion un lote de cadenas orignales firmadas por la Jtit, Directora, Secretario o RECTOR
      $datos = SolicitudSep::where('status', 2)->
                             where('fecha_lote',$fecha)->get();
      // El folio se forma por la fecha del loteCadena
      $folio = carbon::parse($fecha)->format('Ymdhis');
      $responsable = $cadenaResp = '';
      // Se recorren las cuentas de alumnos que no tienen errores
      foreach ($datos as $datosAlumno) {
         $responsable = $this->integraNodosUnam($folio,unserialize($datosAlumno->datos),$cargo);
         // generamos la cadena por alumno e integramos el lote
         $cadena = $this->cadenaOriginal($responsable,$cargo);
         $cadenaResp = $cadenaResp.'@_@'.$cadena;
      }
      $cadenaResp = substr($cadenaResp, 3,strlen($cadenaResp)-3);
      return $cadenaResp;
   }
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
      foreach ($trayectorias as $key => $value) {
         $trayectorias[$key]['tit_fec_emision_tit']=Carbon::parse($value['tit_fec_emision_tit'])->format('d/m/Y');
      }
      return view('/menus/search_eTitulosInfo',
                  ['numCta' => $num_cta,'foto' => $foto, 'identidad' => $identidad, 'trayectorias' => $trayectorias]);
    }
   public function existRequest($num_cta, $nombre, $carrera, $nivel)
   {
      $solicitud = $this->consultaSolicitudSep($num_cta, $carrera);
      if($solicitud != false)
      {
         $msj = "Ya existe un registro del número de cuenta ".$num_cta." con la carrera ".$carrera;
         Session::flash('error', $msj);
      }
      else {
         $curp = $this->consultaCURP(substr($num_cta, 0, 8), substr($num_cta, 8, 1));
         $sistema = $this->consultaSistema(substr($num_cta, 0, 8), substr($num_cta, 8, 1), $carrera, $nivel);
         // Traemos $libro, Fecha, Folio y Fojo
         $query  = "SELECT tit_libro, tit_foja, tit_folio, tit_fec_emision_tit FROM Titulos ";
         $query .= "WHERE tit_ncta='".substr($num_cta,0,8)."' AND tit_plancarr='".$carrera."'";
         $info   = (array)DB::connection('sybase')->select($query)[0];
         // Damos de alta una solicitud SEP
         $this->createSolicitudSep($num_cta, $nombre, $nivel, $carrera,
                                   trim($info['tit_libro']), trim($info['tit_foja']),
                                   trim($info['tit_folio']), $info['tit_fec_emision_tit'],
                                   $sistema,
                                   Auth::id());

         $msj = "La solicitud con el número de cuenta ".$num_cta." y carrera ".$carrera." fue recibida";
         Session::flash('success', $msj);
      }
      return redirect()->route('eSearchInfo', ['numCta' => $num_cta]);
   }
   public function listaErr($queryFecha)
   {
      // Generacion de la lista de errores condicionado por la fecha elegida y solo si no han sido enviados a firma
      $queryBase = "SELECT * FROM solicitudes_sep WHERE status=1 AND ";
      $query  = ($queryFecha=='')? $queryBase: $queryBase.$queryFecha;

      $lists = DB::connection('condoc_eti')->select($query);
      // $lists = SolicitudSep::where()->all();

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

      $listaErrores['_00_todos'] = '-- Todos los registros --';
      ksort($listaErrores);

      return $listaErrores;
   }
   public function showPendientes()
   {
      // Muestra la vista de solicitudes pendientes de enviar a firma.
      $queryFecha = '';
      // Se establecen parametros adicionales para usar como fecha de filtrado
      if (isset(request()->inicio_emision) && isset(request()->fin_emision)) {
        //dd(request()->inicio_emision, request()->fin_emision);
         // Se invierte la fecha porque el DatePicker la presente invertida
         $fecha_o = request()->inicio_emision;
         $fecha_of = request()->fin_emision;
         $ini_fecha_d = substr($fecha_o,0,2);$fecha_m=substr($fecha_o,3,2);$fecha_a=substr($fecha_o,6,4);
         $fin_fecha_d = substr($fecha_of,0,2);$fecha_mf=substr($fecha_of,3,2);$fecha_af=substr($fecha_of,6,4);

         $ini_fecha = Carbon::parse($fecha_a."/".$fecha_m."/".$ini_fecha_d)->format('Y-m-d');
         $fin_fecha = Carbon::parse($fecha_af."/".$fecha_mf."/".$fin_fecha_d)->format('Y-m-d');

         //para datepicker
         $f_inicio = Carbon::parse($fecha_a."/".$fecha_m."/".$ini_fecha_d)->format('d/m/Y');
         $f_fin = Carbon::parse($fecha_af."/".$fecha_mf."/".$fin_fecha_d)->format('d/m/Y');
      } else {
         // Como no se especifica la fecha se carga la fecha de emision de titulo mas proxima
         $fechaSolicitud = SolicitudSep::where('status','=',1)
                           ->where('fecha_lote_id', NULL)
                           ->orderBy('fec_emision_tit')
                           ->pluck('fec_emision_tit')
                           ->last();

         $ini_fecha =  Carbon::parse($fechaSolicitud)->format('Y-m-d');
         $fin_fecha =  Carbon::parse($fechaSolicitud)->format('Y-m-d');
         //para datepicker
         $f_inicio = Carbon::parse($fechaSolicitud)->format('d/m/Y');
         $f_fin = Carbon::parse($fechaSolicitud)->format('d/m/Y');
      }
      // Si existe fecha en el datepicker, la enviamos como parametro para elegir los errores de la fecha elegida
      $queryFecha = $this->queryPeriodo($ini_fecha, $fin_fecha);
      // dd($queryFecha);
      // dd($fecha);
      $listaErrores =  $this->listaErr($queryFecha);

      $query = ''; // query de condiciones multiples de errores en cédulas
      $queryBase = "SELECT * FROM solicitudes_sep WHERE (status = 1) ";
      // Preguntamos si se la elegido una opcion en el menu de inconsistencias
      if (isset(request()->eSelect)) {
         // Se encuentran elecciones en el arreglo

         if (in_array("_00_todos",request()->eSelect)){
            // La solicitud contiene el item --todos los registros--
            // Seleccionamos como unica opcion
            $seleccion = $listaErrores['_00_todos'];
         } else {
            // No tiene el item "todos", pero se seleccionaron varios errores.
            $query='';
            foreach (request()->eSelect as $key => $value) {
               $query .= "errores LIKE '%".$value."%' OR ";
            }
            // Retiramos el ultimo OR de la cadena $query
            $query = "AND (".substr($query,0,strlen($query)-4).") ";
            // Los valores seleccionados son los mismos que regresan en el request
            $seleccion = request()->eSelect;
         }
         // Consulta general a Solicitudes_sep y parametros de filtrado.
         // Si existe datepicker, agregamos la condicion (AND)
         $query = $queryBase.$query.' AND '.$queryFecha;
      } else {
         // No existe una elección previa, se entra por primera vez a la vista
         $seleccion = ['_00_todos'];
         $query  = $queryBase.' AND '.$queryFecha;

         // Se recalculan todos los errores
      }
      $lists  = DB::connection('condoc_eti')->select($query);
      $acordeon = $this->acordionTitulosUpdate($lists);
      // total de registros
      $total = count($lists);
      $title = 'Solicitudes de cédula profesional electrónica';
      return view('menus/lista_solicitudes',
            compact('title','lists', 'total','listaErrores','acordeon','seleccion', 'f_inicio', 'f_fin'));
   }
   public function queryFecha($fecha)
   {
      $fechaP = explode("/", $fecha);
      $query = " (YEAR(fec_emision_tit) = ".$fechaP[0]." AND ";
      $query .= " MONTH(fec_emision_tit) = ".$fechaP[1]." AND ";
      $query .= " DAY(fec_emision_tit) = ".$fechaP[2].")";
      return $query;

   }
   public function queryPeriodo($ini_fecha, $fin_fecha){
     $query = " (fec_emision_tit BETWEEN '".$ini_fecha."%' AND '".$fin_fecha."%')";
     return $query;
   }
   public function infoCedula($cuenta,$carrera)
   {
      // Actualizamos en solicitudes-Sep la fecha de emision, de titulo, Libro, Folio. Foja.
      $this->actualizaFLFF($cuenta,$carrera);
      return redirect()->route('filtraCedula');
   }
   public function acordionTitulosUpdate($data)
   {
      // Elaboracion del acordion con listas.
      $composite = "<div class='fila'>";
      $composite .=  "<div class='Heading'>";
      $composite .=     "<div class='Cell id'>";
      $composite .=        "<p># Solicitud</p>";
      $composite .=     "</div>";
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
      $composite .=  "<div class='Cell sistema'>";
      $composite .=     "<p>Sistema</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell nError center'>";
      $composite .=     "<p>Errores</p>";
      $composite .=  "</div>";
      $composite .="</div>";
      $composite .="<div class='Heading Cell actions center'>";
      $composite .= "<div class='switch demo3'>
                        <input type='checkbox' id='checkAll'>
                           <label><i></i></label>
                     </div>";
      // $composite .=  "<input type='checkbox' id='checkAll'>";
      // $composite .=     "<i id='checkAll' class='fa fa-check fa-2x' aria-hidden='true'></i>";
      $composite .="</div>";
            $composite .=   "</div>";
      for ($i=0; $i < count($data) ; $i++) {
         $x_list = $i + 1;
         $composite .= "<div class='fila'>";
         $composite .="<div class='accordion-a'>";
         $composite .=  "<a class = 'a-row element' data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."' onClick='hCalcClick(".$x_list.")'>";
         $composite .=     "<div class='Row'>";
         $composite .=        "<div class='Cell id right'>";
         $composite .=           "<p>".$data[$i]->id."</p>";
         $composite .=        "</div>";
         $composite .=        "<div class='Cell cta'>";
         $composite .=           "<p>".$data[$i]->num_cta."</p>";
         $composite .=        "</div>";
         $composite .=        "<div class='Cell name'>";
         $composite .=           "<p>".$data[$i]->nombre_completo."</p>";
         $composite .=        "</div>";
         $composite .=        "<div class='Cell date'>";
         $composite .=           "<p>".Carbon::parse($data[$i]->fec_emision_tit)->format('d/m/Y')."</p>";
         $composite .=        "</div>";
         $composite .=        "<div class='Cell book'>";
         $composite .=           "<p>".$data[$i]->libro."-".$data[$i]->foja."-".$data[$i]->folio."</p>";
         $composite .=        "</div>";
         $composite .=        "<div class='Cell level'>";
         $composite .=           "<p>".$data[$i]->nivel."</p>";
         $composite .=        "</div>";
         $composite .=        "<div class='Cell cve'>";
         $composite .=           "<p>".$data[$i]->cve_carrera."</p>";
         $composite .=        "</div>";
         $composite .=        "<div class='Cell sistema'>";
         $composite .=           "<p>".$data[$i]->sistema."</p>";
         $composite .=        "</div>";
         // desSerializamos la lista de errores para convertirla en array
         $listaErrores = unserialize($data[$i]->errores);
         $composite .=        "<div class='Cell nError center'>";
         // Si la lista de errores contiene el item "sin errores" entonces la cuenta se despliegan "0" errores.
         $composite .=           "<p>".(in_Array('Sin errores',$listaErrores)? 0 : count($listaErrores))."</p>";
         $composite .=        "</div>";
         $composite .=     "</div>";
         $composite .=  "</a>";
         $composite .="</div>";
         $composite .="<div class='Cell btns'>";
         $composite .=  "<input type='checkbox' name='check_list[]' value='".$data[$i]->id."'>";
         $composite .="</div>";
         $composite .=    "</div>";

         // solo el primer listado se despliega, los demas se colapsan.
         $collapse   =(count($data)==1)? 'in': '';
         $composite .="<div id='collapse".$x_list."' class='panel-collapse collapse ".$collapse."'>";
         $composite .=  "<div class='panel-body'>";
         $composite .=     "<div class='table-responsive'>";
         $composite .=        "<table class='table table-striped table-dark'>";
         $composite .=           "<thead>";
         $composite .=              "<tr>";
         $composite .=                 "<th scope='col'>#</th>";
         $composite .=                 "<th scope='col'><strong>Llave XML</strong></th>";
         $composite .=                 "<th scope='col'><strong>SEP</strong></th>";
         $composite .=                 "<th scope='col'><strong>UNAM</strong></th>";
         $composite .=                 "<th scope='col'><strong>Observación</strong></th>";
         $composite .=              "</tr>";
         $composite .=           "</thead>";
         $composite .=           "<tbody>";
         $regis = 0;
         // Creamos un arreglo de datos a partir del contenido del campo datos.

         // if ($data[$i]->num_cta=='099266406') {
         //    dd($data[$i]->datos);
         // }
         $listaDatos = unserialize($data[$i]->datos);
         $paridad = unserialize($data[$i]->paridad);
         foreach ( $listaDatos as $key => $value) {
            $composite .=           "<tr>";
            $composite .=              "<td>".($regis++)."</td>";
            $composite .=              "<td class='envio-sep'>".$key."</td>";
            $composite .=              "<td class='envio-sep'>".$value."</td>";
            // Actualizamos la informacion de clave carrera UNAM si existe clave SEP para la misma
            $datoUnam  = array_key_exists($key,$paridad)? $paridad[$key]: '';
            $composite .=              "<td>".$datoUnam."</td>";
            // Determinamos si existe la llave en la lista de errores para desplegarlo como obsevacion
            $observa    = array_key_exists($key,$listaErrores)? $listaErrores[$key]: '';
            $composite .=              "<td class='envio-sep'>".$observa."</td>";
            $composite .=           "</tr>";
         }
         $composite .=           "</tbody>";
         $composite .=        "</table>";
         $composite .=     "</div>"; // cierra el table responsive
         $composite .=  "</div>"; // cierra el panel-body
         $composite .="</div>"; // cierra el collapse
     }
     return $composite;
   }
   public function searchAlumDate()
   {
        return view('/menus/search_eTitulosDate');
   }
   public function postSearchAlumDate(Request $request)
   {
        $request->validate([
            'datepicker' => 'required'
        ],[
            'datepicker.required' => 'El campo es obligatorio',
            // 'num_cta.numeric' => 'El campo debe contener solo números',
            // 'num_cta.digits'  => 'El campo debe ser de 9 dígitos',
        ]);
        $mda = explode("/", $request->datepicker);
        $amd = $mda[2]."-".$mda[1]."-".$mda[0];
        $this->showInfoDate($amd);
        return redirect()->route('registroTitulos/buscar/fecha');

   }
   public function showInfoDate($fecha)
   {
      // dd($fecha);
      $datos = $this->consultaTitulosDate($fecha);
      $procesa = array(0,0,0);
      // $fechaView  = Carbon::createFromDate($fecha);
      $fechaView = Carbon::createFromFormat('Y-m-d', $fecha);
      foreach ($datos as $key => $value) {
         $act = $this->createSolicitudSep($value->num_cta, $value->dat_nombre,
                                   $value->tit_nivel,$value->tit_plancarr,
                                   trim($value->tit_libro),trim($value->tit_foja),trim($value->tit_folio),
                                   // substr($value->tit_fec_emision_tit,0,10),
                                   $value->tit_fec_emision_tit,
                                   $value->dat_sistema,
                                   Auth::id());
         $procesa[0] += $act[0];
         $procesa[1] += $act[1];
         $procesa[2] += $act[2];
      }
      $msj = "Se procesaron ".($procesa[0]+$procesa[1]+$procesa[2])." registros con fecha ".$fechaView->format('d-m-Y').":";
      if($procesa[0] > 0)
      {
         $msj .= "<p> ->".$procesa[0]." se dieron de alta </p>";
      }
      if($procesa[1] > 0)
      {
         $msj .= "<p> ->".$procesa[1]." se actualizaron</p>";
      }
      if($procesa[2] > 0)
      {
         $msj .= "<p> ->".$procesa[2]." no se actualizaron, debido a que ya pasaron al proceso de firma</p>";
      }
      Session::flash('info', $msj);
      // return view('/menus/search_eTitulosDate');
   }
   public function nameButton()
   {
      if(isset($_POST['check_list']))
      {
         if(isset($_POST['enviar']))
         {
            $date = Carbon::now();
            $date = $date->format('Y-m-d h:i:s');
            $msj = $this->enviarFirma($_POST['check_list'], $date);
            return redirect()->route('registroTitulos/lista-solicitudes/pendientes');
         }
         elseif (isset($_POST['actualizar'])) {
            $this->actualizaFLFFIds($_POST['check_list']);
            $msj = "Se actualizaron ".count($_POST['check_list'])." registros.";
            Session::flash('info', $msj);
            return redirect()->route('registroTitulos/lista-solicitudes/pendientes');
         }
         elseif(isset($_POST['actualizar_WS'])){
           $this->actualizarXWS($_POST['check_list']);
           //$this->actualizaFLFFIds($_POST['check_list']);
           $msj = "Se actualizaron ".count($_POST['check_list'])." registros por WS.";
           Session::flash('info', $msj);
           return redirect()->route('registroTitulos/lista-solicitudes/pendientes');
         }
      }
      $msj = "No se selecciono ningún registro.";
      Session::flash('info', $msj);
      return redirect()->route('registroTitulos/lista-solicitudes/pendientes');


   }
   public function verTitulos()
   {
      return $this->titulosA(2000);
   }

   public function verTitulosSinFirma()
   {
     $mysql        = "DATE_FORMAT(fec_emision_tit,'%Y-%m-%d') as emisionYmd,";
     $mysql       .= "SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total ";
     $mysqlData    = DB::table('solicitudes_sep')
                   ->select(DB::raw($mysql))
                   ->where('fecha_lote_id', NULL)
                   ->groupBy('fec_emision_tit')
                   ->get();

     return $mysqlData;
   }

}
