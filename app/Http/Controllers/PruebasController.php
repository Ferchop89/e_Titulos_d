<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use DB;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\{SolicitudSep, Web_Service, AutTransInfo};
use App\Http\Controllers\Admin\WSController;
use App\Models\LotesUnam;
use App\Models\Carrera;
// Traits.
use App\Http\Traits\Consultas\XmlCadenaErrores;
use App\Http\Traits\Consultas\TitulosFechas;

class PruebasController extends Controller
{
   use TitulosFechas, XmlCadenaErrores;

   public function carreras(){
//       $carreras = ['01057', '50922', '01053', '01054', '01055', '40437', '01056', '01336', '00642', '01061',
//                   '00445', '00637', '00638', '01912', '0123470', '1003171', '0093103', '1003165', '0163100', '0123429', '0063200',
//                   '0113157', '3003020', '0083077', '0073143', '0083224', '0093105', '0013180', '0143002', '0093109', '0123447',
// '0093112',
// '0093110',
// '2000344',
// '0113072',
// '0093107',
// '0113156',
// '0123443',
// '0113158',
// '3003021',
// '0083220',
// '0083164',
// '0043191',
// '0143070',
// '0083218',
// '0113159',
// '0093113',
// '2000346',
// '0063196',
// '2000341',
// '0063085',
// '0083222',
// '0803116',
// '0043192',
// '0043193',
// '0143167',
// '0083226',
// '0113161',
// '0143166',
// '0093190',
// '0113155',
// '0093189',
// '0010346',
// '0093108',
// '0123467',
// '0093003',
// '0123406',
// '0113154',
// '0123448',
// '0013179',
// '0093104',
// '0010347',
// '0073133',
// '0104161',
// '0724087',
// '2004146',
// '0044115',
// '0064073',
// '0964087',
// '0124096',
// '3004085',
// '0064189',
// '1004169',
// '0044113',
// '0744087',
// '0674194',
// '3004178',
// '0664109',
// '0754102',
// '2004042',
// '0794100',
// '4004025',
// '0594153',
// '0064193',
// '0754101',
// '0124085',
// '0074146',
// '4004146',
// '0164169',
// '4004149',
// '0924078',
// '0694172',
// '0194173',
// '0744085',
// '0054154',
// '6204102',
// '0024137',
// '0164170',
// '0974085',
// '2004117',
// '0104081',
// '0104149',
// '0064190',
// '0754103',
// '0044116',
// '0134130',
// '0024125',
// '0104089',
// '0104110',
// '0514087',
// '0044114',
// '0104145',
// '0904104',
// '6214171',
// '0034085',
// '2004149',
// '0104195',
// '0104144',
// '0054109',
// '0134135',
// '0905104',
// '0805090',
// '0075146',
// '0015092',
// '0785143',
// '3005088',
// '0105145',
// '0645142',
// '0105110',
// '0125093',
// '0755103',
// '0055112',
// '0725087',
// '0695143',
// '0045117',
// '0675121',
// '0125143',
// '0165071',
// '0715143',
// '0085123',
// '5005088',
// '0745087',
// '0975085',
// '0055109',
// '0145095',
// '0105148',
// '0755102',
// ];
$carreras = [
   '70321',
'70322',
'71024',
'20412',
'0093105',
'0093003',
'0104120',
'0144095',
'6204102',
'0754102',
'0064192',
'0134133',
'0014198',
'0905104',
'0695160',
'0145095',
'0015111',
'0795123',
'0105089',
'0645142',
'0595143',
'0025139',
'1005071',
'0105091',
'0755102',
'0655085',
'0035085',
'0125085',
'0785143',
'0695085',
'3005085',
];
   foreach ($carreras as $key => $value) {
         $nombreCarrera = $this->nombreCarrera($value);
         echo "<pre>";
         print_r($nombreCarrera[0]->nombreCarrera);
         echo "</pre>";
      }

      // dd($nombreCarrera[0]->nombreCarrera);
   }
   public function showFirmasP(){
     $title = "Lotes de Cédulas por Firmar";

     $rol = Auth::user()->roles()->get();
     $rol = $rol[0]->nombre;
     switch ($rol) {
        case 'Jtit':
           $lists = $this->consultaTitulos();
           break;
        case 'Director':
           $lists = $this->consultaDirector();
           break;
        case 'SecGral':
           $lists = $this->consultaSecretario();
           break;
        case 'Rector':
           $lists = $this->consultaRector();
           break;
        default:
           // code...
           // dd("Permisos");
           break;
     }
     // Cuenta de lotes pendientes. No se usa count($lists) porque esta paginado.
     $total = $this->lotesPendientes($rol);
     $acordeon = $this->generaListasxLote($lists);
     return view('menus/lista_firmarSolicitudes', compact('title', 'lists', 'total', 'acordeon'));
   }
   public function consultaTitulos(){
      $lists = LotesUnam::select(['id','fecha_lote'])
            ->where('firma0',false)
            ->paginate(5);
      return $lists;
   }
   public function consultaDirector(){
      $lists = LotesUnam::select(['id','fecha_lote'])
            ->where('firma1',false)
            ->where('firma0', true)
            ->paginate(5);
      return $lists;
   }
   public function consultaSecretario(){
      $lists = LotesUnam::select(['id','fecha_lote'])
            ->where('firma2', false)
            ->where('firma1', true)
            ->paginate(5);
      return $lists;
   }
   public function consultaRector(){
      $lists = LotesUnam::select(['id','fecha_lote'])
            ->where('firma3', false)
            ->where('firma2', true)
            ->paginate(5);
      return $lists;
   }

   public function lotesPendientes($rol)
   {
      // Cuenta el numero de lotes pendientes de firma por el Rol.
      $where = "" ;
      switch ($rol) {
        case 'Jtit':
           $where = "firma0 = 0";
           break;
        case 'Director':
           $where = "firma0 = 1 AND firma1 = 0";
           break;
        case 'SecGral':
           $where = "firma1 = 1 AND firma2 = 0";
           break;
        case 'Rector':
           $where = "firma2 = 1 AND firma3 = 0";
           break;
        default:
           break;
     }
     $cuenta = LotesUnam::Select('id')
                           ->whereRaw($where)
                           ->count();
     return $cuenta;
   }

   public function generaListasxLote($data){
      // Elaboracion del acordion con listas.
      $curp = $this->authCurp();
      $composite = "<div class='firmas'>";
      for ($i=0; $i < count($data) ; $i++) {
      $x_list = $i + 1;
      $alumnos = $this->detalleLote($data[$i]->fecha_lote);
      // dd($alumnos[0]->num_cta);
      $cuentas = "";
      foreach ($alumnos as $key => $alumno) {
         $cuentas .= $alumno->num_cta."*";
      }
      $composite .= "<div class='accordion-a'>";
      $composite .=  "<a class = 'a-row' data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."'>";
      $composite .=     "<div class='Row'>";
      $composite .=        "<div class='Cell id '>";
      $composite .=           "<p> Lote: ".$data[$i]->id."</p>";
      $composite .=        "</div>";
      $composite .=        "<div class='Cell fechaLote'>";
      $composite .=           "<p> Fecha de Lote: ".$data[$i]->fecha_lote."</p>";
      $composite .=        "</div>";
      $composite .=        "<div class='Cell numCedulaxLote'>";
      $composite .=           "<p> Contiene: ".count($alumnos)." cédulas</p>";
      $composite .=        "</div>";
      $composite .=     "</div>";
      $composite .=  "</a>";
      $composite .= "</div>";
      $composite .= "<div class='Cell btns'>";
      $url = "https://condoc.dgae.unam.mx/registroTitulos/response/firma?lote=".$data[$i]->fecha_lote."&cuentas=".$cuentas;
      // $composite .=  "<form action='https://enigma.unam.mx/componentefirma/initSigningProcess' method = 'POST'>";
      $composite .=  "<form action='https://kryptos.unam.mx/componentefirma/initSigningProcess' method = 'POST'>";
      $composite .=     "<input type='hidden' name='_token' value='".csrf_token()."'>";
      $composite .=     "<input type='hidden' name='datos' value='".$this->loteCadena($data[$i]->fecha_lote, Auth::user()->roles()->first()->nombre)."'>";
      $composite .=     "<input type='hidden' name='URL' value='".$url."'>";
      $composite .=     "<input type='hidden' name='curp' value='".$curp."'>";
      $composite .=     "<input type='submit' value='Firmar' id='btnFirma' class='btn'/>";
      $composite .=  "</form>";
      $composite .= "</div>";
      $composite .=       "<div id='collapse".$x_list."' class='panel-collapse collapse'>";
      $composite .=       "<div class='panel-body'>";
      $composite .=        "<div class='table-responsive'>";
      $composite .=         "<table class='table table-striped table-dark'>";
      $composite .=           "<thead>";
      $composite .=             "<tr>";
      $composite .=               "<th scope='col'># solicitud</th>";
      $composite .=               "<th scope='col'><strong>No. cuenta</strong></th>";
      $composite .=               "<th scope='col'><strong>Nombre completo</strong></th>";
      $composite .=               "<th scope='col'><strong>Clave carrera</strong></th>";
      $composite .=               "<th scope='col'><strong>Nombre carrera</strong></th>";
      $composite .=               "<th scope='col'><strong>Nivel</strong></th>";
      $composite .=               "<th scope='col'><strong>Sistema</strong></th>";
      $composite .=             "</tr>";
      $composite .=           "</thead>";
      $composite .=           "<tbody>";
      $regis = 1;
      foreach ( $alumnos as $key => $alumno) {
        $composite .=           "<tr>";
        $composite .=             "<th scope='row'>".$alumno->id."</th>";
        $composite .=               "<td>".$alumno->num_cta."</td>";
        $composite .=               "<td>".$alumno->nombre_completo."</td>";
        $composite .=               "<td>".$alumno->cve_carrera."</td>";
        // Mostrar el nombre de la carrera SEP.
        $cveSep = unserialize($alumno->datos)['_09_cveCarrera'];
        $nomCarrera = Carrera::where('CVE_INSTITUCION','090001')
                              ->where('CVE_SEP',$cveSep)
                              ->first();
        $composite .=               "<td>".$nomCarrera->CARRERA."</td>";
        // $composite .=               "<td>".$this->carreraNombre($alumno->cve_carrera)."</td>";
        $composite .=               "<td>".$alumno->nivel."</td>";
        $composite .=               "<td>".$alumno->sistema."</td>";
        $composite .=           "</tr>";
      }
      $composite .=            "</tbody>";
      $composite .=         "</table>";
      $composite .=        "</div>"; // cierra el table responsive
      $composite .=       "</div>"; // cierra el panel-body
      $composite .=      "</div>"; // cierra el collapse
  }
    return $composite;
   }
   public function authCurp(){
      $curp = '';
      $rol = Auth::user()->roles()->first()->nombre;
      // $nombre = Auth::user()->username;
      switch ($rol) {
         case 'Jtit':
            // $curp = "UIES180831S04";
            $curp = "GOND701217HP2";
            break;
         case 'Director':
            // $curp = "UIES180831S03";
            $curp = "RAWI6005073U0";
            // $curp = "UIES180831S02";
            break;
         case 'SecGral':
         // $curp = "UIES180831S02";
         $curp = "LOVL7004289W7";
         // $curp = "UIES180831S03";
            break;
         case 'Rector':
            // $curp = "UIES180831S01";
            $curp = "GAWE510109C14";
            break;
      }
      return $curp;
   }

   public function detalleLote($lote){
      $datos = SolicitudSep::where('fecha_lote',$lote)->get();
      return $datos;
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
   }
   public function showPendientes()
   {
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
      return view('menus/lista_solicitudesCopia', compact('title','lists', 'total','listaErrores','acordeon'));
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
      $actualiza = 0;
      $act = 0;
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

         $act = $this->createSolicitudSep($value->num_cta, $value->dat_nombre, $value->tit_nivel, $value->tit_plancarr, Auth::id());
         dd($act);
         $registros++;
      }
      $msj = "Se solicitaron ".$registros." registros con fecha ".$fechaView->format('d-m-Y');
      Session::flash('info', $msj);
      return view('/menus/search_eTitulosDate');
   }
}
