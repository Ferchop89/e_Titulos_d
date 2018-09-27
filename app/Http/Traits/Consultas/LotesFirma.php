<?php

namespace App\Http\Traits\Consultas;
use DB;
use App\Models\{SolicitudSep, Web_Service, Alumno, LotesUnam};
use App\Http\Controllers\Admin\WSController;
use Carbon\Carbon;

trait LotesFirma {

   public function enviarFirma($ids, $date)
   {
      foreach ($ids as $value) {
         $this->updateRequestSign($value, $date);
         $this->createLote($date);
         /*Cadena*/
      }
   }

   public function updateRequestSign($id, $date)
   {
      $solicitud = SolicitudSep::find($id);
      $solicitud->status = '01';
      $solicitud->fecha_lote = $date;
      $solicitud->save();
   }
   public function createLote($date)
   {
      $lote = new LotesUnam();
      $lote->fechaLote = $date;
      /*Cadena*/
      $lote->save();
   }



  // public function consultaDatos($cuenta, $verif){
  //    $info = DB::connection('sybase')->table('Datos')->select('dat_curp', 'dat_nombre')->where('dat_ncta', $cuenta)->where('dat_dig_ver', $verif)->orderBy('dat_fecha_alta', 'desc')->first();
  //    return $info;
  // }

  // public function consultaSolicitudSep($cuenta, $cveCarrera){
  //   $info = DB::connection('condoc_eti')->table('solicitudes_sep')->where('num_cta', $cuenta)->where('cve_carrera', $cveCarrera)->get();
  //       if($info->isEmpty())
  //       {
  //          $info = false;
  //       }
  //       else {
  //          $info = true;
  //       }
  //   return $info;
  //  }
}
