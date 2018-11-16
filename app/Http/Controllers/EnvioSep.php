<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\Consultas\XmlCadenaErrores;
use App\Models\SolicitudSep;
use Carbon\Carbon;
use Zipper;
use DB;

class EnvioSep extends Controller
{
   use XmlCadenaErrores;

   public function envio3Firmas()
   {
      // Generación de XML de cédulas de alumno correspondientes a un lote
      // Fecha del Lote.
      $fechaLote = request()->fecha_lote;
      // Fecha del Lote en formato comprimido para indentificacion y envio
      $folio = Carbon::parse($fechaLote)->format('Ymdhis');
      // Conjunto de solicitudes_sep
      $loteCedulasXml = ''; $cuenta = 1;
      // Conjunto de registros de solicitudes que pueden ser firmadas por SEP.
      // $solicitudes = SolicitudSep::where('status',2)
      $solicitudes = SolicitudSep::where('status',6)
                                   ->where('fecha_lote',$fechaLote)
                                   ->get();
      foreach ($solicitudes as $solicitud) {
         // Creamos un archivo por solicitudes
         // Transformamos las firmas en sello al eliminiar header, footer y codificar a 64 bytes.
         // $sello1 = $sello2 = $sello3 = ' ';
         $sello1 = $this->firmaToSello($solicitud->firma1);
         $sello2 = $this->firmaToSello($solicitud->firma2);
         $sello3 = $this->firmaToSello($solicitud->firma3);
         // $datos del alumno que guarda en su registro y se utilizan para generar los nodos
         $datos = unserialize($solicitud->datos);
         // $nodos forma un arreglo de nodos de información para formar el XML
         // El folio contiene el Lote y el numero de cuenta.
         $xFolio = $folio.'-'.$solicitud->num_cta;

         $nodos = $this->IntegraNodosSep($xFolio,$datos,$sello1,$sello2,$sello3);
         // $cedula contiene el objeto que contiene la versión XML y toma el arreglo de nodos para formar el XML
         $cedula = $this->tituloXml($nodos);
         /// Si es un solo folio, le damos ext. xml si son mas de uno, le agragamos el nuermo de archivo consecutivo
         $nombreArchivo = 'xml/'.$folio.'-'.$solicitud->num_cta.'.xml';
         // pasamos a disco el xml
         $cedula->save($nombreArchivo);

         // Genera un archivo copia en xmlG que no contiene los sellos.
         $this->XmldirGral($nodos, $folio, $solicitud->num_cta);
      }
      // // Genera archivo zip si el lote consiste en varios xml (cedulas)
      $this->generaZip($fechaLote);

      // genera una copia de los Zip generados sin firmas para la Direccion generaLotes
      $this->generaZipDirGral($fechaLote);

      // Actualiza el status de la tabla solicitudes_sep que pasa de '6' (firma rector) a '7' genera archivo XML
      $this->statusXmlZip($fechaLote);

      return redirect()->route('registroTitulos/firmas_progreso'); // registroTitulos/firmas_progreso  /firmas_progreso
   }
   public function XmldirGral($nodos,$folio,$num_cta)
   {
      // Duplicado sin firmas
      $cedula = $this->tituloXml($nodos);
      //  Le borramos el atributo de sello para todos los responsables
      $cedula->query('firmaResponsable')->attr(['sello'=>'']);
      /// Si es un solo folio, le damos ext. xml si son mas de uno, le agragamos el nuermo de archivo consecutivo
      $nombreArchivo = 'xmlG/'.$folio.'-'.$num_cta.'.xml';
      // pasamos a disco el xml
      $cedula->save($nombreArchivo);
   }

   public function generaZipDirGral($fechaLote)
   {
      // Pasamos a formate yyyymmddhhmmss la fecha extendida de fechaLote para indenficar los archivos
      $folio = Carbon::parse($fechaLote)->format('Ymdhis');
      // los integramos a un zip todos los archivos xml de un lote,  y luego los elimianmso del directorio
      $archivos = 'xmlG/'.$folio.'*.xml';
      $files = glob($archivos);
      // Si el lote consta de un solo xml no lo incluimos en un zip y permanece con extension 'sml' en el mismo directorio
      if (count($files)>1) {
         // El lote consta de mas de un xml por lo que se integran al zip.
         // En caso de existir previamente el archivo ZIP con ese lote, se borra para no agregar items a su contenido
         if (file_exists('xmlG/'.$folio.'.zip')) {
            unlink('xmlG/'.$folio.'.zip');
         }
         // Creamos y agregamos los archivos xml al archivo Zip.
         Zipper::make('xmlG/'.$folio.'.zip')->add($files)->close();
         // borramos los archivos xml del directorio porque ya se encuentran includios en el Zip
         foreach ($files as $file) {
            unlink($file);
         }
      }
   }
   public function generaZip($fechaLote)
   {
      // Pasamos a formate yyyymmddhhmmss la fecha extendida de fechaLote para indenficar los archivos
      $folio = Carbon::parse($fechaLote)->format('Ymdhis');
      // los integramos a un zip todos los archivos xml de un lote,  y luego los elimianmso del directorio
      $archivos = 'xml/'.$folio.'*.xml';
      $files = glob($archivos);
      // Si el lote consta de un solo xml no lo incluimos en un zip y permanece con extension 'sml' en el mismo directorio
      if (count($files)>1) {
         // El lote consta de mas de un xml por lo que se integran al zip.
         // En caso de existir previamente el archivo ZIP con ese lote, se borra para no agregar items a su contenido
         if (file_exists('xml/'.$folio.'.zip')) {
            unlink('xml/'.$folio.'.zip');
         }
         // Creamos y agregamos los archivos xml al archivo Zip.
         Zipper::make('xml/'.$folio.'.zip')->add($files)->close();
         // borramos los archivos xml del directorio porque ya se encuentran includios en el Zip
         foreach ($files as $file) {
            unlink($file);
         }
      }
   }
   public function firmaToSello($firma)
   {
      // Transmamos la firma en sello.

      // Eliminamos de la firma header y footer
      $firma = str_replace('-----BEGIN PKCS7-----','',$firma);
      $firma = str_replace('-----END PKCS7-----','',$firma);
      // codificamos en base 64 para elaborar el sello;
      $sello = base64_encode($firma);

      //  regresamos el sello a partir de la f
      return $sello;
   }
   public function statusXmlZip($fechaLote)
   {
      // Actualizamos el status de 05 (firma rector) a 06 generación del archivo xml o Zip
      DB::table('solicitudes_sep')
                 ->where('fecha_lote', $fechaLote)
                 ->where('status',6)
                 ->update(['status' => 7]);
   }
}
