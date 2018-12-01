<?php

use Carbon\Carbon;
namespace App\Http\Controllers;
use \Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{LotesUnam, SolicitudSep, SolicitudesCanceladas, LotesCancelados};
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

    return view('/menus/proceso_alumno', compact('title', 'nombre', 'num_cta', 'motivos', 'carrera', 'nivel', 'info', 'lote'));
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
   public function showDetalleLote(){
      $fechaLote = $_GET['fechaLote'];
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

      //
      foreach ($lote as $key => $alumno) {
         $composite .=     "<tr class='".$alumno->num_cta."'>";
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
     $fechaLote = $_GET['fechaLote'];
     $lote = SolicitudSep::where('fecha_lote', $fechaLote)->get();
     $list = $this->armadoDetalleLote($lote);
     $total = $lote->count();
     $fecha = Carbon::parse($_GET['fechaLote'])->format("d-m-Y");
     $fecha = explode("-", $fecha);
     $datepicker = $fecha[0]."%2F".$fecha[1]."%2F".$fecha[2];
     $fechaLote = Carbon::parse($_GET['fechaLote'])->format("d-m-Y H:i:s");
     $title = "Lote: ".$lote[0]->fecha_lote_id."; &nbsp; fecha: ".$fechaLote."; &nbsp; cédula(s): ".$total;
     return view('/menus/detalleLoteCuenta', compact('title', 'fechaLote', 'list', 'datepicker'));
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
     }elseif($info[0]->status != '7' || $info[0]->status != '8'){
       $msj = "El alumno no cuenta con el proceso requerido para esta cancelación.";
       Session::flash('info', $msj);
     }
     $motivos = $this->motivosCancelacion();
     return view('/menus/cedulas_canceladas', ['foto' => $foto, 'info' => $info, 'motivos' => $motivos]);
   }
   /* ////////////////////////////////////////////////////////////////////////////////// */

}
