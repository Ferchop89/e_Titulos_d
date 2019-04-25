<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, RegistrosDGP};
use App\Imports\SepActual;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Zipper;
use ZipArchive;
// use Excel;

use App\Http\Controllers\Controller;

class HojasCalculo extends Controller
{
   public function respuestaSep($lote_dgp)
   {
      $filename = $this->readZip($lote_dgp);
      $path = storage_path()."/app/lotes_dgp_xls/descomprimido/$filename";
      //
      // $array = Excel::toArray(new SepActual, $filename, 'lotes')[0];
      // dd($array);
      // return;
      $reader = Excel::load($path, function($archivo){
         // dd($archivo);
            $result=$archivo->get();
            foreach ($result as $key => $value) {
               $table = new RegistrosDGP;
               $table->lote_dgp = substr($archivo->title, 21, strlen($archivo->title));
               $table->num_cta = substr($value->archivo, 15, 9);
               $table->ESTATUS = $value->estatus;
               $table->NOMBRE_ARCHIVO = $value->archivo;
               $table->DESCRIPCION = $value->descripcion;
               $table->FOLIO_CONTROL = $value->folio_control;
               // dd($table);
               $table->save();
            }
         })->get();
         
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

}
