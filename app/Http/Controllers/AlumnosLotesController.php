<?php

use Carbon\Carbon;
namespace App\Http\Controllers;
use \Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{LotesUnam, SolicitudSep, SolicitudesCanceladas, LotesCancelados, Estudio};
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\Consultas\XmlCadenaErrores;
use App\Http\Traits\Consultas\TitulosFechas;
use App\Http\Traits\Consultas\LotesFirma;
use App\Http\Traits\Consultas\Utilerias;
use DateTime;
use Session;
use DB;
use Carbon\Carbon;

class AlumnosLotesController extends Controller
{
  use TitulosFechas, XmlCadenaErrores, LotesFirma, Utilerias;

  public function showprocesoAlumno(){
    $title = "Trazabilidad de Cédula Electrónica";
    $num_cta = $_GET['numCta'];
    $nombre = $_GET['nombre'];
    $carrera = $_GET['carrera'];
    $nivel = $_GET['nivel'];
    $cuenta = substr($num_cta, 0, 8);
    $foto = $this->consultaFotosMin($cuenta);

    $consulta = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE num_cta = "'.$num_cta.'" AND cve_carrera = "'.$carrera.'" AND nivel = "'.$nivel.'"');
    $lote = $consulta[0]->fecha_lote;
    // dd($lote, $consulta);
    $firmas = DB::connection('condoc_eti')->select("select * from lotes_unam WHERE fecha_lote LIKE '".$lote."'");
    $fechaSol = $this->conversionFechaEsp($consulta[0]->created_at);
    $tiempoSol = Carbon::parse($consulta[0]->created_at)->diffForHumans();

    $fechaAut = $this->conversionFechaEsp($lote);
    $tiempoAut = Carbon::parse($lote)->diffForHumans();
    if(!empty($firmas))
    {
      $fechaTit = $this->conversionFechaEsp($firmas[0]->fec_firma0);
      $tiempoTit = Carbon::parse($firmas[0]->fec_firma0)->diffForHumans();
      $fechaDGAE = $this->conversionFechaEsp($firmas[0]->fec_firma1);
      $tiempoDGAE = Carbon::parse($firmas[0]->fec_firma1)->diffForHumans();
      $fechaSEC = $this->conversionFechaEsp($firmas[0]->fec_firma2);
      $tiempoSEC = Carbon::parse($firmas[0]->fec_firma2)->diffForHumans();
      $fechaREC = $this->conversionFechaEsp($firmas[0]->fec_firma3);
      $tiempoREC = Carbon::parse($firmas[0]->fec_firma3)->diffForHumans();
      $fechaEnvio = "";
      $tiempoEnvio = "";
    }
    else {
       $fechaTit = $tiempoTit = $fechaDGAE = $tiempoDGAE = $fechaSEC = $tiempoSEC = $fechaREC = $tiempoREC = $fechaEnvio = $tiempoEnvio = "";
    }
    switch ($consulta[0]->status) {
      case '1':
        $info = array('solicitud' => true,
                      'fecha_sol' => $fechaSol,
                      'tiempo_sol' => $tiempoSol,
                      'autorizacion' => false,
                      'fecha_aut' => $fechaAut,
                      'tiempo_aut' => $tiempoAut,
                      'firma_tit' => false,
                      'fecha_tit' => $fechaTit,
                      'tiempo_tit' => $tiempoTit,
                      'firma_dgae' => false,
                      'fecha_dgae' => $fechaDGAE,
                      'tiempo_dgae' => $tiempoDGAE,
                      'firma_sec' => false,
                      'fecha_sec' => $fechaSEC,
                      'tiempo_sec' => $tiempoSEC,
                      'firma_rec' => false,
                      'fecha_rec' => $fechaREC,
                      'tiempo_rec' => $tiempoREC,
                      'enviada' => false,
                      'fecha_envio' => $fechaEnvio,
                      'tiempo_envio' => $tiempoEnvio
                    );
      break;
      case '2':
        $info = array('solicitud' => true,
                      'fecha_sol' => $fechaSol,
                      'tiempo_sol' => $tiempoSol,
                      'autorizacion' => true,
                      'fecha_aut' => $fechaAut,
                      'tiempo_aut' => $tiempoAut,
                      'firma_tit' => false,
                      'fecha_tit' => $fechaTit,
                      'tiempo_tit' => $tiempoTit,
                      'firma_dgae' => false,
                      'fecha_dgae' => $fechaDGAE,
                      'tiempo_dgae' => $tiempoDGAE,
                      'firma_sec' => false,
                      'fecha_sec' => $fechaSEC,
                      'tiempo_sec' => $tiempoSEC,
                      'firma_rec' => false,
                      'fecha_rec' => $fechaREC,
                      'tiempo_rec' => $tiempoREC,
                      'enviada' => false,
                      'fecha_envio' => $fechaEnvio,
                      'tiempo_envio' => $tiempoEnvio
                   );
      break;
      case '3':
        $info = array('solicitud' => true,
                      'fecha_sol' => $fechaSol,
                      'tiempo_sol' => $tiempoSol,
                      'autorizacion' => true,
                      'fecha_aut' => $fechaAut,
                      'tiempo_aut' => $tiempoAut,
                      'firma_tit' => true,
                      'fecha_tit' => $fechaTit,
                      'tiempo_tit' => $tiempoTit,
                      'firma_dgae' => false,
                      'fecha_dgae' => $fechaDGAE,
                      'tiempo_dgae' => $tiempoDGAE,
                      'firma_sec' => false,
                      'fecha_sec' => $fechaSEC,
                      'tiempo_sec' => $tiempoSEC,
                      'firma_rec' => false,
                      'fecha_rec' => $fechaREC,
                      'tiempo_rec' => $tiempoREC,
                      'enviada' => false,
                      'fecha_envio' => $fechaEnvio,
                      'tiempo_envio' => $tiempoEnvio
                   );
      break;
      case '4':
        $info = array('solicitud' => true,
                      'fecha_sol' => $fechaSol,
                      'tiempo_sol' => $tiempoSol,
                      'autorizacion' => true,
                      'fecha_aut' => $fechaAut,
                      'tiempo_aut' => $tiempoAut,
                      'firma_tit' => true,
                      'fecha_tit' => $fechaTit,
                      'tiempo_tit' => $tiempoTit,
                      'firma_dgae' => true,
                      'fecha_dgae' => $fechaDGAE,
                      'tiempo_dgae' => $tiempoDGAE,
                      'firma_sec' => false,
                      'fecha_sec' => $fechaSEC,
                      'tiempo_sec' => $tiempoSEC,
                      'firma_rec' => false,
                      'fecha_rec' => $fechaREC,
                      'tiempo_rec' => $tiempoREC,
                      'enviada' => false,
                      'fecha_envio' => $fechaEnvio,
                      'tiempo_envio' => $tiempoEnvio
                      );
      break;
      case '5':
        $info = array('solicitud' => true,
                      'fecha_sol' => $fechaSol,
                      'tiempo_sol' => $tiempoSol,
                      'autorizacion' => true,
                      'fecha_aut' => $fechaAut,
                      'tiempo_aut' => $tiempoAut,
                      'firma_tit' => true,
                      'fecha_tit' => $fechaTit,
                      'tiempo_tit' => $tiempoTit,
                      'firma_dgae' => true,
                      'fecha_dgae' => $fechaDGAE,
                      'tiempo_dgae' => $tiempoDGAE,
                      'firma_sec' => true,
                      'fecha_sec' => $fechaSEC,
                      'tiempo_sec' => $tiempoSEC,
                      'firma_rec' => false,
                      'fecha_rec' => $fechaREC,
                      'tiempo_rec' => $tiempoREC,
                      'enviada' => false,
                      'fecha_envio' => $fechaEnvio,
                      'tiempo_envio' => $tiempoEnvio
                   );
      break;
      case '6':
        $info = array('solicitud' => true,
                      'fecha_sol' => $fechaSol,
                      'tiempo_sol' => $tiempoSol,
                      'autorizacion' => true,
                      'fecha_aut' => $fechaAut,
                      'tiempo_aut' => $tiempoAut,
                      'firma_tit' => true,
                      'fecha_tit' => $fechaTit,
                      'tiempo_tit' => $tiempoTit,
                      'firma_dgae' => true,
                      'fecha_dgae' => $fechaDGAE,
                      'tiempo_dgae' => $tiempoDGAE,
                      'firma_sec' => true,
                      'fecha_sec' => $fechaSEC,
                      'tiempo_sec' => $tiempoSEC,
                      'firma_rec' => true,
                      'fecha_rec' => $fechaREC,
                      'tiempo_rec' => $tiempoREC,
                      'enviada' => false,
                      'fecha_envio' => $fechaEnvio,
                      'tiempo_envio' => $tiempoEnvio
                   );
      break;
      case '7':
         $info = array('solicitud' => true,
                      'fecha_sol' => $fechaSol,
                      'tiempo_sol' => $tiempoSol,
                      'autorizacion' => true,
                      'fecha_aut' => $fechaAut,
                      'tiempo_aut' => $tiempoAut,
                      'firma_tit' => true,
                      'fecha_tit' => $fechaTit,
                      'tiempo_tit' => $tiempoTit,
                      'firma_dgae' => true,
                      'fecha_dgae' => $fechaDGAE,
                      'tiempo_dgae' => $tiempoDGAE,
                      'firma_sec' => true,
                      'fecha_sec' => $fechaSEC,
                      'tiempo_sec' => $tiempoSEC,
                      'firma_rec' => true,
                      'fecha_rec' => $fechaREC,
                      'tiempo_rec' => $tiempoREC,
                      'enviada' => true,
                      'fecha_envio' => $fechaEnvio,
                      'tiempo_envio' => $tiempoEnvio
                    );
      break;
    }

    $motivos = $this->motivosCancelacion();
    $n_nivel = $this->nombreNivel($nivel);
    $n_carrera = $this->carreraNombre($carrera);

    return view('/menus/proceso_alumno', compact('title', 'nombre', 'num_cta', 'motivos', 'carrera', 'n_carrera', 'nivel', 'n_nivel', 'info', 'lote', 'foto'));
  }

  public function cancelaProcesoAlumno(Request $request){

      $num_cta = $_POST['num_cta'];
      $nombre = $_POST['nombre'];
      $nivel = $_POST['nivel'];
      $carrera = $_POST['carrera'];
      $motivo = $_POST['motivo'];
      $hoy = new DateTime();

      //Se consulta para saber si es el único en el lote al que pertenece
      $lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE num_cta = "'.$num_cta.'" AND cve_carrera = "'.$carrera.'"');
      $total_lote = DB::connection('condoc_eti')->select('select * from solicitudes_sep WHERE fecha_lote = "'.$lote[0]->fecha_lote.'"');
      //Se agrega a registro de SOLICITUDES CANCELADAS
      $sql = SolicitudesCanceladas::insertGetId(
            array('num_cta' => $num_cta,
                  'nombre_completo' => $nombre,
                  'nivel' => $nivel,
                  'cve_carrera' => $carrera,
                  'fecha_cancelacion' => $hoy->format("Y-m-d H:i:s"),
                  'id_motivoCan' => (int)$motivo
      ));
      //Se elimina de la tabla de solicitudes
      $sql1 = DB::connection('condoc_eti')->delete("delete from solicitudes_sep WHERE num_cta = ".$num_cta." and cve_carrera = '".$carrera."'");
      //En caso de que el lote quede vacío, se agrega al registro de LOTES CANCELADOS
      if(count($total_lote) == 1){
        $sql2 = LotesCancelados::insertGetId(
            array('fecha_lote' => $lote[0]->fecha_lote,
                  'fecha_cancelacion' => $hoy->format("Y-m-d H:i:s")
        ));

        //Y se elimina el lote de lotes_unam
        $sql3 = DB::connection('condoc_eti')->delete("delete from lotes_unam WHERE fecha_lote = '".$lote[0]->fecha_lote."'");
      }

      $msj = "La solicitud con número de cuenta: ".$num_cta." y carrera: ".$carrera." fue cancelada exitosamente";
      Session::flash('warning', $msj);

      return redirect()->route('eSearchInfo', ['num_cta'=>$num_cta]);
  }

  public function showDetalleFirmadas(){
    $fechaLote = $_GET['fechaLote'];
    $lote = SolicitudSep::where('fecha_lote', $fechaLote)->get();
    $list = $this->armadoDetalleLote($lote);
    $total = $lote->count();
    $fecha = Carbon::parse($_GET['fechaLote'])->format("d-m-Y");
    $fecha = explode("-", $fecha);
    $datepicker = $fecha[0]."%2F".$fecha[1]."%2F".$fecha[2];
    $fechaLote = Carbon::parse($_GET['fechaLote'])->format("d-m-Y H:i:s");
    $title = "Lote: ".$lote[0]->fecha_lote_id."; &nbsp; fecha: ".$fechaLote."; &nbsp; cédula(s): ".$total;
    return view('/menus/detalleFirmas', compact('title', 'fechaLote', 'list', 'datepicker'));
  }

   public function showDetalleLote(){
      $fechaLote = $_GET['fechaLote'];  // decia $_GET['lote'];
      $lote = SolicitudSep::where('fecha_lote', $fechaLote)->get();
      $list = $this->armadoDetalleLote($lote);
      $total = $lote->count();
      $fecha = Carbon::parse($_GET['fechaLote'])->format("d-m-Y");
      $fecha = explode("-", $fecha);
      $datepicker = $fecha[0]."%2F".$fecha[1]."%2F".$fecha[2];
      $fechaLote = Carbon::parse($_GET['fechaLote'])->format("d-m-Y H:i:s");
      $title = "Lote: ".$lote[0]->fecha_lote_id."; &nbsp; fecha: ".$fechaLote."; &nbsp; cédula(s): ".$total;
      return view('/menus/detalleLote', compact('title', 'fechaLote', 'list', 'datepicker'));
   }
   public function armadoDetalleLote($lote){
      // dd($lote);
      $composite = "<div class='lote'>";
      $composite .=  "<table class='table table-striped table-dark table-bordered'>";
      $composite .=     "<thead>";
      $composite .=        "<tr>";
      $composite .=           "<th scope='col'># SOLICITUD</th>";
      $composite .=           "<th scope='col'><strong>NO. CUENTA</strong></th>";
      $composite .=           "<th scope='col'><strong>NOMBRE COMPLETO</strong></th>";
      $composite .=           "<th scope='col'><strong>CLV CARRERA</strong></th>";
      $composite .=           "<th scope='col'><strong>NOMBRE CARRERA</strong></th>";
      $composite .=           "<th scope='col'><strong>NIVEL</strong></th>";
      $composite .=           "<th scope='col'><strong>SISTEMA</strong></th>";
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
         $composite .=           "<td>".$alumno->sistema."</td>";
         $composite .=        "</tr>";
      }
      $composite .=     "</tbody>";
      $composite .=  "</table>";
      $composite .= "</div>";
      return $composite;
   }

   public function showDetalleCuenta(){
     $num_cta = $_GET['num_cta'];
     $carrera = $_GET['carrera'];
     $infoP = SolicitudSep::where('num_cta', $num_cta)->where('cve_carrera', $carrera)->get();
     $lote = SolicitudSep::where('fecha_lote', $infoP[0]->fecha_lote)->get();
     $nombre = $infoP[0]->nombre_completo;
     $nivel = $infoP[0]->nivel;
     $list = $this->armadoDetalleLote($lote);
     $total = $lote->count();
     $fecha = Carbon::parse($lote[0]->fecha_lote)->format("d-m-Y");
     $fecha = explode("-", $fecha);
     $fechaLote = Carbon::parse($lote[0]->fecha_lote)->format("d-m-Y H:i:s");
     $title = "Lote: ".$lote[0]->fecha_lote_id."; &nbsp; fecha: ".$fechaLote."; &nbsp; cédula(s): ".$total;
     return view('/menus/detalleLoteCuenta', compact('title', 'fechaLote', 'list', 'num_cta', 'nombre', 'carrera', 'nivel'));
   }

   public function showDetalleEnviadas()
   {
      $fechaLote  = $_GET['fechaLote'];
      $fechaEnvio = $_GET['fechaEnvio'];
      $nivel = $_GET['nivel'];
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
     $list = $this->armadoDetalleLote($lote);
     $total = $lote->count();
     $nombre = ($nivel=='*')? '': Estudio::where('cat_subcve',$nivel)->pluck('cat_nombre')[0];
     $nombre = $nivel.' '.$nombre;
     return view('/menus/detalleEnviadas', compact('fechaEnvio','fechaLote','nombre', 'total','list'));
   }

   public function showFiltradas(){
     $nivel = $_GET['nivel'];
     $fechaLote = new Carbon($_GET['fechaLote']);
     $fechaLote = $fechaLote->format('Y-m-d H:i:s');
     if($nivel == "--- TODOS ---"){
       $lote = SolicitudSep::where('fecha_lote', $fechaLote)->get();
       $total = $lote->count();
       $title = "Lote (".$total." cédulas): ".$fechaLote." | Todos";
     }else{
       $nivel_res = substr($nivel, 0, 2);
       $lote = SolicitudSep::where('fecha_lote', $fechaLote)->where('nivel', $nivel_res)->get();
       $total = $lote->count();
       $title = "Lote (".$total." cédulas): ".$fechaLote." | ".$nivel;
     }
     $list = $this->armadoDetalleLote($lote);
     $fecha = Carbon::parse($_GET['fechaLote'])->format("d-m-Y");
     $fecha = explode("-", $fecha);
     $datepicker = $fecha[0]."%2F".$fecha[1]."%2F".$fecha[2];
     $fechaLote = Carbon::parse($_GET['fechaLote'])->format("d-m-Y H:i:s");
     $aux = Carbon::parse($_GET['fechaLote'])->format("Y-m-d H:i:s");
     $niveles_con = (array)DB::connection('condoc_eti')->select('select * from _estudios');
     $niveles_sol = (array)DB::connection('condoc_eti')
                    ->select('SELECT nivel
                              FROM condoc_tests.solicitudes_sep
                              WHERE fecha_lote = "'.$aux.'"
                              GROUP BY nivel
                              ORDER BY nivel');
     $foo = array('cat_subcve' => '--- TODOS ---');
     $foo = (object)$foo;
     $niveles[0] = $foo->cat_subcve;
     foreach ($niveles_con as $nvl) {
       foreach ($niveles_sol as $nvlc) {
         if($nvl->cat_subcve == $nvlc->nivel){
           $nombre = $nvl->cat_subcve.". ".$nvl->cat_nombre;
           array_push($niveles, $nombre);
         }
       }
     }
     return view('/menus/detalleEnviadas', compact('title', 'fechaLote', 'list', 'niveles', 'datepicker', 'total'));
   }

   /* ///////////////////////// PASAR A FIRMASCEDULACONTROLLER ///////////////////////// */
   public function showSCanceladas(){
     return view('menus/red_solicitudes_canceladas');
   }

   public function postSCanceladas(Request $request){
     $request->validate([
         'num_cta' => 'required|numeric|digits:9'
     ],[
         'num_cta.required' => 'El campo es obligatorio',
         'num_cta.numeric' => 'El campo debe contener solo números',
         'num_cta.digits'  => 'El campo debe ser de 9 dígitos',
     ]);
     return redirect()->route('solicitud_cancelada', ['num_cta'=>$request->num_cta]);
   }

   public function showInfoSC($num_cta){
     $cuenta = substr($num_cta, 0, 8);
     $verif = substr($num_cta, 8, 1);
     $foto = $this->consultaFotos($cuenta);
     $info = $this->consultaCancelacionesS($num_cta);
     if($info == null)
     {
        $msj = "No se encuentran registros en cancelaciones con el número de cuenta ".$num_cta;
        Session::flash('error', $msj);
        $motivo = null;
     }else{
       $motivo = $this->motivoCom($info[0]->id_motivoCan);
     }
     return view('/menus/solicitudes_canceladas', ['numCta' => $num_cta,'foto' => $foto, 'info' => $info, 'motivo' => $motivo]);
   }

   public function showCancelarC(){
     return view('menus/red_cedulas_canceladas');
   }

   public function postCancelarC(Request $request){
     $request->validate([
         'num_cta' => 'required|numeric|digits:9'
     ],[
         'num_cta.required' => 'El campo es obligatorio',
         'num_cta.numeric' => 'El campo debe contener solo números',
         'num_cta.digits'  => 'El campo debe ser de 9 dígitos',
     ]);
     return redirect()->route('cedula_cancelada', ['num_cta'=>$request->num_cta]);
   }

   public function showInfoCC($num_cta){
     $cuenta = substr($num_cta, 0, 8);
     $verif = substr($num_cta, 8, 1);
     $foto = $this->consultaFotos($cuenta);
     $info = $this->solicitud($num_cta);
     if($info == null){
       $msj = "No se encuentran registros con el número de cuenta ".$num_cta;
       Session::flash('error', $msj);
     }elseif($info[0]->status != 7){
       $msjc = "El alumno no cuenta con el proceso requerido para esta cancelación.";
       Session::flash('info', $msjc);
     }
     $motivos = $this->motivosCancelacion();
     return view('/menus/cedulas_canceladas', ['foto' => $foto, 'info' => $info, 'motivos' => $motivos]);
   }
   /* ////////////////////////////////////////////////////////////////////////////////// */

}
