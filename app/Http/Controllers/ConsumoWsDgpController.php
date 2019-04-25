<?php

namespace App\Http\Controllers;
use DB;
use App\Models\Web_Service;
use App\Http\Controllers\Admin\WSController;
use Session;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\Models\RegistrosDGP;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

use Illuminate\Http\Request;

class ConsumoWsDgpController extends Controller
{
   public function showEnvios()
   {
      $title = "Cedulas enviadas";
      $lotesDgp = DB::table('lotes_dgp')-> select('lote_dgp', 'fecha_carga', 'lote_unam_id')->where('estatus', 2)->paginate(10);
      $contenido = array();
      foreach ($lotesDgp as $key => $loteDgp) {
         $contenido[$loteDgp->lote_dgp] = DB::table('solicitudes_sep')->where('fecha_lote_id', $loteDgp->lote_unam_id)->count();
      }
      return view('menus.cedulasEnviadas', compact('title','lotesDgp', 'contenido'));
   }

   public function descarga($lote)
   {
      $wsDGP = new WSController();
      $response = $wsDGP->ws_Dgp_Descarga($lote);
      if(isset($response->titulosBase64))
      {
         Storage::disk('lotes_comp')->put("$lote.zip", $response->titulosBase64);
         $this->almacenaDescarga($response->numeroLote, $response->mensaje, $lote.".zip");
         $this->LecturaRespuestaXLS($lote, $response->mensaje);
         $msj = "Lote $lote descargado.";
         Session::flash('success', $msj);
      }
      else {
         $msj = "El lote $lote no pudo ser descargado. Intente más tarde.";
         Session::flash('error', $msj);
      }
      return redirect()->route('responseCedulas');
   }
   public function LecturaRespuestaXLS($lote_dgp, $mensaje)
   {
      $filename = $this->readZip($lote_dgp);
      $contador = 0;
      $registros = 0;
      $path = storage_path()."/app/lotes_dgp_xls/descomprimido/$filename";
      $reader = Excel::load($path, function($archivo)  use (&$contador, &$registros){
         $result=$archivo->get();
         $registros = count($result);
         $lote_dgp = substr($archivo->title, 21, strlen($archivo->title));
         foreach ($result as $key => $value) {
            $num_cta = substr($value->folio_control, -9);
            $table = RegistrosDGP::firstOrNew(['lote_dgp' => $lote_dgp, 'num_cta' => substr($value->archivo, 15, 9)]);
            $table->lote_dgp = $lote_dgp;
            $table->num_cta = $num_cta;
            $table->ESTATUS = $value->estatus;
            $table->NOMBRE_ARCHIVO = $value->archivo;
            $table->DESCRIPCION = $value->descripcion;
            $table->FOLIO_CONTROL = $value->folio_control;
            $table->save();
            $contador++;
            if($value->estatus == 1)
            {
               $this->registroExitoso($lote_dgp, $num_cta);
            }
            else {
               if(strpos($value->descripcion, "registrado") === false)
               {
                  $this->registroRechazado($lote_dgp, $num_cta);
               }
               else {
                  $this->registroExitoso($lote_dgp, $num_cta);
               }
            }
         }

      })->get();
      if($contador == $registros){
         $this->loteExitoso($lote_dgp, $mensaje);
      }
   }
   public function readZip($lote){
      $path = storage_path('app/lotes_dgp_xls/comprimido/').$lote.".zip";
      $descomprime = storage_path('app/lotes_dgp_xls/descomprimido');
      $zip =  new ZipArchive;
      $zip->open($path);
      $filename = $zip->getNameIndex(0);
      $zip->extractTo($descomprime);
      $zip->close();
      return $filename;
   }
   public function registroExitoso($lote_dgp, $num_cta){
      $lote_unam = DB::table('lotes_dgp')->select('lote_unam_id')->where('lote_dgp', $lote_dgp)->first();
      if(!empty($lote_unam))
      {
         $date = Carbon::now();
         DB::table('solicitudes_sep')
            ->where('num_cta', $num_cta)
            ->where('fecha_lote_id', $lote_unam->lote_unam_id)
            ->update([
               'status' => 8,
               'updated_at' => $date
            ]);
      }
   }
   public function registroRechazado($lote_dgp, $num_cta){
      $lote_unam = DB::table('lotes_dgp')->select('lote_unam_id')->where('lote_dgp', $lote_dgp)->first();
      if(!empty($lote_unam))
      {
         DB::table('solicitudes_sep')
            ->where('num_cta', $num_cta)
            ->where('fecha_lote_id', $lote_unam->lote_unam_id)
            ->update([
               'status' => 7,
               'updated_at' => Carbon::now()
            ]);
      }
   }
   public function loteExitoso($lote_dgp, $msjDescarga){
      DB::table('lotes_dgp')
         ->where('lote_dgp', $lote_dgp)
         ->update([
            'estatus' => 3,
            'msj_descarga' => $msjDescarga,
            'updated_at' => Carbon::now()
         ]);
   }
   public function almacenaDescarga($lote_dgp, $mensaje, $archivo){
      $lote_unam = DB::table('lotes_dgp')->select('lote_unam_id')->where('lote_dgp', $lote_dgp)->first();
      DB::table('solicitudes_sep')
         ->where('fecha_lote_id', $lote_unam->lote_unam_id)
         ->update([
            'status' => 6,
         ]);

      DB::table('lotes_dgp')
         ->where('lote_dgp', $lote_dgp)
         ->update([
            'archivo_descarga' => $archivo,
            'msj_descarga' => $mensaje,
            'fecha_descarga' => Carbon::now()
         ]);
   }
}
