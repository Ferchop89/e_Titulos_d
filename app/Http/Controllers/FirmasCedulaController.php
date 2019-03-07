<?php

namespace App\Http\Controllers;
use \Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{LotesUnam, SolicitudSep, Estudio};
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\Consultas\XmlCadenaErrores;
use App\Http\Traits\Consultas\TitulosFechas;
use Carbon\Carbon;
use DateTime;
use Response;
use Session;
use DB;

class FirmasCedulaController extends Controller
{
   use XmlCadenaErrores, TitulosFechas;
   public function showFirmasP()
   {
      $title = "Lotes de Cédulas por Firmar";

      $rol = Auth::user()->roles()->get();
      $roles_us = array();
      foreach($rol as $actual)
      {
         array_push($roles_us, $actual->nombre);
      }
      // dd($roles_us);
      $lists = LotesUnam::all();
      // dd($lists);
      $total = count($lists);
      $acordeon = $this->generaListasxLote($lists);
      return view('menus/lista_firmarSolicitudes', compact('title', 'lists', 'total', 'acordeon'));
   }

   public function authCurp()
   {
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

   public function generaListasxLote($data)
   {
      // Elaboracion del acordion con listas.
      $curp = $this->authCurp();
      $url = "https://condoc.dgae.unam.mx/registroTitulos/response/firma";
      $composite = "<div class='firmas'>";
      for ($i=0; $i < count($data) ; $i++)
      {
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
         foreach ( $alumnos as $key => $alumno)
         {
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
      }
      return $composite;
   }

   public function generaListas($data)
   {
      // Elaboracion del acordion con listas.
      $curp = $this->authCurp();
      $curp = "UIES180831HDFSEP03";
      $url = "https://condoc.dgae.unam.mx/registroTitulos/response/firma";
      $composite = "<div class='panel-group' id='accordion'>";
      for ($i=0; $i < count($data) ; $i++)
      {
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
        $composite .=
            "<th class='left' scope='col'>".$data[$i]->id."</th>";
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
      return $composite;
   }

   public function detalleLote($lote)
   {
     $datos = SolicitudSep::where('fecha_lote',$lote)->get();
     return $datos;
   }

   public function cantidadCedulas($fechaLote)
   {
      $cantidad = SolicitudSep::where('fecha_lote',$fechaLote)->count();
      return $cantidad;
   }

   public function detalleLoteN($lote, $nivel)
   {
       $datos = SolicitudSep::where('fecha_lote',$lote)->where('nivel', $nivel)->get();
       return $datos;
   }

   public function lote_Session()
   {
      if(isset($_POST['fecha_lote']))
      {
         session(['lote' => $_POST['fecha_lote']]);
         return redirect()->to("https://enigma.unam.mx/componentefirma/initSigningProcess");
       }
   }

   public function showFirmasBusqueda()
   {
      $fecha = DB::table('solicitudes_sep')
                     ->where('status', '!=', 1)
                     ->where('fecha_lote', '!=', NULL)
                     ->orderBy('fec_emision_tit', 'ASC')->first();
      // $fecha = DB::table('lotes_unam')->select('fecha_lote')->orderBy('fecha_lote', 'desc')->first();
      $lote = $lotes = $listaErrores = $acordeon = $libros = $fojas = $fojas_sel = null;
      $total = 0; $fechaOmision = '';
      if(!empty($fecha))
      {
         // $fecha_formato = Carbon::parse($fecha->fec_emision_tit)->format('d/m/Y');
         $fechaOmision = $fecha->fec_emision_tit;
         $fecha_formato = Carbon::parse($fechaOmision)->format('d/m/Y');
         $lote = '';  // lote seleccionado en el menu
         $queryLotes  = "SELECT DISTINCT fecha_lote_id AS loteId, DATE_FORMAT(fecha_lote,'%d-%m-%Y %h:%i:%s') AS lote ";
         $queryLotes .= "FROM solicitudes_sep ";
         $queryLotes .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha_formato' AND ";
         $queryLotes .= "fecha_lote_id <> '' ";
         $queryLotes .= "ORDER BY fecha_lote_id DESC,LPAD(foja,3,'0'),LPAD(folio,3,0)";
         $lotes = DB::connection('condoc_eti')
                        ->select($queryLotes);
         // Requerimiento de las solicitudes de la fecha de emisión del título.
         $querySol  = "SELECT id, fec_emision_tit, fecha_lote_id, libro, foja, folio, ";
         $querySol .= "num_cta, nombre_completo, nivel, cve_carrera, sistema, datos, ";
         $querySol .= "paridad, cat_nombre as nivel_nombre ";
         $querySol .= "FROM solicitudes_sep ";
         $querySol .= "JOIN _estudios ";
         $querySol .= "ON (nivel COLLATE utf8_unicode_ci)=(cat_subcve COLLATE utf8_unicode_ci) ";
         $querySol .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha_formato' AND ";
         $querySol .= "status != 1 ";
         $querySol .= "ORDER BY fecha_lote_id DESC, ";
         $querySol .= "CAST(libro AS UNSIGNED INTEGER) ASC, CAST(foja AS UNSIGNED INTEGER) ASC, CAST(folio AS UNSIGNED INTEGER) DESC";
         // $querySol .= "libro+LPAD(foja,4,'0')+LPAD(folio,4,0) ASC";
         // $querySol .= "ORDER BY LPAD(fecha_lote_id,5,'0')+";
         $solicitudes = DB::connection('condoc_eti')
                  ->select($querySol);
         $total = count($solicitudes);
         // $listaErrores = $this->listaErr();
         $title = $fecha_formato.' - Solicitudes enviadas a Firma';
         $acordeon = $this->acordionTitulosF($solicitudes);
         // dd($solicitudes);
         // $libros = DB::connection('condoc_eti')
         // ->select("select DISTINCT libro from solicitudes_sep where fec_emision_tit='$fecha->fecha_lote' order by ABS(libro) ASC");
         $fojas = DB::connection('condoc_eti')
         ->select("select DISTINCT foja from solicitudes_sep where fec_emision_tit='$fecha->fec_emision_tit' order by ABS(foja) ASC");
      }
      $lote = $foja = '';  // Por ser la vista de primer ingreso, no esta ninún lote elegido.
      $fechaOmision = substr($fechaOmision,8,2).'/'.substr($fechaOmision,5,2).'/'.substr($fechaOmision,0,4);
      $title = "Solicitudes enviadas a Firma";

      return view('menus/firmas_enviadas', compact('title','lote','lotes', 'total','acordeon', 'listaErrores', 'libros','fojas_sel','foja', 'fojas','fechaOmision'));
   }

   public function fechaxOmision()
   {
      // Fecha que nos da la ultima fecha de emisión de títulos solo con firma del departamento de
      $fecha = DB::table('solicitudes_sep')->where('status', '!=', 1)->orderBy('fec_emision_tit', 'desc')->first();
      if($fecha != null){
        return Carbon::parse($fecha->fec_emision_tit)->format('d/m/Y');
      }else{
        return null;
      }
   }

   public function dataSolicitudes(Request $request)
   {
      // dd($request->all());
      // lote de solicitudes con parametros de fecha, lote y foja.
      $select = "select * from solicitudes_sep ";
      $fecha  = $request->fecha;
      $where  = "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '".$fecha."' ";
      $order  = "ORDER BY  ";
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
      // Consulta de Solicitudes.
      $foja  = $request->foja;
      $lote  = $request->lotes;
      $fecha = $request->fecha; // ejemplo "04/10/2018"
      // dd($fecha,$lote,$foja,'errex');

      if ($lote!=0) { // Parametros Fecha y Lote.
         // $loteFecha = LotesUnam::find($lote)->first();
         // dd($loteFecha->fecha_lote);
         if ($foja!=0) { //
            //Menu de Lotes, contienen todos los lotes que tienen
            $queryLotes  = "SELECT DISTINCT fecha_lote_id AS loteId, DATE_FORMAT(fecha_lote,'%d-%m-%Y %h:%i:%s') AS lote ";
            $queryLotes .= "FROM solicitudes_sep ";
            $queryLotes .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' ";
            // $queryLotes .= "AND fecha_lote_id = '$lote' ";
            $queryLotes .= "AND foja = '$foja' ";
            $queryLotes .= "AND fecha_lote_id IS NOT NULL ";
            $queryLotes .= "ORDER BY fecha_lote_id DESC,LPAD(foja,4,'0'),LPAD(folio,4,0)";
            // Menu fojas tienen las fojas correspondientes a lote
            $queryFojas  = "select DISTINCT foja from solicitudes_sep ";
            $queryFojas .= "WHERE fecha_lote_id = '$lote' AND ";
            $queryFojas .= "status > 1 ";
            $queryFojas .= "ORDER BY foja ASC";
            // solicitudes
            $querySol  = "SELECT id, fec_emision_tit, fecha_lote_id, libro, foja, folio, ";
            $querySol .= "num_cta, nombre_completo, nivel, cve_carrera, sistema, datos, ";
            $querySol .= "paridad, cat_nombre as nivel_nombre ";
            $querySol .= "FROM solicitudes_sep ";
            $querySol .= "JOIN _estudios ";
            $querySol .= "ON (nivel COLLATE utf8_unicode_ci)=(cat_subcve COLLATE utf8_unicode_ci) ";
            $querySol .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' AND ";
            $querySol .= "fecha_lote_id = '$lote' AND foja = '$foja' AND " ;
            $querySol .= "status > 1 ";
            $querySol .= "ORDER BY fecha_lote_id DESC, ";
            $querySol .= "CAST(libro AS UNSIGNED INTEGER) ASC, CAST(foja AS UNSIGNED INTEGER) ASC, CAST(folio AS UNSIGNED INTEGER) DESC";
            // $querySol .= "libro ASC, foja ASC, folio DESC ";
         } else { //
            // Menu lotes contiene todas los lotes de la fecha de emisión de titutlos
            $queryLotes  = "SELECT DISTINCT fecha_lote_id AS loteId, DATE_FORMAT(fecha_lote,'%d-%m-%Y %h:%i:%s') AS lote ";
            $queryLotes .= "FROM solicitudes_sep ";
            $queryLotes .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' ";
            // $queryLotes .= "AND fecha_lote_id = '$lote' ";
            // $queryLotes .= "AND foja = '$foja' ";
            $queryLotes .= "AND fecha_lote_id IS NOT NULL ";
            $queryLotes .= "ORDER BY fecha_lote_id DESC,LPAD(foja,3,'0'),LPAD(folio,3,0)";
            // Menu fojas tienen las fojas correspondientes a lote
            $queryFojas  = "select DISTINCT foja from solicitudes_sep ";
            $queryFojas .= "WHERE fecha_lote_id = '$lote' AND ";
            $queryFojas .= "status > 1 ";
            $queryFojas .= "ORDER BY foja ASC";
            // Las solicitudes corresponden a la Fecha de emisióń de titulos y del lote correspondiente
            $querySol  = "SELECT id, fec_emision_tit, fecha_lote_id, libro, foja, folio, ";
            $querySol .= "num_cta, nombre_completo, nivel, cve_carrera, sistema, datos, ";
            $querySol .= "paridad, cat_nombre as nivel_nombre ";
            $querySol .= "FROM solicitudes_sep ";
            $querySol .= "JOIN _estudios ";
            $querySol .= "ON (nivel COLLATE utf8_unicode_ci)=(cat_subcve COLLATE utf8_unicode_ci) ";
            $querySol .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' AND ";
            $querySol .= "fecha_lote_id = '$lote' AND ";
            $querySol .= "status > 1 ";
            $querySol .= "ORDER BY fecha_lote_id DESC, ";
            $querySol .= "CAST(libro AS UNSIGNED INTEGER) ASC, CAST(foja AS UNSIGNED INTEGER) ASC, CAST(folio AS UNSIGNED INTEGER) DESC";
            // $querySol .= "libro ASC, foja ASC, folio DESC ";
         }
      }else{ // No se especifica el lote y se especifica la foja
         if ($foja!=0) { // existe el valor de Lote
            //Menu de Lotes, contienen todos los lotes que tienen
            $queryLotes  = "SELECT DISTINCT fecha_lote_id AS loteId, DATE_FORMAT(fecha_lote,'%d-%m-%Y %h:%i:%s') AS lote ";
            $queryLotes .= "FROM solicitudes_sep ";
            $queryLotes .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' AND ";
            // $queryLotes .= "AND fecha_lote_id = '$lote' ";
            $queryLotes .= "foja = '$foja' AND ";
            $queryLotes .= "fecha_lote_id IS NOT NULL ";
            $queryLotes .= "ORDER BY fecha_lote_id DESC,LPAD(foja,4,'0'),LPAD(folio,4,0)";
            // Menu fojas tienen las fojas correspondientes a lote
            $queryFojas  = "select DISTINCT foja from solicitudes_sep ";
            $queryFojas .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' AND ";
            $queryFojas .= "status > 1 ";
            $queryFojas .= "ORDER BY foja ASC";
            // Solicitudes (todas) y que no se especifico ni lote ni foja
            $querySol  = "SELECT id, fec_emision_tit, fecha_lote_id, libro, foja, folio, ";
            $querySol .= "num_cta, nombre_completo, nivel, cve_carrera, sistema, datos, ";
            $querySol .= "paridad, cat_nombre as nivel_nombre ";
            $querySol .= "FROM solicitudes_sep ";
            $querySol .= "JOIN _estudios ";
            $querySol .= "ON (nivel COLLATE utf8_unicode_ci)=(cat_subcve COLLATE utf8_unicode_ci) ";
            $querySol .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' AND ";
            $querySol .= "foja = '$foja' AND status<>1 ";
            $querySol .= "ORDER BY fecha_lote_id DESC, ";
            $querySol .= "libro ASC, foja ASC, folio DESC ";
         } else { // Sin lote y sin Foja
            // Menu lotes contiene todas los lotes de la fecha de emisión de titutlos
            $queryLotes  = "SELECT DISTINCT fecha_lote_id AS loteId, DATE_FORMAT(fecha_lote,'%d-%m-%Y %h:%i:%s') AS lote ";
            $queryLotes .= "FROM solicitudes_sep ";
            $queryLotes .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' AND ";
            $queryLotes .= "fecha_lote_id IS NOT NULL ";
            $queryLotes .= "ORDER BY fecha_lote_id DESC,LPAD(foja,4,'0'),LPAD(folio,4,0)";
            // Fojas.
            $queryFojas  = "select DISTINCT foja from solicitudes_sep ";
            $queryFojas .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' AND ";
            $queryFojas .= "status > 1 ";
            $queryFojas .= "ORDER BY foja ASC";
            // Menu fojas tienen las fojas correspondientes a lote
            $querySol  = "SELECT id, fec_emision_tit, fecha_lote_id, libro, foja, folio, ";
            $querySol .= "num_cta, nombre_completo, nivel, cve_carrera, sistema, datos, ";
            $querySol .= "paridad, cat_nombre as nivel_nombre ";
            $querySol .= "FROM solicitudes_sep ";
            $querySol .= "JOIN _estudios ";
            $querySol .= "ON (nivel COLLATE utf8_unicode_ci)=(cat_subcve COLLATE utf8_unicode_ci) ";
            $querySol .= "WHERE DATE_FORMAT(fec_emision_tit,'%d/%m/%Y') = '$fecha' AND ";
            // $querySol .= "fecha_lote_id = '$lote' AND ";
            $querySol .= "status > 1 ";
            $querySol .= "ORDER BY fecha_lote_id DESC, ";
            // $querySol .= "LPAD(foja,4,'0')+LPAD(folio,4,0) ASC";
            $querySol .= "libro ASC, foja ASC, folio ASC";
         }
      }

      $lotes = DB::connection('condoc_eti')
               ->select($queryLotes);
      $fojas = DB::connection('condoc_eti')
                   ->select($queryFojas);
       $solicitudes = DB::connection('condoc_eti')
               ->select($querySol);
      $title = $libros = '';
      $info = $request->all();
      $total = count($solicitudes);
      $listaErrores = $this->listaErr();
      $acordeon = $this->acordionTitulosF($solicitudes);
      $fechaOmision = $fecha;
      $title = 'Solicitudes enviadas a Firma';
      return view('menus/firmas_enviadas', compact('title','lote','lotes', 'total','acordeon', 'listaErrores', 'libros','fojas_sel','foja', 'fojas','fechaOmision'));
   }

   public function postFirmasBusqueda_x(Request $request)
   {
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
         $fecha_formato = Carbon::parse($fechloteIda->fec_emision_tit)->format('d/m/Y');
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
         $composite .=    "<div class='Cell date'>";
         $composite .=       "<p>".$data[$i]->fecha_lote_id."</p>";
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
         $xNivel = $data[$i]->nivel.'. '.$data[$i]->nivel_nombre;
         $composite .=       "<p>".$xNivel."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell cve'>";
         $composite .=       "<p>".$data[$i]->cve_carrera."</p>";
         $composite .=    "</div>";
         $composite .=    "<div class='Cell sistema'>";
         $composite .=       "<p>".$data[$i]->sistema."</p>";
         $composite .=    "</div>";
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

   public function showProgreso()
   {
      // Genera archivo de envio zip/xml y envia al WS de la SEP
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
      else{
         $fecha = LotesUnam::all()->last();
         if($fecha != null){
           $fecha = $fecha->fecha_lote;
           $fecha = Carbon::parse($fecha)->format('Y/m/d');
         }else{
           $lists = null;
           return view('menus/firmas_progreso', compact('title', 'lists'));
         }
      }
      $fecha_o = Carbon::parse($fecha)->format('d/m/Y');
      $day = substr($fecha,8,2);;
      $month = substr($fecha,5,2);
      $year = substr($fecha,0,4);
      // Seleccionamos los lotes que tengan solicitudes que no hayan sido enviados a la DGP
      $queryFecha = "SELECT *, lotes_unam.id AS loteId FROM lotes_unam ";
      $queryFecha .= "JOIN solicitudes_sep ON lotes_unam.id = solicitudes_sep.fecha_lote_id ";
      $queryFecha .= "WHERE (YEAR(lotes_unam.fecha_lote) = ".$year." AND MONTH(lotes_unam.fecha_lote) = ".$month." AND DAY(lotes_unam.fecha_lote) = ".$day.")";
      $queryFecha .= " AND solicitudes_sep.status < 5 AND solicitudes_sep.fecha_lote IS NOT NULL GROUP BY solicitudes_sep.fecha_lote";
      $lists = DB::connection('condoc_eti')->select($queryFecha);
      // dd($lists);
      $total = count($lists);
      // Contabilizacion de firmas
      foreach ($lists as $l) {
         if($l->firma0 && $l->firma1){
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

   public function generaLotes($data, $totalF)
   {
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
      $composite .=                 "'.../../informacionDetallada/lote?fechaLote=$var'";
      $composite .=              ">".$data[$i]->fecha_lote."</a>";
      $composite .=           "</p>";
      $composite .=        "</div>";
      $composite .=        "<div class='Cell_mod numCedulaxLote center'>";
      $composite .=           "<p> Contiene: ".$cedulas." cédulas</p>";
      $composite .=        "</div>";
      $composite .=        "<div class='Cell_mod firmas_total'>";
      $composite .=           "<table style='width: 100%;'><tr>";
      $composite .=           "<td><p> Firmas: ".count($totalF[$i])."</p></td>";
      if(count($totalF[$i]) == 2)
      {
         $composite .=        "<td>
                                 <span class='fa fa-check-square-o f_dirt'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_dir'/>
                              </td>";
      }
      elseif (count($totalF[$i]) == 1)
      {
         $composite .=        "<td>
                                 <span class='fa fa-check-square-o f_dirt'/>
                              </td>
                              <td>
                                 <span class='fa fa-check-square-o f_dir oculto'/>
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
      if(count($totalF[$i]) == 2){
      // if(count($totalF[$i]) == 0){
         if ($enviado->status == 4) {
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
        case 'Jtit':
           $lote = NULL;
           $num=0;
           $fecha = DB::table('lotes_unam')->where('firma'.$num, '1')->orderBy('fec_firma'.$num, 'desc')->first();
           if($fecha != null)
           {
              $fecha_formato = Carbon::parse($fecha->fec_firma0)->format('d/m/Y');
              $lote = $this->SolFirma($fecha->fec_firma0, $num);
           }
        break;
        case 'Director':
            $lote = NULL;
            $num=1;
            $fecha = DB::table('lotes_unam')->where('firma'.$num, '1')->orderBy('fec_firma'.$num, 'desc')->first();
            if($fecha != null)
            {
               $fecha_formato = Carbon::parse($fecha->fec_firma1)->format('d/m/Y');
               $lote = $this->SolFirma($fecha->fec_firma1, $num);
            }
           break;
        case 'SecGral':
            $lote = NULL;
            $num=2;
            $fecha = DB::table('lotes_unam')->where('firma'.$num, '1')->orderBy('fec_firma'.$num, 'desc')->first();
            if($fecha != null)
            {
               $fecha_formato = Carbon::parse($fecha->fec_firma2)->format('d/m/Y');
               //$aux = Carbon::parse($fecha->fec_firma2)->format('Y-m-d');
               $lote = $this->SolFirma($fecha->fec_firma2, $num);
            }
           break;
        case 'Rector':
           $lote = NULL;
           $num=3;
           $fecha = DB::table('lotes_unam')->where('firma'.$num, '1')->orderBy('fec_firma'.$num, 'desc')->first();
           if($fecha != null)
           {
             $fecha_formato = Carbon::parse($fecha->fec_firma3)->format('d/m/Y');
             $lote = $this->SolFirma($fecha->fec_firma3, $num);
           }
           break;
     }
     $total = count($lote);
     if($total != 0){
       $title = $fecha_formato.' - Solicitudes firmadas';
     }else{
       $title = 'Solicitudes firmadas';
     }
     $acordeon = $this->generaListasxLoteSinF($lote, " Firmado ");
     // $acordeon = $this->generaLotes($lote, $total);

     return view('menus/firmadas', compact('title','lote', 'total','acordeon', 'fecha_formato'));
   }

   public function postFirmadas(Request $request)
   {
       $request->validate([
           'fecha' => 'required'
       ],[
           'fecha.required' => 'Debes seleccionar una fecha',
       ]);

       $fecha = $_POST['fecha'];
       $time =  DateTime::createFromFormat('d/m/Y', $fecha);
       $date = $time->format('Y-m-d');
       $fecha_formato = $time->format('d/m/Y');
       $rol = Auth::user()->roles()->get();
       $roles_us = array(); //Obtenemos los roles del usuario actual
       foreach($rol as $actual){
         array_push($roles_us, $actual->nombre);
       }
       switch ($roles_us[0]) {
          case 'Jtit':
             $lote = DB::connection('condoc_eti')
                 ->select('select * from lotes_unam WHERE firma0 = 1 AND fec_firma0 LIKE "'.$date.'%" order by id');
             break;
          case 'Director':
             $lote = DB::connection('condoc_eti')
                    ->select('select * from lotes_unam WHERE firma1 LIKE "1" AND fec_firma1 LIKE "'.$date.'%" order by id');
             break;
          case 'SecGral':
             $lote = DB::connection('condoc_eti')
                      ->select('select * from lotes_unam WHERE firma2 LIKE "1" AND fec_firma2 LIKE "'.$date.'%" order by id');
             break;
          case 'Rector':
             $lote = DB::connection('condoc_eti')
                      ->select('select * from lotes_unam WHERE firma3 LIKE "1" AND fec_firma3 LIKE "'.$date.'%" order by id');
             break;
       }

       $total = count($lote);
       $listaErrores = $this->listaErr();
       $title = $fecha_formato.' - Solicitudes firmadas';
       $acordeon = $this->generaListasxLoteSinF($lote, "Firmado");

       return view('menus/firmadas', compact('title','lote', 'total','acordeon', 'listaErrores', 'fecha_formato'));
   }

   public function generaListasxLoteSinF($data, $estado)
   {
      // Elaboracion del acordion con listas.
      $composite = "<div class='firmas'>";
      for ($i=0; $i < count($data) ; $i++) {
         $x_list = $i + 1;
         $cedulas = $this->cantidadCedulas($data[$i]->fecha_lote);
         $composite .= "<div class='accordion-a style-firmas'>";
         $composite .= "<table style='width:100%;'>";
         $composite .= "<tr><td><p>Lote: ".$data[$i]->id."</p></td>";
         $composite .= "<td><p> Fecha de Lote: <a href=";
         $var = $data[$i]->fecha_lote;
         $composite .= "'.../../informacionDetallada/firmadas/lote?fechaLote=$var'";
         $composite .= "> ".$var."</a></p></td>";
         $composite .= "<td><p>Contiene: ".$cedulas." cédulas</p></td>";
         $composite .= "<td><p style='color: green;'>".$estado."</p></td>";
         $composite .= "</tr>";
         $composite .= "</table>";
         $composite .= "</div>";
      }
      return $composite;
   }

   public function SolFirma($fechaFirma, $num)
   {
      //$fecha_formato = Carbon::parse($fechaFirma)->format('d/m/Y');
      $aux = Carbon::parse($fechaFirma)->format('Y-m-d');
      $lote = DB::connection('condoc_eti')
         ->table('lotes_unam')
         ->where('firma'.$num, '1')
         ->where('fec_firma'.$num, 'LIKE', $aux.'%')
         ->paginate(5);
      return $lote;
   }

   public function cedulasDGP_Fecha()
   {
      //Elegimos la ultima fecha en al que se ha hecho un envios a al DGP
      $queryFecha  = "SELECT DISTINCT DATE_FORMAT(tit_fec_DGP,'%d/%m/%Y') AS fecha ";
      $queryFecha .= "FROM solicitudes_sep ";
      $queryFecha .= "WHERE tit_fec_DGP IS NOT NULL ";
      $queryFecha .= "GROUP BY tit_fec_DGP ";
      $queryFecha .= "ORDER BY fecha DESC";
      $data = DB::select($queryFecha);
      return (!empty($data))? $data[0]->fecha : "";
   }
   public function cedulasDGP_Envios($fecha, $nivel)
   {
      // Consultamos las cédulas enviadas a la DGP en el nivel y en la fecha elegida.
      // $queryDGP =  "SELECT fecha_lote_id, nivel, cat_nombre as nivel_nombre ,fecha_lote, ";
      $queryDGP =  "SELECT fecha_lote_id,fecha_lote, ";
      $queryDGP.=  "tit_fec_DGP, DATE_FORMAT(tit_fec_DGP,'%d-%m-%Y') as fecha_DGP, count(*) AS cedulas ";
      $queryDGP.=  "FROM solicitudes_sep s ";
      $queryDGP.=  "INNER JOIN lotes_dgp l ";
      $queryDGP.=  "ON l.lote_unam_id = s.fecha_lote_id ";
      $queryDGP.=  "WHERE tit_fec_DGP IS NOT NULL ";
      $queryDGP.=  "AND status=7 ";
      $queryDGP.=  ($fecha!='*')? "AND DATE_FORMAT(tit_fec_DGP,'%d/%m/%Y') = '$fecha'  " : "";
      $queryDGP.=  ($nivel!='*')? "AND nivel = '$nivel' " : "";
      $queryDGP.=  "GROUP BY tit_fec_DGP ";
      $queryDGP.=  "ORDER BY tit_fec_DGP DESC ";
      // dd($queryDGP);
      $data = DB::connection('condoc_eti')->select($queryDGP);
      return $data;
   }
   public function cedulasDGP_Niveles($fecha,$nivel)
   {
      // Obtenemos los niveles de las cédulas en la fecha de elección.
      $niveles = array();
      $queryNivel =  "SELECT nivel, cat_nombre as nivel_nombre, count(*) AS cedulas ";
      $queryNivel.=  "FROM solicitudes_sep ";
      $queryNivel.=  "INNER JOIN _estudios ";
      $queryNivel.=  "ON cat_subcve  COLLATE utf8_spanish_ci = nivel ";
      $queryNivel.=  "WHERE tit_fec_DGP IS NOT NULL ";
      $queryNivel.=  ($fecha!='*' )? "AND DATE_FORMAT(tit_fec_DGP,'%d/%m/%Y') = '$fecha' " : "";
      // $queryNivel.=  ($nivel!='*' && $fecha!='*')? "AND nivel = '$nivel' " : "";
      $queryNivel.=  "GROUP BY nivel";
      $data = DB::connection('condoc_eti')->select($queryNivel);
      $total_Cedulas=0; $niveles['*'] = '--Todos--'; // inicializamos los niveles y agregamos el nivel general
      foreach ($data as $envio) {
         $total_Cedulas += $envio->cedulas;
         if (!in_array($envio->nivel, $niveles)) {
            $niveles[$envio->nivel] = $envio->nivel_nombre.' ('.$envio->cedulas.' Cédulas)';
         }
      }
      return $niveles;
   }

   public function showCedulasDGP()
   {
      // En primera consulta, se eligen todos los envios y todas niveles disponibles.
      $fecha_inicial = '*'; $nivel = '*';
      $data          = $this->cedulasDGP_Envios($fecha_inicial,$nivel);
      $niveles       = $this->cedulasDGP_Niveles($fecha_inicial,$nivel);
      // dd($fecha_inicial,$niveles,$data);
      if (isset($data)) {
         // Obtenemos el arreglo restringido de niveles disponibles ($niveles) y ,
         // el Total de Lotes ($totLotes) y Total de cédulas ($totCedulas)
         $total_Lotes = count($data); $total_Cedulas = 0;
         foreach ($data as $envio) {
            $total_Cedulas += $envio->cedulas;
         }
         $title = "Lotes Enviados:$total_Lotes; Cédulas: $total_Cedulas.";
         $acordeon = $this->acordionTitulosLTS($data,$nivel);
         // fecha que por omision se va a colocar primero en el Date Picker;
         return view('menus/cedulas_DGP', compact('title','acordeon','niveles','nivel','fecha_inicial'));
      }
      else
      {
         $title = 'Cédulas enviadas a la DGP';
         return view('menus/cedulas_DGP', compact('title'));
      }
   }

   public function acordionTitulosLTS($data,$nivel)
   {
      // Elaboracion del acordion con listas.
      $composite = "<div class='firmas' style='width:100%;'>";
      for ($i=0; $i < count($data) ; $i++) {
      $x_list = $i + 1;
      // $cedulas = $this->cantidadCedulas($data[$i]->fecha_lote);
      $composite .= "<div class='accordion-aL'>";
      $composite .=     "<div class='Row'>";
      $composite .=        "<div class='Cell_mod id_f right_f'>";
      $composite .=           "<p> Lote Id: ".$data[$i]->fecha_lote_id."</p>";
      $composite .=        "</div>";
      $composite .=        "<div class='Cell_mod id_f right_f'>";
      $composite .=           "<p> Envio: ".$data[$i]->tit_fec_DGP."</p>";
      $composite .=        "</div>";
      $composite .=        "<div class='Cell_mod fechaLoteL'>";
      $composite .=           "<p>";
      $composite .=              "Lote: ";
      $composite .=              "<a href=";
      $lote = $data[$i]->fecha_lote;
      $envio = $data[$i]->tit_fec_DGP;
      $composite .=                 "'.../../informacionDetallada/enviadas/lote?fechaLote=$lote&fechaEnvio=$envio&nivel=$nivel'";
      $composite .=              ">".$data[$i]->fecha_lote."</a>";
      $composite .=           "</p>";
      $composite .=        "</div>";
      $composite .=        "<div class='Cell_mod numCedulaxLote center'>";
      $composite .=           "<p> Contiene: ".$data[$i]->cedulas." cédulas</p>";
      $composite .=        "</div>";
      $composite .=     "</div>";
      $composite .= "</div>";
      }
      return $composite;
   }

   public function postCedulasDGP(Request $request)
   {
      // Regreso de el formulario de Envios a la DGP
      $fecha_inicial = (isset($request->fecha))? $request->fecha : "*";
      $nivel         = (isset($request->nivel))? $request->nivel : "*" ;
      $data          = $this->cedulasDGP_Envios($fecha_inicial,$nivel);
      $niveles       = $this->cedulasDGP_Niveles($fecha_inicial,$nivel);
      if ($data==[]) {
         // Replanteamos la consulta suprimiendo el nivel para que nos genere resultados.
         $nivel = "*";
         $data          = $this->cedulasDGP_Envios($fecha_inicial,$nivel);
         $niveles       = $this->cedulasDGP_Niveles($fecha_inicial,$nivel);
      }
      if (isset($data)) {
         // Obtenemos el arreglo restringido de niveles disponibles ($niveles) y ,
         // el Total de Lotes ($totLotes) y Total de cédulas ($totCedulas)
         $total_Lotes = count($data); $total_Cedulas = 0;
         foreach ($data as $envio) {
            $total_Cedulas += $envio->cedulas;
         }
         $title = "Lotes Enviados:$total_Lotes; Cédulas: $total_Cedulas.";
         $acordeon = $this->acordionTitulosLTS($data,$nivel);
         // fecha que por omision se va a colocar primero en el Date Picker;
         return view('menus/cedulas_DGP', compact('title','acordeon','niveles','nivel','fecha_inicial'));
      }
      else
      {
         $title = 'Cédulas enviadas a la DGP';
         return view('menus/cedulas_DGP', compact('title'));
      }
   }

   public function postCedulasDGP_Respaldo(Request $request)
   {
    if(isset($_POST['seleccion'])) {
      $request->validate([
          'fecha' => 'required'
      ],[
          'fecha.required' => 'Debes seleccionar una fecha de lote'
      ]);

      $fecha = $_POST['fecha'];
      $date = date_create($fecha);
      $fecha_formato = date_format($date, 'd/m/Y');
      $aux = Carbon::parse($fecha)->format('Y-m-d');
      $lote = DB::connection('condoc_eti')
               ->select("SELECT *
                         FROM lotes_unam
                         WHERE fecha_lote LIKE '$aux%' order by id");
      $title = $fecha_formato.' - Cédulas enviadas a la DGP';
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

   public function generaPDF_Envios($data,$nivel,$fecha,$lotes,$cedulas)
   {
      // General el HTML de los envíos a la DGP, sin especificar el detalle.
      // dd($data,$nivel,$fecha,$lotes,$cedulas);
      $composite = "";
      $composite .= "<div class='container pdf_c'>";
      $composite .= "<div class='ati_pdf'>";
      $composite .= "<table class='tabla'>";
      $composite .= "<tr>";
      $composite .= "<td><div align='left'><img src='images/logo_unam.jpg' height='120' width='105'></div></td>";
      $composite .= "<td><div class='head_DGP' align='center'><h3>UNIVERSIDAD NACIONAL AUTONOMA DE MÉXICO.</h3>";
      $fecha      = ($fecha!='*')? '----> '.$fecha : "" ;
      // Si se establece el nivel, lo tomamos del primer registros del conjunto "data"
      $composite .= "<h3>$lotes Envios a DGP $fecha </h3>";
      if ($nivel!='*') {
         $nivel_n =  DB::table('_estudios')->where('cat_subcve', $nivel)->pluck('cat_nombre')[0];
      } else {
         $nivel_n="";
      }
      // colocamos un guion intermedio si $fecha y $nivel no estan vacios.
      $composite .= "<h3>$nivel_n  $cedulas Cédulas </h3></div></td>";
      $composite .= "</tr>";
      $composite .= "</table>";
      $composite .= "</div>";
      for ($x=0; $x < count($data) ; $x++)
      {
         $composite .= "<table id='t01'>";
         $composite .= "<thead>";
         $composite .= "<tr>";
         $composite .= "<th scope='col'><strong>#</strong></th>";
         $composite .= "<th scope='col'><strong>Lote ID</strong></th>";
         $composite .= "<th scope='col'><strong>Fecha de Envío</strong></th>";
         $composite .= "<th scope='col'><strong>Fecha de Lote</strong></th>";
         $composite .= "<th scope='col'><strong># Cédulas</strong></th>";
         $composite .= "</tr>";
         $composite .= "</thead>";
         $composite .= "<tbody>";
          $composite .= "<tr>";
          $composite .= "<th>".($x+1)."</th>";
          $composite .= "<td>".$data[$x]->fecha_lote_id."</td>";
          $composite .= "<td>".$data[$x]->tit_fec_DGP."</td>";
          $composite .= "<td>".$data[$x]->fecha_lote."</td>";
          $composite .= "<td>".$data[$x]->cedulas."</td>";
          $composite .= "</tr>";
          $composite .= "</tbody>";
          $composite .= "</table>";
          // Buscamos el detalle de la fecha y del nivel para filtrar las cedulasG
          // showDetallePDF($fechaLote,$fechaEnvio,$nivel)
          $detalle = $this->showDetallePDF($data[$x]->fecha_lote,
                                           $data[$x]->tit_fec_DGP,
                                           $nivel);
          // $composite .= "<tr>";
          $composite .= $detalle;
          // $composite .= "</tr>";
      }
      return $composite;
   }

   public function showDetallePDF($fechaLote,$fechaEnvio,$nivel)
   {
      // Detalle de los lotes enviados y se utiliza en el PDF
      if ($nivel=='*') {
         // filtro fecha todos los niveles
         $lote = SolicitudSep::
                     where('fecha_lote', $fechaLote)
                     ->get();
      } else {
         // filtro fecha y nivel
         $lote = SolicitudSep::
                     where('fecha_lote', $fechaLote)->
                     where('nivel',$nivel)->
                     get();
      }
     $list = $this->armadoDetallePDF($lote);
     return $list;
   }

   public function armadoDetallePDF($lote)
   {
      // Detalle PDF
      // $composite = "<div class='lote'>";
      // $composite =  "<table class='table table-striped table-dark table-bordered'>";
      $composite =  "<table id='t02'>";
      $composite .=     "<thead>";
      $composite .=        "<tr>";
      $composite .=           "<th scope='col'># SOLICITUD</th>";
      $composite .=           "<th scope='col'><strong>NO. CUENTA</strong></th>";
      $composite .=           "<th scope='col'><strong>NOMBRE COMPLETO</strong></th>";
      $composite .=           "<th scope='col'><strong>CLV CARRERA</strong></th>";
      $composite .=           "<th scope='col'><strong>NOMBRE CARRERA</strong></th>";
      $composite .=           "<th scope='col'><strong>NIVEL</strong></th>";
      $composite .=        "</tr>";
      $composite .=     "</thead>";
      $composite .=     "<tbody>";
      foreach ($lote as $key => $alumno) {
         $composite .=     "<tr id='".$alumno->num_cta."' class='".$alumno->num_cta."'>";
         $composite .=        "<th scope='row'>".$alumno->id."</th>";
         $composite .=           "<td>".$alumno->num_cta."</td>";
         $composite .=           "<td>".$alumno->nombre_completo."</td>";
         $composite .=           "<td>".$alumno->cve_carrera."</td>";
         $composite .=           "<td>".$this->carreraNombre($alumno->cve_carrera)."</td>";
         $composite .=           "<td>".$alumno->nivel."</td>";
         $composite .=        "</tr>";
      }
      $composite .=     "</tbody>";
      $composite .=  "</table>";
      // $composite .= "</div>";
      return $composite;
   }

   public function generaPDF_Cedulas($data,$request)
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
      $composite .= "<h3>Listado de Cédulas enviadas a DGP. Fecha:</h3></div></td>";
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

   // public function pdf_DGP($nivel,$fecha)
   public function pdf_DGP(Request $request)
   {
      // Generación de el PDF de envios a la DGP
      // Pasamos las llaves aun arreglo..
      $parametros = array_keys($request->all());
      if(count($parametros)==1)
      {
         // Solo un parametro (entonces no se elige ni fecha ni nivel)
         $fecha_inicial = '*';
         $nivel         = '*';
         $data          = $this->cedulasDGP_Envios($fecha_inicial,$nivel);
      } else {
         // Recuperamos $fecha y $niveles
         $nivel         = $parametros[0];
         $fecha_inicial = $parametros[1];
         $data          = $this->cedulasDGP_Envios($fecha_inicial,$nivel);
      }
      // Variables del encabezado del documento PDF
      $total_Lotes = count($data); $total_Cedulas = 0;
      foreach ($data as $envio) {
         $total_Cedulas += $envio->cedulas;
      }
      // Generación de la vista y el PDF.
      $vista = $this->generaPDF_Envios($data, $nivel, $fecha_inicial, $total_Lotes,$total_Cedulas);
      $view = \View::make('consultas.listasDGP', compact('vista'))->render();
      $pdf = \App::make('dompdf.wrapper');
      $pdf->loadHTML($view);
      return $pdf->stream('EnvíoDGP_'.str_replace('/','-',$request['fecha']).'.pdf');
   }

}
