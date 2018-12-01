<?php

namespace App\Http\Traits\Consultas;
use DB;
use App\Models\{SolicitudSep, Web_Service, Alumno, LotesUnam, Cancela};
use App\Http\Controllers\Admin\WSController;
use Carbon\Carbon;
use Session;

trait LotesFirma {

   public function enviarFirma($ids, $date)
   {
      $errores = array();
      $errores = $this->verificarErrores($ids, $date);
      $sinErrores = array_diff($ids, $errores);
      if(!empty($sinErrores)) // $sinErrores contiene id's de cédulas sin errores
      {
         $msj = "";
         $idSol = "";
         $pasoAfirma = 0;
         $noPasoAfirma = 0;
         // $ids representa el conjunto de registros sin errores que se autorizan a firma
         $divIds = array_chunk($sinErrores, 100, true);
         foreach ($divIds as $key => $datos) {
            foreach ($datos as $id) {
               // actualizamos la tabla Selicitudes_sep en los campos status y fecha_lote
               $solicitud = SolicitudSep::find($id);
               if($solicitud->status == 1)
               {
                  // Primero Se crea el lote para recabar firmas
                  $this->createLote($date);
                  //Se busca y añade id del lote
                  $id_fecha_lote = DB::table('lotes_unam')->where('fecha_lote', $date)->first();
                  // Segundo, se actualiza la solicitud y se le asigna el ID del Lote
                  $solicitud->status = 2;
                  $solicitud->fecha_lote = $date;
                  $solicitud->fecha_lote_id = $id_fecha_lote->id;
                  $solicitud->save();
                  $pasoAfirma++;
                  $msj = "Se enviaron ".$pasoAfirma." registro(s) a proceso de firma.";
                  Session::flash('info', $msj);
               }
               else{
                  $noPasoAfirma ++;
                  $idSol .= $solicitud->id.",";
                  $msj = "Los (".$noPasoAfirma.") registros seleccionados con número(s) de solicitud (".$idSol.") ya habian sido enviados previamente.";
                  Session::flash('error', $msj);
               }
            }
            $date = strtotime ( '+2 second' , strtotime ( $date ) ) ;
            $date = date ( 'Y-m-d H:i:s' , $date );
         }
      }
      if(!empty($errores))
      {
         $msj = "Algunos registros seleccionados presentan errores. No es posible enviarlos a firma.";
         $msj .= "<p class='sangria'>Solicitudes:</p>";
         $msj .= "<p class='sangria'>";
         foreach ($errores as $value) {
            $msj .= $value.", ";
         }
         $msj .= "</p>";
         Session::flash('error', $msj);
      }
      return $msj;
   }
   /*Método para crear LotesUnam*/
   public function createLote($date)
   {
      $lote = LotesUnam::where('fecha_lote', $date)->get();
      if($lote->isEmpty())
      {
         $lote = new LotesUnam();
         $lote->fecha_lote = $date;
         $lote->save();
      }
   }
   /*Método que me devuelve todos los ids con errores*/
   public function verificarErrores($ids, $date){
      $sumaErrores = array();
      foreach ($ids as $id) {
         $error = SolicitudSep::find($id);
         if(!in_array("Sin errores", unserialize($error->errores))){
            array_push($sumaErrores, $id);
         }
      }
      return $sumaErrores;
   }

   //Verifica si el alumno tiene asignada una fecha de lote
   public function pasoAFirma($num_cta, $carrera){
     $paso = DB::connection('condoc_eti')
            ->select('select fecha_lote from solicitudes_sep WHERE num_cta = "'.$num_cta.'" AND cve_carrera = "'.$carrera.'"
                      AND fecha_lote IS NOT NULL');
     if(empty($paso)){
       $res = false;
     }
     else{
       $res = true;
     }
     return $res;
   }

   //Verifica si el alumno cuenta con todas las firmas
   /*public function firmasAutoridades($num_cta, $carrera){
     $firmas = DB::connection('condoc_eti')
              ->select('select * from solicitudes_sep WHERE firma1 != "" AND firma2 != "" AND firma3 != ""
                        AND num_cta = "'.$num_cta.'" AND cve_carrera = "'.$carrera.'"');
     if(empty($firmas)){
       $res = false;
     }
     else{
       $res = true;
     }
     return $res;
   }
   //Verifica si la información del alumno ya fue enviada a la SEP
   public function enviadoSEP($num_cta, $carrera){
     $enviados = DB::connection('condoc_eti')
                 ->select('select * from solicitudes_sep WHERE status = 7
                   AND num_cta = "'.$num_cta.'" AND cve_carrera = "'.$carrera.'"');
     if(empty($enviados)){
       $res = false;
     }
     else{
       $res = true;
     }
     return $res;
   }*/

   //Obtiene los motivos de cancelación de solicitudes (por el momento usaremos el catalogo exitente aparentemente relacionado)
   public function motivosCancelacion(){
     $motivos = Cancela::all();
     return $motivos;
   }

   //Verifica si la solicitud fue cancelada
   public function sCancelada($num_cta, $carrera){
     $cancelada = DB::connection('condoc_eti')
                 ->select('select * from solicitudes_canceladas WHERE num_cta = "'.$num_cta.'" AND cve_carrera = "'.$carrera.'"');
     if(empty($cancelada)){
       $res = false;
     }
     else{
       $res = true;
     }
     return $res;
   }

}
