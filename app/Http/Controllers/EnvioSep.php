<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Traits\Consultas\XmlCadenaErrores;
use App\Models\SolicitudSep;
use Carbon\Carbon;
use Session;
use Zipper;
use DB;
use DateTime;
use App\Http\Controllers\Admin\WSController;
use \FluidXml\FluidXml;


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
      // Conjunto de registros de solicitudes que integras un lote (fecha_lote).
      $solicitudes = SolicitudSep::where('status',4)
                                   ->where('fecha_lote',$fechaLote)
                                   ->get();
      // Las cedulas se integran a un archivo xml con sus tres firmas.
      if (count($solicitudes)==1) {
         // El archivo xml. Obtenemos el archivo XMLO en una cadena
         foreach ($solicitudes as $solicitud) {
            $cedula = $this->cadenaArchivo($solicitud,$folio);
         }
         // Un archivo envio de una sola cedula forma un archivo de extensión xml
         $nombreArchivo = 'xml/'.$folio.$solicitud->num_cta.'.xml';
         // guardamos un archivo xml por cada cédula del Lote.
         // posteriormente se integran en un solo archivo ZIP si el count(lote) > 1 o XML si count(lote) = 1
         $cedula->save($nombreArchivo);
      } else {
         // Archivo Zip contendiendo archivos xml
         foreach ($solicitudes as $solicitud) {
            $cedula = $this->cadenaArchivo($solicitud,$folio);
            // Un archivo envio de una sola cedula forma un archivo de extensión xml
            // Se le omite el numero de cuenta porque ya lo lleva en el folio interno
            $nombreArchivo = 'xml/'.$folio.'-'.$solicitud->num_cta.'.xml';
            // $nombreArchivo = 'xml/'.$folio.'xml';
            // guardamos un archivo xml por cada cédula del Lote.
            // posteriormente se integran en un solo archivo ZIP si el count(lote) > 1 o XML si count(lote) = 1
            $cedula->save($nombreArchivo);
            // Genera un archivo copia en xmlG que no contiene los sellos.
            // $this->XmldirGral($nodos, $folio, $solicitud->num_cta);
         }
      }
      // Generación del ZIP/XML y envio a la SEP (en ese orden)

      // GEneración del archivo de envio y envio mediante WS a la sep
      $this->generaZip($fechaLote);
      // $this->generaZipDirGral($fechaLote);
      // Consultamos el id de la fechaLote, porque es el valor de alta en lotes_dgp
      $fecha_lote_id = $this->obten_lote_id($fechaLote);
      $responseDGP = $this->enviaZipXml($fechaLote, $fecha_lote_id, $solicitud->num_cta);
      if(!empty($responseDGP))
      {
         // Registro del numero de lote que devuelve la DGP
         $wsDGP = new WSController();
         $wsDGP->registraLoteDgp($responseDGP,$fecha_lote_id);
         $msj = "Lote UNAM $fecha_lote_id enviado. Registrado en DGP con el lote: ".$responseDGP[0];
         Session::flash('success', $msj);
      }
      else {
         $msj = "Lote UNAM $fecha_lote_id enviado. Sin respuesta de DGP, intente enviar más tarde.";
         Session::flash('error', $msj);
      }
      // Copia de los Zip generados sin firmas para la Direccion generaLotes
      $this->generaZipDirGral($fechaLote);
      return redirect()->route('registroTitulos/firmas_progreso'); // registroTitulos/firmas_progreso  /firmas_progreso
   }

   public function cadenaArchivo($solicitud,$folio)
   {
      // Genera la cadena xlm para integrar el archivo ZIP o XML
      $sello1 = $this->firmaToSello($solicitud->firma1);
      // $sello2 = $this->firmaToSello($solicitud->firma2);
      // $sello3 = $this->firmaToSello($solicitud->firma3);
      // $datos del alumno que guarda en su registro y se utilizan para generar los nodos
      $datos = unserialize($solicitud->datos);
      // $nodos forma un arreglo de nodos de información para formar el XML
      // El folio contiene el Lote y el numero de cuenta.
      $xFolio = $folio.$solicitud->num_cta;
      // $nodos contiene en un arreglo la información de una cédula para convertirla en archivo xml

      // Buscamos el certificado responsable en la tabla lotes_unam
      $cer1 = DB::table('lotes_unam')
                  ->where('id',$solicitud->fecha_lote_id)
                  ->first();
      // dd($solicitud->fecha_lote_id,$cer1,'hola');
      $nodos = $this->IntegraNodosSep($xFolio,$datos,$sello1,$cer1->cert1);
      // $cedula contiene la versión XML y toma el arreglo de $nodos para formar el XML
      $cedula = $this->tituloXml($nodos);
      // dd('EnvioSep: cadenaArchivo',$nodos,$cedula);
      return $cedula;
   }

   public function generaZip($fechaLote)
   {
      // Generación del archivo Zip a partir de los archivos XML. Si el lote estan
      // formado por mas de un XML, se integran en un Zip y se borran los archivos XMLDiff\Base
      // Si el lote consta de un solo archivo, mantiene la extensión XML y no se borra ningún archivo.
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
   public function enviaZipXml($fechaLote, $fecha_lote_id, $num_cta)
   {
      // Registro del envio a la DGP (tabla lotes_DGP ), actualiza el status
      // del lote en la tabla solicitudes_sep y procede al envio del WS.
      $this->altaDPG($fecha_lote_id);
      // Actualiza el status de la tabla solicitudes_sep que pasa de '6' (firma rector) a '7' genera archivo XML
      // $this->statusXmlZip($fechaLote);
      
      // Envia por WS el Archivo XML o ZIP.
      $responseDGP = $this->xmlzipToDGP($fechaLote,$fecha_lote_id, $num_cta);
      return $responseDGP;
   }
   public function xmlzipToDGP($fechaLote, $fecha_lote_id, $num_cta="")
   {
      // Leemos el archivo para enviarlo mediante el WS a la DGP.
      $lote = Carbon::parse($fechaLote)->format('Ymdhis');
      // buscamos si existe el archivo en formato xml (una sola cedula) o zip (muchas cedulas)
      $archivoXml = 'xml/'.$lote.$num_cta.'.xml';
      $archivoZip = 'xml/'.$lote.'.zip';
      $fileXml = glob($archivoXml);
      $fileZip = glob($archivoZip);
      // Le proporcionamos la extensión adecuada al archivo.
      if ($fileXml!=[]) { // Se trata de un archivo xml
         $fileEnvioDGP = $fileXml;
      } elseif ($fileZip!=[]) { // Se trata de un archivo ZIP
         $fileEnvioDGP = $fileZip;
      } else {
         $fileEnvioDGP = '';
      }
      // Creamos una nueva instancia del Web Service que se encuentra en la clase WSController
      $wsDGP = new WSController();
      $responseDGP = $wsDGP->ws_Dgp_Carga($fileEnvioDGP,$fecha_lote_id);
      return $responseDGP;
   }

   public function actualizaDGP()
   {
      // Actualizamos las fechas faltantes en lotes_dgp como si hubieran sido enviadas
      // y quedan en la categoria status "sin respuesta"
      $query  = "Select distinct sol.fecha_lote_id, sol.fec_emision_tit, sol.fecha_lote, sol.status ";
      $query .= "from solicitudes_sep sol ";
      $query .= "join lotes_unam una ";
      $query .= "on una.fecha_lote = sol.fecha_lote ";
      $query .= "where sol.status = 7 ";
      $query .= "order by sol.fecha_lote ";
      $data = DB::connection('condoc_eti')->select($query);
      $registros = 0;
      foreach ($data as $regis) {
         // Se verifica la existencia, y la alta en la tabla lotes_dgp
         $fecha_lote_id = $this->obten_lote_id($data['fecha_lote']);
         $this->altaDPG($fecha_lote_id);
      }
   }
   public function altaDPG($fecha_lote_id)
   {
      // Verificamos que el registro no exista y lo damos de alta en lotes_dgp
      $existe = DB::connection('condoc_eti')->
                     table('lotes_dgp')->
                     where('lote_unam_id',$fecha_lote_id)->
                     exists();
      if (!$existe)
      {
         // Registramos este envio que no estaba presente
         DB::connection('condoc_eti')->
            table('lotes_dgp')->
            insert([ 'lote_unam_id' => $fecha_lote_id,
                     'user_id' => Auth::id(),
                     'lote_dgp' => 0,  // lote_dgp not null
                     'msj_carga' => 'Crea Lote' ,
                     'fecha_carga'=> null ,
                     'estatus' => 1, // estatus not nul
                     'msj_consulta' => null,
                     'fecha_consulta' => null,
                     'archivo_descarga' => '', // archivo not null
                     'ruta_descarga' => '',  // ruta not null
                     'msj_descarga' => null,
                     'fecha_descarga' => null,
                     'created_at' => Carbon::now()
         ]);
      }

   }
   public function obten_lote_id($fecha_lote)
   {
      // Si existe el lote, nos regresa el Id de lote.
      $query  = "Select id as fecha_lote_id ";
      $query .= "from lotes_unam ";
      $query .= "where fecha_lote = '$fecha_lote' ";
      $data = DB::connection('condoc_eti')->select($query);
      $id = (isset($data))? $data[0]->fecha_lote_id : null;
      return $id;
   }

   public function XmldirGral($nodos,$folio,$num_cta)
   {
      // Duplicado sin firmas
      $cedula = $this->tituloXml($nodos);
      //  Le borramos el atributo de sello para todos los responsables
      $cedula->query('firmaResponsable')->attr(['sello'=>'']);
      /// Si es un solo folio, le damos ext. xml si son mas de uno, le agragamos el nuermo de archivo consecutivo
      $nombreArchivo = 'xmlG/'.$folio.$num_cta.'.xml';
      // pasamos a disco el xml
      $cedula->save($nombreArchivo);
   }
   public function generaZipDirGral($fechaLote)
   {
      // Pasamos a formate yyyymmddhhmmss la fecha extendida de fechaLote para indenficar los archivos
      $folio = Carbon::parse($fechaLote)->format('Ymdhis');
      // dd($fechaLote,$folio,'generaZipDirGral');
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

   public function firmaToSello($firma)
   {
      // Transmamos la firma en sello. Eliminamos de la firma header y footer
      // $firma = str_replace('-----BEGIN PKCS7-----','',$firma);
      // $firma = str_replace('-----END PKCS7-----','',$firma);
      // codificamos en base 64 para elaborar el sello;
      // $sello = base64_encode($firma);
      $sello = $firma;

      //  regresamos el sello a partir de la f
      return $sello;
   }
   public function statusXmlZip($fechaLote)
   {
      //Se obtiene fecha y hora actual
      $hoy = new DateTime();
      $hoyf = $hoy->format("Y-m-d H:i:s");
      // Actualizamos el status de 06 (firma rector) a 07 generación del archivo xml o Zip (enviado DGP) y la fecha
      DB::table('solicitudes_sep')
                 ->where('fecha_lote', $fechaLote)
                 ->where('status',6)
                 ->update(['status' => 7, 'tit_fec_DGP' => $hoyf ]);
                 // ->update(['tit_fec_DGP' => $hoyf]);
      //Se actualiza la misma información en las bases de datos requeridas
      // Se comenta el código para hacer pruebas..
      // $n_cuentas = DB::connection('condoc_eti')->select("select num_cta from solicitudes_sep WHERE fecha_lote = '$fechaLote'");
      // foreach($n_cuentas as $c){
      //   $num_cta = substr($c->num_cta, 0, 8);
      //   $sql = DB::connection('sybase')->update('update Titulos set tit_fec_DGP = '.$hoyf.' where tit_ncta = '.$num_cta);
      // }
   }
   //Cancelación de Título Electrónico
   public function showCancelaAccion(Request $request)
   {
     $num_cta = $_POST['num_cta'];
     $cve_carrera = $_POST['cve_carrera'];
     $folioControl = "CBGE17028750"; //Especifico
     $motivo = $_POST['motivo'];
     $motivoCancela = DB::connection('condoc_eti')->select('select ID_MOTIVO_CAN from _cancelacionesSep WHERE id = '.$motivo);
     $user = Auth::user();

     $nodos = $this->integraNodosC($folioControl,$motivoCancela[0]->ID_MOTIVO_CAN,$user->name,$user->password);
     $cedula = $this->cancelaTituloXml($nodos);
     $nombreArchivo = 'xmlC/'.$folioControl.$num_cta.'.xml';
     $cedula->save($nombreArchivo);

     //----------------- ELIMINAR/ACTUALIZAR BASES DE DATOS CORRESPONDIENTES ----------------------
     DB::table('solicitudes_sep')
                ->where('num_cta', $num_cta)
                ->where('cve_carrera', $cve_carrera)
                ->update(['status' => 1 ]);//Se usa para evitar que se siga mostrando como posible cancelación

     $msj = "Se solicitó cancelación de Título Electrónico con n° de cuenta ".$num_cta;
     Session::flash('success', $msj);

     //return view('menus/accion_cancelacion', ['num_cta' => $num_cta, 'motivo' => $motivoCancela[0]->ID_MOTIVO_CAN]);
     return redirect()->route('cedula_cancelada', ['num_cta' => $num_cta]);
   }

}
