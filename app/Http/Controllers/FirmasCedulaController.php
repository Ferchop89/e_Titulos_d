<?php

namespace App\Http\Controllers;
use \Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{LotesUnam, SolicitudSep};
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\Consultas\XmlCadenaErrores;
use App\Http\Traits\Consultas\TitulosFechas;
use Carbon\Carbon;
use Response;
use Session;
use DB;

class FirmasCedulaController extends Controller
{
   use XmlCadenaErrores, TitulosFechas;
  public function showFirmasP(){
    $title = "Lotes de Cédulas por Firmar";

    $rol = Auth::user()->roles()->get();
    $roles_us = array();
    foreach($rol as $actual){
      array_push($roles_us, $actual->nombre);
    }
    // dd($roles_us);
    $lists = LotesUnam::all();
    // dd($lists);
    $total = count($lists);
    $acordeon = $this->generaListasxLote($lists);
    return view('menus/lista_firmarSolicitudes', compact('title', 'lists', 'total', 'acordeon'));
  }
   public function authCurp(){
      $curp = '';
      $nombre = Auth::user()->username;
      switch ($nombre) {
         case 'Jtit':
            $curp = "UIES180831HDFSEP04";
            break;
         case 'Directora':
            $curp = "UIES180831HDFSEP03";
            break;
         case 'SecGral':
            $curp = "UIES180831HDFSEP02";
            break;
         case 'Rector':
            $curp = "UIES180831HDFSEP01";
            break;
      }
      return $curp;
   }
   public function generaListasxLote($data){
      // Elaboracion del acordion con listas.
      $curp = $this->authCurp();
      $url = "https://condoc.dgae.unam.mx/registroTitulos/response/firma";
      $composite = "<div class='firmas'>";
      for ($i=0; $i < count($data) ; $i++) {
      $x_list = $i + 1;
      $alumnos = $this->detalleLote($data[$i]->fecha_lote);
      $composite .= "<div class='accordion-a'>";
      $composite .=  "<a class = 'a-row' data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."'>";
      $composite .=     "<div class='Row'>";
      $composite .=        "<div class='Cell id right'>";
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
      // $composite .=  "<form action='/test/firmas/".$data[$i]->fechaLote."/Directora' method = 'GET'>";
      $composite .=  "<form action='https://enigma.unam.mx/componentefirma/initSigningProcess' method = 'POST'>";
      $composite .=     "<input type='hidden' name='_token' value='".csrf_token()."'>";
      $composite .=     "<input type='hidden' id='fechaLote' name='fecha_lote' value='".$data[$i]->fecha_lote."'>";
      $composite .=     "<input type='hidden' name='datos' value='".$this->loteCadena($data[$i]->fecha_lote, Auth::user()->name)."'>";
      $composite .=     "<input type='hidden' name='URL' value='".$url."'>";
      $composite .=     "<input type='hidden' name='curp' value='".$curp."'>";
      $composite .=     "<input type='submit' value='Firmar' id='btnFirma' class='btn'/>";
      $composite .=  "</form>";
      $composite .= "</div>";
      // solo el primer listado se despliega, los demas se colapsan.
      $collapse   =       (count($data)==1)? 'in': '';
      $composite .=       "<div id='collapse".$x_list."' class='panel-collapse collapse ".$collapse."'>";
      $composite .=       "<div class='panel-body'>";
      $composite .=        "<div class='table-responsive'>";
      $composite .=         "<table class='table table-striped table-dark'>";
      $composite .=           "<thead>";
      $composite .=             "<tr>";
      $composite .=               "<th scope='col'>#</th>";
      $composite .=               "<th scope='col'><strong># cuenta</strong></th>";
      $composite .=               "<th scope='col'><strong>Nombre completo</strong></th>";
      $composite .=               "<th scope='col'><strong>Clave carrera</strong></th>";
      $composite .=             "</tr>";
      $composite .=           "</thead>";
      $composite .=           "<tbody>";
      $regis = 1;
      foreach ( $alumnos as $key => $alumno) {
        $composite .=           "<tr>";
        $composite .=             "<th scope='row'>".($regis++)."</th>";
        $composite .=               "<td>".$alumno->num_cta."</td>";
        $composite .=               "<td>".$alumno->nombre_completo."</td>";
        $composite .=               "<td>".$alumno->cve_carrera."</td>";
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
  public function generaListas($data){
     // Elaboracion del acordion con listas.
     $curp = $this->authCurp();
     $curp = "UIES180831HDFSEP03";
     $url = "https://condoc.dgae.unam.mx/registroTitulos/response/firma";
   $composite = "<div class='panel-group' id='accordion'>";
   for ($i=0; $i < count($data) ; $i++) {
     $x_list = $i + 1;
     $alumnos = $this->detalleLote($data[$i]->fecha_lote);
     $composite .=    "<div class='panel panel-default'>";
     $composite .=         "<div class='panel-heading'>";
     // $composite .=       "<form action='/test/firmas/".$data[$i]->fechaLote."/Directora' method = 'GET'>";
     // $composite .=       "<form action='https://enigma.unam.mx/componentefirma/initSigningProcess' method = 'POST'>";
     $composite .=         "<input type='hidden' name='_token' value='".csrf_token()."'>";
     $composite .=         "<input type='hidden' id='fechaLote' name='fecha_lote' value='".$data[$i]->fecha_lote."'>";
     $composite .=         "<input type='hidden' name='datos' value='".$this->loteCadena($data[$i]->fecha_lote, Auth::user()->name)."'>";
     $composite .=         "<input type='hidden' name='URL' value='".$url."'>";
     $composite .=         "<input type='hidden' name='curp' value='".$curp."'>";
     $composite .=         "<input type='submit' value='Firmar' id='btnFirma' class='btn'/>";
     $composite .=       "</form>";
     $composite .=            "<h4 class='panel-title'>";
     $composite .=              "<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."'>";
     $composite .=                 "<table class='table'>";
     $composite .=                    "<thead class='thead-dark'>";
     $composite .=                    "<tr>";
     $composite .=                    "<th class='left' scope='col'>".$data[$i]->id."</th>";
     $composite .=                    "<th class='left' scope='col'>".$data[$i]->fecha_lote."</th>";
     $composite .=                    "<th class='left' scope='col'>".count($alumnos)."</th>";
     $composite .=                    "</tr>";
     $composite .=                    "</thead>";
     $composite .=                 "</table>";
     $composite .=              "</a>";
     $composite .=            "</h4>";
     $composite .=         "</div>";
     // solo el primer listado se despliega, los demas se colapsan.
     $collapse   =       (count($data)==1)? 'in': '';
     $composite .=       "<div id='collapse".$x_list."' class='panel-collapse collapse ".$collapse."'>";
     $composite .=       "<div class='panel-body'>";
     // $composite .=       "<form action='infoCedula/".$data[$i]->num_cta."/".$data[$i]->cve_carrera."'>";
     // $composite .=       "<input type='submit' value='actualiza' />";
     // $composite .=       "</form>";
     $composite .=        "<div class='table-responsive'>";
     $composite .=         "<table class='table table-striped table-dark'>";
     $composite .=           "<thead>";
     $composite .=             "<tr>";
     $composite .=               "<th scope='col'>#</th>";
     $composite .=               "<th scope='col'><strong># Cuenta</strong></th>";
     $composite .=               "<th scope='col'><strong>Nombre</strong></th>";
     $composite .=               "<th scope='col'><strong>Clave carrera</strong></th>";
     $composite .=             "</tr>";
     $composite .=           "</thead>";
     $composite .=           "<tbody>";
     $regis = 0;
     foreach ( $alumnos as $key => $alumno) {
       $composite .=           "<tr>";
       $composite .=             "<th scope='row'>".($regis++)."</th>";
       $composite .=               "<td>".$alumno->num_cta."</td>";
       $composite .=               "<td>".$alumno->nombre_completo."</td>";
       $composite .=               "<td>".$alumno->cve_carrera."</td>";
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

  public function detalleLote($lote){
     $datos = SolicitudSep::where('fecha_lote',$lote)->get();
     return $datos;
 }
 public function cantidadCedulas($fechaLote){
   $cantidad = SolicitudSep::where('fecha_lote',$fechaLote)->count();
   return $cantidad;
}

 public function detalleLoteN($lote, $nivel){
    $datos = SolicitudSep::where('fecha_lote',$lote)->where('nivel', $nivel)->get();
    return $datos;
  }

   public function lote_Session(){
      if(isset($_POST['fecha_lote']))
      {
         // dd($_POST);
         session(['lote' => $_POST['fecha_lote']]);
         return redirect()->to("https://enigma.unam.mx/componentefirma/initSigningProcess");
      //       ->withInput([
      //          'datos' => $_POST['datos'],
      //          'URL' => $_POST['URL'],
      //          'curp' => $_POST['curp']
      //       ]);
       }
   }

   public function showFirmasBusqueda()
   {
      $fecha = DB::table('solicitudes_sep')->where('status', '!=', 1)->orderBy('fec_emision_tit', 'desc')->first();
      $fechaO = $this->fechaxOmision();
      $lote = $lotes = $listaErrores = $acordeon = $libros = $fojas = $fojas_sel = null;
      $total = 0;
      $title = "Solicitudes enviadas a Firma";
      if(!empty($fecha))
      {
         $fecha_formato = Carbon::parse($fecha->fec_emision_tit)->format('d/m/Y');
         // $lote = DB::connection('condoc_eti')
         //          ->select('select * from solicitudes_sep WHERE fec_emision_tit = "'.$fecha->fec_emision_tit.'%" AND status != 1 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $lote = '';  // lote seleccionado en el menu
         $lotes = DB::connection('condoc_eti')
                  ->select("select distinct fecha_lote from solicitudes_sep WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fechaO' AND status != 1 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC");
         $solicitudes = DB::connection('condoc_eti')
                  ->select("select * from solicitudes_sep WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fechaO' AND status != 1 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC");
         $total = count($solicitudes);
         // $listaErrores = $this->listaErr();
         $title = $fecha_formato.' - Solicitudes enviadas a Firma';
         $acordeon = $this->acordionTitulosF($solicitudes);
         $libros = DB::connection('condoc_eti')
         ->select("select DISTINCT libro from solicitudes_sep where fec_emision_tit='$fecha->fec_emision_tit' order by ABS(libro) ASC");
         $fojas = DB::connection('condoc_eti')
         ->select("select DISTINCT foja from solicitudes_sep where fec_emision_tit='$fecha->fec_emision_tit' order by ABS(foja) ASC");
         $fojas_sel = array(); //Permitirá identificar las fojas por libro
         $foja = '';  // foja seleccionada en el menu
         foreach ($libros as $key=>$value) {
            $sql = DB::connection('condoc_eti')->select("select DISTINCT foja from solicitudes_sep WHERE libro = '".$libros[$key]->libro."' order by ABS(foja) ASC");
            foreach ($sql as $value) {
               array_push($fojas_sel, $libros[$key]->libro."-".$value->foja);
            }
         }
      }
      return view('menus/firmas_enviadas', compact('title','lote','lotes', 'total','acordeon', 'listaErrores', 'libros','fojas_sel','foja', 'fojas','fechaO'));
   }

   public function fechaxOmision()
   {
      // Fecha que nos da la ultima fecha de emisión de títulos solo con firma del departamento de
      $fecha = DB::table('solicitudes_sep')->where('status', '!=', 1)->orderBy('fec_emision_tit', 'desc')->first();
      return Carbon::parse($fecha->fec_emision_tit)->format('d/m/Y');
   }
   public function dataSolicitudes(Request $request)
   {
      // dd($request->all());
      // lote de solicitudes con parametros de fecha, lote y foja.
      $select = "select * from solicitudes_sep ";
      $fecha = $request->fecha;
      $where  = "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '".$fecha."' ";
      if ($request->lotes!=0) {
         $lote = $request->lotes;
         $where .= "AND fecha_lote = '$lote' ";
      }
      if ($request->foja!=0) {
         $foja = $request->foja;
         $where .= "AND foja ='$foja' ";
      }
      $where .= "AND status <> 1 ";
      $order  = "order by fecha_lote,libro, foja, folio";
      $query = $select.$where.$order;

      $lote = DB::connection('condoc_eti')
               ->select($query);
      return $lote;
   }

   public function postFirmasBusqueda(Request $request)
   {
      dd($request->all());
      // Consulta de Solicitudes.
      $foja = $request->foja;
      $lote = $request->lote;
      $fecha = $request->fecha;

      if ($request->foja==0) {
         //  Menu de lotes contiene los lotes que pertenecen a la fecha de emision de titulo.
         $queryLotes  = "select distinct fecha_lote from solicitudes_sep ";
         $queryLotes .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' AND status != 1 ";
         $queryLotes .= "order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC";
         // Menu de fojas contiene todas las fojas que pertenecen a la fecha de emisión de titulos
         $queryFojas  = "select DISTINCT foja from solicitudes_sep ";
         $queryFojas .= "WHERE fec_emision_tit='$fecha->fec_emision_tit' order by ABS(foja) ASC"
      }else{
         // Especificaron la foja
         if ($request->lote!=0) { // especificaron lote
            // Menú de lotes contiene los lotes que pertenecen a esta foja
            $queryLotes  = "select distinct fecha_lote from solicitudes_sep ";
            $queryLotes .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' AND ";
            $queryLotes .= "AND foja = '$foja' status != 1 ";
            $queryLotes .= "order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC";
         }else
         {  // Se especifica foja pero no lotes
            // Se regargan todos los lotes pero se deja apuntando al cero.
            // Se recargan todas las fojas pero se dejan apuntando

         }
         //  Se especifica la foja. El Menu de lotes debe contener los lotes asociados a la foja y la fechaEmisionTitulo
         $queryLotes  = "select distinct fecha_lote from solicitudes_sep ";
         $queryLotes .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' AND ";
         $queryLotes .= "fecla_lote = '$lote' status != 1 ";
         $queryLotes .= "order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC";
      }

      $lotes = DB::connection('condoc_eti')
               ->select($queryLotes);

      $foja = $request->foja;
      $fojas = DB::connection('condoc_eti')
      ->select("select DISTINCT foja from solicitudes_sep where fec_emision_tit='$fecha->fec_emision_tit' order by ABS(foja) ASC");

      $solicitudes = $this->dataSolicitudes($request);
      $title = $libros = '';
      $info = $request->all();
      //"fecha" => "08/11/2018" "lotes" => "2018-11-09 12:01:29" "foja" => "1078" "consultar" => "Solicitar"
      $total = count($solicitudes);
      $listaErrores = $this->listaErr();
      $acordeon = $this->acordionTitulosF($solicitudes);
      return view('menus/firmas_enviadas', compact('title','lote','lotes', 'total','acordeon', 'listaErrores', 'libros','fojas_sel','foja', 'fojas','fechaO'));
      // return view('menus/firmas_enviadas', compact('title','lote', 'total','acordeon', 'listaErrores', 'libros','foja','fojas', 'fojas_sel'));
   }

   public function postFirmasBusqueda_x(Request $request){
     $libros = DB::connection('condoc_eti')->select('select DISTINCT libro from solicitudes_sep order by ABS(libro) ASC');
     $fojas = DB::connection('condoc_eti')->select('select DISTINCT foja from solicitudes_sep order by ABS(foja) ASC');
     $fojas_sel = array(); //Permitirá identificar las fojas por libro
     foreach ($libros as $key=>$value) {
       $sql = DB::connection('condoc_eti')->select("select DISTINCT foja from solicitudes_sep WHERE libro = '".$libros[$key]->libro."' order by ABS(foja) ASC");
       foreach ($sql as $value) {
        array_push($fojas_sel, $libros[$key]->libro."-".$value->foja);
       }
     }

     if(isset($_POST['consultar'])) {
       $fecha = $_POST['fecha'];
       $libro = $_POST['libro'];
       $foja = $_POST['foja'];
       $lote_s = $_POST['lote_s'];
       //dd($fecha, $libro, $foja, $lote);

       if($fecha != "" && $libro != "0" && $foja == "0" && $lote_s == ""){ //Fecha y libro (pues el libro depende de la fecha)
         $date = date_create($fecha);
         $fecha_formato = date_format($date, 'd/m/Y');
         $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE
                 fec_emision_tit LIKE "'.$fecha.'%" AND libro LIKE "'.$libro.'" AND status != 1
                 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $title = $fecha_formato.' - Solicitudes enviadas a Firma';
       }elseif($fecha == "" && $libro != "0" && $foja == "0" && $lote_s == ""){ //Libro
         $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE
                 libro LIKE "'.$libro.'" AND status != 1
                 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $title = 'Libro: '.$libro.' - Solicitudes enviadas a Firma';
       }elseif($fecha == "" && $libro == "0" && $foja != "0" && $lote_s == ""){ //Foja
         $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE
                 foja LIKE "'.$foja.'" AND status != 1
                 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $title = 'Foja: '.$foja.' - Solicitudes enviadas a Firma';
       }elseif($fecha == "" && $libro == "0" && $foja == "0" && $lote_s != ""){ //Lote
         $date = date_create($lote_s);
         $fecha_formato = date_format($date, 'd/m/Y');
         $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE
                 fecha_lote LIKE "'.$lote_s.'%" AND status != 1
                 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $title = 'Lote: '.$fecha_formato.' - Solicitudes enviadas a Firma';
       }elseif($fecha != "" && $libro != "0" && $foja != "0" && $lote_s == ""){ //Fecha, libro y foja
         $date = date_create($fecha);
         $fecha_formato = date_format($date, 'd/m/Y');
         $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE
                 fec_emision_tit LIKE "'.$fecha.'%" AND libro LIKE "'.$libro.'" AND foja LIKE "'.$foja.'" AND status != 1
                 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $title = '['.$fecha_formato.' | '.$libro.' | '.$foja.'] - Solicitudes enviadas a Firma';
       }elseif($fecha != "" && $libro != "0" && $foja == "0" && $lote_s != ""){ //Fecha, libro y lote
         $date = date_create($fecha);
         $date_l = date_create($lote_s);
         $fecha_formato = date_format($date, 'd/m/Y');
         $fecha_formato_l = date_format($date_l, 'd/m/Y');
         $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE
                 fec_emision_tit LIKE "'.$fecha.'%" AND libro LIKE "'.$libro.'" AND fecha_lote LIKE "'.$lote_s.'%" AND status != 1
                 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $title = '['.$fecha_formato.' | '.$libro.' | '.$fecha_formato_l.'] - Solicitudes enviadas a Firma';
       }elseif($fecha == "" && $libro != "0" && $foja != "0" && $lote_s == ""){ //Libro y foja
         $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE
                 libro LIKE "'.$libro.'" AND foja LIKE "'.$foja.'" AND status != 1
                 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $title = '['.$libro.' | '.$foja.'] - Solicitudes enviadas a Firma';
       }elseif($fecha == "" && $libro != "0" && $foja == "0" && $lote_s != ""){ //Libro y lote
         $date = date_create($lote_s);
         $fecha_formato = date_format($date, 'd/m/Y');
         $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE
                 libro LIKE "'.$libro.'" AND fecha_lote LIKE "'.$lote_s.'%" AND status != 1
                 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $title = '['.$libro.' | '.$fecha_formato.'] - Solicitudes enviadas a Firma';
       }elseif($fecha == "" && $libro == "0" && $foja != "0" && $lote_s != ""){ //Foja y lote
         $date = date_create($lote_s);
         $fecha_formato = date_format($date, 'd/m/Y');
         $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE
                 foja LIKE "'.$foja.'" AND fecha_lote LIKE "'.$lote_s.'%" AND status != 1
                 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $title = '['.$foja.' | '.$fecha_formato.'] - Solicitudes enviadas a Firma';
       }elseif($fecha != "" && $libro != "0" && $foja != "0" && $lote_s != ""){ //TODOS
         $date = date_create($fecha);
         $date_l = date_create($lote_s);
         $fecha_formato = date_format($date, 'd/m/Y');
         $fecha_formato_l = date_format($date_l, 'd/m/Y');
         $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE
                 fec_emision_tit LIKE "'.$fecha.'%" AND libro LIKE "'.$libro.'" AND foja LIKE "'.$foja.'" AND fecha_lote LIKE "'.$lote_s.'%" AND status != 1
                 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $title = '['.$fecha_formato.' | '.$libro.' | '.$foja.' | '.$fecha_formato_l.'] - Solicitudes enviadas a Firma';
       }else{
         $fecha = DB::table('solicitudes_sep')->where('status', '!=', 1)->orderBy('fec_emision_tit', 'desc')->first();
         $fecha_formato = Carbon::parse($fecha->fec_emision_tit)->format('d/m/Y');
         $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE fec_emision_tit = "'.$fecha->fec_emision_tit.'%" AND status != 1 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
         $title = $fecha_formato.' - Solicitudes enviadas a Firma';
         $msj = "Debes seleccionar un filtro.";
         Session::flash('error', $msj);
       }

       $total = count($lote);
       $listaErrores = $this->listaErr();
       $acordeon = $this->acordionTitulosF($lote);
       return view('menus/firmas_enviadas', compact('title','lote', 'total','acordeon', 'listaErrores', 'libros', 'fojas', 'fojas_sel'));
     }
     // if(isset($_POST['consultar_fecha'])) {
     //   $request->validate([
     //       'fecha' => 'required'
     //   ],[
     //       'fecha.required' => 'Debes seleccionar una fecha',
     //   ]);
     //
     //   $fecha = $_POST['fecha'];
     //   $date = date_create($fecha);
     //   $fecha_formato = date_format($date, 'd/m/Y');
     //   $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE fec_emision_tit LIKE "'.$fecha.'%" AND status != 1 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
     //   $total = count($lote);
     //   $listaErrores = $this->listaErr();
     //   $title = $fecha_formato.' - Solicitudes enviadas a Firma';
     //   $acordeon = $this->acordionTitulosF($lote);
     //
     //   return view('menus/firmas_enviadas', compact('title','lote', 'total','acordeon', 'listaErrores'));
     // }elseif (isset($_POST['consultar_libro'])) {
     //   $request->validate([
     //       'libro' => 'required|numeric'
     //   ],[
     //       'libro.required' => 'Debes proporcionar un libro',
     //       'libro.numeric' => 'Deben ser solo dígitos'
     //   ]);
     //   $libro = $_POST['libro'];
     //   $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE libro = '.$libro.' AND status != 1 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
     //   $total = count($lote);
     //   $listaErrores = $this->listaErr();
     //   $title = 'Libro '.$libro.' - Solicitudes enviadas a Firma';
     //   $acordeon = $this->acordionTitulosF($lote);
     //
     //   return view('menus/firmas_enviadas', compact('title','lote', 'total','acordeon', 'listaErrores'));
     // }elseif (isset($_POST['consultar_foja'])) {
     //   $request->validate([
     //       'foja' => 'required|numeric'
     //   ],[
     //       'foja.required' => 'Debes proporcionar una foja',
     //       'foja.numeric' => 'Deben ser solo dígitos'
     //   ]);
     //   $foja = $_POST['foja'];
     //   $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE foja = '.$foja.' AND status != 1 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
     //   $total = count($lote);
     //   $listaErrores = $this->listaErr();
     //   $title = 'Foja '.$foja.' - Solicitudes enviadas a Firma';
     //   $acordeon = $this->acordionTitulosF($lote);
     //
     //   return view('menus/firmas_enviadas', compact('title','lote', 'total','acordeon', 'listaErrores'));
     // }elseif (isset($_POST['consultar_lote'])) {
     //   $request->validate([
     //       'lote_s' => 'required'listaErrores
     //   ],[
     //       'lote_s.required' => 'Debes seleccionar una fecha'
     //   ]);
     //   $lote_s = $_POST['lote_s'];
     //   $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE fecha_lote LIKE "'.$lote_s.'%" AND status != 1 order by ABS(libro) ASC, ABS(foja) ASC, ABS(folio) ASC');
     //   $total = count($lote);
     //   $listaErrores = $this->listaErr();
     //   $title = 'Lote '.$lote_s.' - Solicitudes enviadas a Firma';
     //   $acordeon = $this->acordionTitulosF($lote);
     //
     //   return view('menus/firmas_enviadas', compact('title','lote', 'total','acordeon', 'listaErrores'));
     // }
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

   public function acordionTitulosF($data)
   {
      $composite = "<div class='Heading'>";
      $composite .=  "<div class='Cell id'>";
      $composite .=     "<p># Solicitud</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell date'>";
      $composite .=     "<p>#Lote</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell cve'>";
      $composite .=     "<p>Fecha Lote</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell book'>";
      $composite .=     "<p>Libro-Foja-Folio</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell cta'>";
      $composite .=     "<p>No. Cuenta</p>";
      $composite .=  "</div>";
      $composite .=  "<div class='Cell name'>";
      $composite .=     "<p>Nombre</p>";
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
      $composite .="</div>";
      for ($i=0; $i < count($data) ; $i++) {
         $x_list = $i + 1;
         $composite .= "<div class='accordion-a'>";
         $composite .=  "<a class = 'a-row' data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."'>";
         $composite .="<div class='Row'>";
         $composite .=    "<div class='Cell id'>";
         $composite .=       "<p>".$data[$i]->id."</p>";
         $composite .=    "</div>";
         $fecha_lote =    LotesUnam::select('id')->where('fecha_lote',$data[$i]->fecha_lote)->first();
         $composite .=    "<div class='Cell date'>";
         $composite .=       "<p>".$fecha_lote->id."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell cve'>";
         $composite .=       "<p>".$data[$i]->fecha_lote."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell book'>";
         $composite .=       "<p>".$data[$i]->libro."-".$data[$i]->foja."-".$data[$i]->folio."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell cta'>";
         $composite .=       "<p>".$data[$i]->num_cta."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell name'>";
         $composite .=       "<p>".$data[$i]->nombre_completo."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell level'>";
         $composite .=       "<p>".$data[$i]->nivel."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell cve'>";
         $composite .=       "<p>".$data[$i]->cve_carrera."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell sistema'>";
         $composite .=       "<p>".$data[$i]->sistema."</p>";
         $composite .=    "</div>";
         //$composite .="</div>";
         //$composite .=  "</a>";
         //$composite .= "</div>";
         $listaErrores = unserialize($data[$i]->errores);

         $composite .="</div>";
         $composite .=  "</a>";
         $composite .= "</div>";
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
         $composite .=               "<th scope='col'><strong>UNAM</strong></th>";
         $composite .=               "<th scope='col'><strong>Observación</strong></th>";
         $composite .=             "</tr>";
         $composite .=           "</thead>";
         $composite .=           "<tbody>";
         $regis = 0;
         // Creamos un arreglo de datos a partir del contenido del campo datos.
         $listaDatos = unserialize($data[$i]->datos);
         $paridad = unserialize($data[$i]->paridad);
         foreach ( $listaDatos as $key => $value) {
            $composite .=           "<tr>";
            $composite .=             "<th scope='row'>".($regis++)."</th>";
            $composite .=               "<td class='envio-sep'>".$key."</td>";
            $composite .=               "<td class='envio-sep'>".$value."</td>";
            // Actualizamos la informacion de clave carrera UNAM si existe clave SEP para la misma
            $datoUnam  = array_key_exists($key,$paridad)? $paridad[$key]: '';
            $composite .=              "<td>".$datoUnam."</td>";
            // Determinamos si existe la llave en la lista de errores para desplegarlo como obsevacion
            $observa    = array_key_exists($key,$listaErrores)? $listaErrores[$key]: '';
            $composite .=               "<td class='envio-sep'>".$observa."</td>";
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

   public function showProgreso(){
      $totalF = array();
      $queryFecha = '';
      $title = "Progreso de Firmas";

      // Se establecen parametros adicionales para usar como fecha de filtrado
      if (isset(request()->datepicker)) {
         // Se invierte la fecha porque el DatePicker la presente invertida
         $fecha_o = request()->datepicker;
         $fecha_d = substr($fecha_o,0,2);
         $fecha_m = substr($fecha_o,3,2);
         $fecha_a = substr($fecha_o,6,4);
         $fecha = Carbon::parse($fecha_a."/".$fecha_m."/".$fecha_d)->format('Y/m/d');
      }
      else {
         $fecha = LotesUnam::all()->last()->fecha_lote;
         $fecha = Carbon::parse($fecha)->format('Y/m/d');
         $fecha_o = Carbon::parse($fecha)->format('d/m/Y');
      }
      $day = substr($fecha,8,2);;
      $month = substr($fecha,5,2);
      $year = substr($fecha,0,4);
      $queryFecha = "SELECT *, lotes_unam.id AS loteId FROM lotes_unam ";
      $queryFecha .= "JOIN solicitudes_sep ON lotes_unam.fecha_lote = solicitudes_sep.fecha_lote ";
      $queryFecha .= "WHERE (YEAR(lotes_unam.fecha_lote) = ".$year." AND MONTH(lotes_unam.fecha_lote) = ".$month." AND DAY(lotes_unam.fecha_lote) = ".$day.")";
      $queryFecha .= " AND solicitudes_sep.status <> 7 AND solicitudes_sep.fecha_lote IS NOT NULL GROUP BY solicitudes_sep.fecha_lote";
      $lists = DB::connection('condoc_eti')->select($queryFecha);
      $total = count($lists);
      // Contabilizacion de firmas
      foreach ($lists as $l) {
         if($l->firma0 && $l->firma1 && $l->firma2 && $l->firma3){
            array_push($totalF, ['1','1','1','1']);
         }elseif($l->firma0 && $l->firma1 && $l->firma2){
            array_push($totalF, ['1','1','1']);
         }elseif($l->firma0 && $l->firma1){
            array_push($totalF, ['1','1']);
         }elseif($l->firma0){
            array_push($totalF, ['1']);
         }else{
            array_push($totalF, null);
         }
      }
      $acordeon = $this->generaLotes($lists, $totalF);
      return view('menus/firmas_progreso', compact('title', 'total', 'acordeon', 'lists', 'fecha_o'));
   }

   // public function queryFecha($fecha)
   // {
   //    $fechaP = explode("/", $fecha);
   //    $query = " (YEAR(fecha_lote) = ".$fechaP[0]." AND ";
   //    $query .= " MONTH(fecha_lote) = ".$fechaP[1]." AND ";
   //    $query .= " DAY(fecha_lote) = ".$fechaP[2].")";
   //    return $query;
   //
   // }

   public function generaLotes($data, $totalF){
      // Elaboracion del acordion con listas.
      $composite = "<div class='firmas'>";
      for ($i=0; $i < count($data) ; $i++) {
      $x_list = $i + 1;
      $cedulas = $this->cantidadCedulas($data[$i]->fecha_lote);
      $composite .= "<div class='accordion-a'>";
      $composite .=     "<div class='Row'>";
      $composite .=        "<div class='Cell_mod id_f right_f'>";
      $composite .=           "<p> Lote: ".$data[$i]->loteId."</p>";
      $composite .=        "</div>";
      $composite .=        "<div class='Cell_mod fechaLote_f'>";
      $composite .=           "<p>";
      $composite .=              "Fecha de Lote: ";
      $composite .=              "<a href=";
      $var = $data[$i]->fecha_lote;
      $composite .=                 "'/registroTitulos/informacionDetallada/lote?fechaLote=$var'";
      $composite .=              ">".$data[$i]->fecha_lote."</a>";
      $composite .=           "</p>";
      $composite .=        "</div>";
      $composite .=        "<div class='Cell_mod numCedulaxLote center'>";
      $composite .=           "<p> Contiene: ".$cedulas." cédulas</p>";
      $composite .=        "</div>";
      $composite .=        "<div class='Cell_mod firmas_total'>";
      $composite .=           "<table style='width: 100%;'><tr>";
      $composite .=           "<td><p> Firmas: ".count($totalF[$i])."</p></td>";
         if(count($totalF[$i]) == 4)
         {
            $composite .=     "<td>
                                 <span class='fa fa-check-square-o f_dirt'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_dir'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_sec'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_rec'/>
                              </td>";
         }
         elseif(count($totalF[$i]) == 3)
         {
            $composite .=     "<td>
                                 <span class='fa fa-check-square-o f_dirt'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_dir'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_sec'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_rec oculto'/>
                              </td>";
      }
      elseif(count($totalF[$i]) == 2)
      {
         $composite .=        "<td>
                                 <span class='fa fa-check-square-o f_dirt'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_dir'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_sec oculto'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_rec oculto'/>
                              </td>";
      }
      elseif (count($totalF[$i]) == 1)
      {
         $composite .=        "<td>
                                 <span class='fa fa-check-square-o f_dirt'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_dir oculto'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_sec oculto'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_rec oculto'/>
                              </td>";
      }
      $composite .=           "</tr></table>";
      $composite .=        "</div>";
      $composite .=     "</div>";
      $composite .= "</div>";
      $composite .= "<div class='Cell btns'>";
      $composite .=  "<form action='envioSep' method = 'GET'>";
      // Evaluamos si el lote ya ha sido envioa a la SEP, donde status = 6 y cambiamos el texto del boton
      $enviado = SolicitudSep::where('fecha_lote',$data[$i]->fecha_lote)->first();
      $composite .=     "<input type='hidden' id='fechaLote' name='fecha_lote' value='".$data[$i]->fecha_lote."'>";
      if(count($totalF[$i]) == 4){
      // if(count($totalF[$i]) == 0){
         if ($enviado->status <= 6) {
            // if ($enviado->status == 2) {
            $composite .=     "<input type='submit' value='Envío SEP' id='btnEnvioSep' class='btn btn-info'/>";
         } else {
            $composite .=     "<input type='submit' value='Enviado SEP' id='btnEnvioSep' class='btn btn-success' disabled/>";
         }
      }else{
            $composite .=     "<input type='submit' value='Envío SEP' id='btnEnvioSep' class='btn' disabled/>";
      }
      $composite .=  "</form>";
      $composite .= "</div>";
    }
    return $composite;
   }

   public function showFirmadas()
   {
     $rol = Auth::user()->roles()->get();
     $roles_us = array(); //Obtenemos los roles del usuario actual
     foreach($rol as $actual){
       array_push($roles_us, $actual->nombre);
     }
     $fecha_formato = ''; $aux = ''; $lote = array();
     switch ($roles_us[0]) {
        case 'Director':
           $fecha = DB::table('lotes_unam')->where('firma1', '1')->orderBy('fec_firma1', 'desc')->first();
           if($fecha != null)
           {
             $fecha_formato = Carbon::parse($fecha->fec_firma1)->format('d/m/Y');
            $aux = Carbon::parse($fecha->fec_firma1)->format('Y-m-d');
            $lote = DB::connection('condoc_eti')
                   ->select('select * from lotes_unam WHERE firma1 LIKE "1" AND fecha_lote LIKE "'.$aux.'%" order by id');
           }

           break;
        case 'SecGral':
            $fecha = DB::table('lotes_unam')->where('firma2', '1')->orderBy('fec_firma2', 'desc')->first();
              if($fecha != null)
              {

                 $fecha_formato = Carbon::parse($fecha->fec_firma2)->format('d/m/Y');
                 $aux = Carbon::parse($fecha->fec_firma2)->format('Y-m-d');
                 $lote = DB::connection('condoc_eti')
                        ->select('select * from lotes_unam WHERE firma2 LIKE "1" AND fecha_lote LIKE "'.$aux.'%" order by id');
              }
           break;
        case 'Rector':
            $fecha = DB::table('lotes_unam')->where('firma2', '1')->orderBy('fec_firma2', 'desc')->first();
           if($fecha != null)
           {
              $fecha = DB::table('lotes_unam')->where('firma3', '1')->orderBy('fec_firma3', 'desc')->first();
             $fecha_formato = Carbon::parse($fecha->fec_firma3)->format('d/m/Y');
             $aux = Carbon::parse($fecha->fec_firma3)->format('Y-m-d');
             $lote = DB::connection('condoc_eti')
                    ->select('select * from lotes_unam WHERE firma3 LIKE "1" AND fecha_lote LIKE "'.$aux.'%" order by id');
           }
           break;
     }

     $title = $fecha_formato.' - Solicitudes firmadas';
     $total = count($lote);
     $listaErrores = $this->listaErr();
     $acordeon = $this->generaListasxLoteSinF($lote, "Firmado");

     return view('menus/firmadas', compact('title','lote', 'total','acordeon', 'listaErrores'));
   }

   public function postFirmadas(Request $request){
       $request->validate([
           'fecha' => 'required'
       ],[
           'fecha.required' => 'Debes seleccionar una fecha',
       ]);

       $fecha = $_POST['fecha'];
       $date = date_create($fecha);
       $fecha_formato = date_format($date, 'd/m/Y');

       $rol = Auth::user()->roles()->get();
       $roles_us = array(); //Obtenemos los roles del usuario actual
       foreach($rol as $actual){
         array_push($roles_us, $actual->nombre);
       }
       switch ($roles_us[0]) {
          case 'Director':
             $lote = DB::connection('condoc_eti')
                    ->select('select * from lotes_unam WHERE firma1 LIKE "1" AND fecha_lote LIKE "'.$fecha.'%" order by id');
             break;
          case 'SecGral':
             $lote = DB::connection('condoc_eti')
                      ->select('select * from lotes_unam WHERE firma2 LIKE "1" AND fecha_lote LIKE "'.$fecha.'%" order by id');
             break;
          case 'Rector':
             $lote = DB::connection('condoc_eti')
                      ->select('select * from lotes_unam WHERE firma3 LIKE "1" AND fecha_lote LIKE "'.$fecha.'%" order by id');
             break;
       }

       $total = count($lote);
       $listaErrores = $this->listaErr();
       $title = $fecha_formato.' - Solicitudes firmadas';
       $acordeon = $this->generaListasxLoteSinF($lote, "Firmado");

       return view('menus/firmadas', compact('title','lote', 'total','acordeon', 'listaErrores'));
     }

     public function generaListasxLoteSinF($data, $estado){
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
        $composite .=        "<div class='Cell id'>";
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
        $composite .= "<div class='Cell btns firmado'>";
        $composite .=   "<p> ".$estado." </p>";
        $composite .= "</div>";
        // solo el primer listado se despliega, los demas se colapsan.
        $collapse   =       (count($data)==1)? 'in': '';
        $composite .=       "<div id='collapse".$x_list."' class='panel-collapse collapse ".$collapse."'>";
        $composite .=       "<div class='panel-body'>";
        $composite .=        "<div class='table-responsive'>";
        $composite .=         "<table class='table table-striped table-dark'>";
        $composite .=           "<thead>";
        $composite .=             "<tr>";
        $composite .=               "<th scope='col'># solicitud</th>";
        $composite .=               "<th scope='col'><strong>No. cuenta</strong></th>";
        $composite .=               "<th scope='col'><strong>Nombre completo</strong></th>";
        $composite .=               "<th scope='col'><strong>Clave carrera</strong></th>";
        $composite .=               "<th scope='col'><strong>Nivel</strong></th>";
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
          $composite .=               "<td>".$alumno->nivel."</td>";
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

     public function showCedulasDGP(){
       $fecha = DB::table('solicitudes_sep')->where('status', 7)->orderBy('fecha_lote', 'desc')->first();
       if(isset($fecha)){
         $fecha_formato = Carbon::parse($fecha->fecha_lote)->format('d/m/Y');
         $aux = Carbon::parse($fecha->fecha_lote)->format('Y-m-d');
         $lote = DB::connection('condoc_eti')
                ->select('SELECT *
                          from condoc_etitulos.solicitudes_sep as ss JOIN condoc_etitulos.lotes_unam as lu ON ss.fecha_lote = lu.fecha_lote
                          where ss.status = 7 and ss.fecha_lote LIKE "'.$aux.'%" order by ss.id');
         $title = $fecha_formato.' | Todos - Cédulas enviadas a la DGP';
         $total = count($lote);
         $listaErrores = $this->listaErr();

         $niveles_con = (array)DB::connection('condoc_eti')->select('select * from _estudios');
         $foo = array('cat_subcve' => 'Todos');
         $foo = (object)$foo;
         $niveles[0] = $foo;
         foreach ($niveles_con as $nvl) {
           array_push($niveles, $nvl);
         }

         $acordeon = $this->acordionTitulosF($lote);

         return view('menus/cedulas_DGP', compact('title','lote', 'total','acordeon', 'listaErrores', 'niveles', 'aux'));
       }
       else{
         $title = 'Cédulas enviadas a la DGP';
         return view('menus/cedulas_DGP', compact('title'));
       }
     }

     public function postCedulasDGP(Request $request){
       if(isset($_POST['seleccion'])) {
         $request->validate([
             'nivel' => 'required',
             'fecha' => 'required'
         ],[
             'nivel.required' => 'Debes seleccionar un nivel',
             'fecha.required' => 'Debes seleccionar una fecha de lote'
         ]);

         $nivel = $_POST['nivel'];
         $fecha = $_POST['fecha'];
         $date = date_create($fecha);
         $fecha_formato = date_format($date, 'd/m/Y');
         $aux = Carbon::parse($fecha)->format('Y-m-d');
         if($nivel == "Todos"){
           $lote = DB::connection('condoc_eti')
                  ->select('SELECT *
                            from condoc_etitulos.solicitudes_sep as ss JOIN condoc_etitulos.lotes_unam as lu ON ss.fecha_lote = lu.fecha_lote
                            where ss.status = 7 and ss.fecha_lote LIKE "'.$aux.'%" order by ss.id');
         }else{
           $lote = DB::connection('condoc_eti')
                  ->select('SELECT *
                            from condoc_etitulos.solicitudes_sep as ss JOIN condoc_etitulos.lotes_unam as lu ON ss.fecha_lote = lu.fecha_lote
                            where ss.status = 7 and ss.fecha_lote LIKE "'.$aux.'%" AND nivel LIKE "'.$nivel.'" order by ss.id');
         }
         $title = $fecha_formato.' | '.$nivel.' - Cédulas enviadas a la DGP';
         $total = count($lote);
         $listaErrores = $this->listaErr();

         $niveles_con = (array)DB::connection('condoc_eti')->select('select * from _estudios');
         $foo = array('cat_subcve' => 'Todos');
         $foo = (object)$foo;
         $niveles[0] = $foo;
         foreach ($niveles_con as $nvl) {
           array_push($niveles, $nvl);
         }

         $acordeon = $this->acordionTitulosF($lote);

         return view('menus/cedulas_DGP', compact('title','lote', 'total','acordeon', 'listaErrores', 'niveles','aux'));
       }elseif(isset($_POST['impresion'])) {
         $request->validate([
             'fecha_env' => 'required'
         ],[
             'fecha_env.required' => 'Debes seleccionar una fecha de envío'
         ]);
         $fecha_env = $_POST['fecha_env'];
         $fecha_formato = Carbon::parse($fecha_env)->format('d/m/Y');
         $fecha_formato_c = Carbon::parse($fecha_env)->format('m/d/Y'); // mm dd yyyy
         $sig = date('m/d/Y', strtotime('+1 day', strtotime($fecha_formato_c))); //dia siguiente
         $data_prev = DB::connection('sybase')->select("select * from Titulos WHERE tit_fec_DGP >= '".$fecha_formato_c."' AND tit_fec_DGP < '".$sig."'");
         //dd(count($data_prev));
         if($data_prev == null){ //Si no existen solicitudes enviadas a DGP en esa fecha, se notifica
           $msj = "No existen solicitudes enviadas con fecha ".$fecha_formato;
           Session::flash('warning', $msj);
           return redirect()->route('registroTitulos/cedulas_DGP');
         }else{ //En caso contrario, se crea el listado
           $titulos = DB::connection('sybase')->select("select tit_ncta, tit_dig_ver from Titulos where tit_fec_DGP  >= '".$fecha_formato_c."' AND tit_fec_DGP < '".$sig."'");
           $arr_ncta = array();
           foreach ($titulos as $tit) {
             array_push($arr_ncta, $tit->tit_ncta.$tit->tit_dig_ver);
           }
           $data = array();
           foreach ($arr_ncta as $ncta) {
             $sql = DB::connection('condoc_eti')->select("select * from solicitudes_sep WHERE num_cta = '".$ncta."'");
             array_push($data, $sql);
           }
           //dd($data);
           $p = DB::connection('condoc_eti')->select("select * from solicitudes_sep WHERE nivel = '08'"); ///////////////// PRUEBA //////////////////////
           $vista = $this->generaPDF($p, $fecha_formato); ///////////////// PRUEBA ////////////////////// Cambiar $p por $data
           $view = \View::make('consultas.listasDGP', compact('vista'))->render();
           $pdf = \App::make('dompdf.wrapper');
           $pdf->loadHTML($view);
           return $pdf->stream('EnvíoDGP_'.str_replace('/','-',$fecha_formato).'.pdf');
         }
       }
     }

     public function generaPDF($data,$fecha)
     {

       $composite = "";
         $composite .= "<div class='container pdf_c'>";
         // $composite .= "<div class='test'>Impresión de prueba</div>";
         $composite .= "<div class='ati_pdf'>";
         $composite .= "<table class='tabla'>";
         $composite .= "<tr>";
         $composite .= "<td><div align='left'><img src='images/logo_unam.jpg' height='120' width='105'></div></td>";
         $composite .= "<td><div class='head_DGP' align='center'><h3>UNIVERSIDAD NACIONAL AUTONOMA DE MÉXICO.</h3>";
         $composite .= "<h3>Dirección General de Administración Escolar.</h3>";
         $composite .= "<h3>Departamento de Títulos.</h3>";
         $composite .= "<h3>Listado de Cédulas enviadas a DGP. Fecha:".str_replace('.','/',$fecha)."</h3></div></td>";
         $composite .= "</tr>";
         $composite .= "</table>";
         $composite .= "</div>";
         $composite .= "<table id='t01'>";
         $composite .= "<thead>";
         $composite .= "<tr>";
         $composite .= "<th scope='col'><strong>#</strong></th>";
         $composite .= "<th scope='col'><strong>NO. CTA.</strong></th>";
         $composite .= "<th scope='col'><strong>NOMBRE</strong></th>";
         $composite .= "<th scope='col'><strong>NIVEL</strong></th>";
         $composite .= "<th scope='col'><strong>CVE. CARR.</strong></th>";
         $composite .= "<th scope='col'><strong>FEC. EMI. TÍT.</strong></th>";
         $composite .= "</tr>";
         $composite .= "</thead>";
         $composite .= "<tbody>";
         for ($x=0; $x < count($data) ; $x++)
         {
             $composite .= "<tr>";
             $composite .= "<th>".($x+1)."</th>";
             $composite .= "<td>".$data[$x]->num_cta."</td>";
             $composite .= "<td>".$data[$x]->nombre_completo."</td>";
             $composite .= "<td>".$data[$x]->nivel."</td>";
             $composite .= "<td>".$data[$x]->cve_carrera."</td>";
             $composite .= "<td>".$data[$x]->fec_emision_tit."</td>";
             $composite .= "</tr>";
         }
         $composite .= "</tbody>";
         $composite .= "</table>";

      return $composite;

     }

}
