<?php

namespace App\Http\Traits\Consultas;
use DB;
use App\Models\{SolicitudSep, Web_Service, Alumno};
use App\Http\Controllers\Admin\WSController;

trait TitulosFechas {

  public function consultaTitulosDate($fecha)
  {
     $fechaPartes = explode("-", $fecha);
     $query = "SELECT tit_ncta+tit_dig_ver AS num_cta, dat_nombre, dat_sistema, tit_plancarr, tit_nivel, carrp_nombre, carrp_plan, plan_nombre, tit_fec_emision_tit, tit_libro, tit_foja, tit_folio FROM Titulos ";
     $query .= "INNER JOIN Datos ON Titulos.tit_ncta = Datos.dat_ncta AND Titulos.tit_plancarr = Datos.dat_car_actual ";
     $query .= "INNER JOIN Carrprog ON Datos.dat_car_actual = Carrprog.carrp_cve ";
     $query .= "INNER JOIN Planteles ON plan_cve = carrp_plan ";
     //Números de cuenta problematicos
     $query .= "WHERE ";
     // $query .= "(tit_ncta+tit_dig_ver)<>'503459419' AND ";
     // $query .= "(tit_ncta+tit_dig_ver)<>'503006594' AND ";
     $query .=       "(datepart(year,  tit_fec_emision_tit) = ".$fechaPartes[0]." AND ";
     $query .=       "datepart(month,  tit_fec_emision_tit) = ".$fechaPartes[1]." AND ";
     $query .=       "datepart(day,  tit_fec_emision_tit) = ".$fechaPartes[2].")";
     // consulta
     $datos = DB::connection('sybase')->select($query);
     // $cuenta = 1;
     // foreach ($datos as $key => $value) {
     //    // // if ($value->num_cta=='503459419') {
     //    // //
     //    // // }
     //    echo "<p>";
     //    // echo "orden ".$cuenta++.' cuenta: '.$value->num_cta.'   carrera:'.$value->tit_plancarr;
     //    echo $cuenta++.','.$value->num_cta.','.$value->tit_plancarr;
     //    echo "</p>";
     // }
     // dd("fin");
     return $datos;
  }
  public function consultaFotos($num_cta){
     $info = DB::connection('sybase_fotos')->table('Fotos')->where('foto_ncta', $num_cta)->get();
     // $info = '';
     if($info->isEmpty())
     // if($info == '')
     {
        $info = "<img src ='/images/sin_imagen.png' />";
     }
     else {
        if(count($info) >=1)
           $info = $info[count($info)-1];
        $info = '<img src="data:image/jpeg;base64,'.base64_encode( $info->foto_foto ).'" width="200" height="250" />';
     }
     return $info;
  }
  public function consultaDatos($cuenta, $verif){
     $info = DB::connection('sybase')->table('Datos')->select('dat_curp', 'dat_nombre')->where('dat_ncta', $cuenta)->where('dat_dig_ver', $verif)->orderBy('dat_fecha_alta', 'desc')->first();
     return $info;
  }
  public function consultaTitulos($cuenta, $verif){
     // 307255482
     $query = "SELECT tit_plancarr, tit_nivel, carrp_nombre, carrp_plan, plan_nombre, tit_fec_emision_tit, tit_libro, tit_foja, tit_folio FROM Titulos ";
     // $query = "SELECT tit_plancarr, tit_nivel, carrp_nombre FROM Titulos ";
     $query .= "INNER JOIN Datos ON Titulos.tit_ncta = Datos.dat_ncta AND Titulos.tit_plancarr = Datos.dat_car_actual ";
     $query .= "INNER JOIN Carrprog ON Datos.dat_car_actual = Carrprog.carrp_cve ";
     // $query .= "INNER JOIN Orientaciones ON Datos.dat_car_actual = Orientaciones.ori_plancarr ";
     // $query .= "AND Datos.dat_orientacion = Orientaciones.ori_orienta ";
     // $query .= "INNER JOIN Carreras_Profesiones ON convert(int, Orientaciones.ori_cve_profesiones)=Carreras_Profesiones.clave_carrera ";
     $query .= "LEFT JOIN Planteles ON plan_cve = carrp_plan ";
     $query .= "WHERE Titulos.tit_ncta = '".$cuenta."' ";
     $query .= "AND Titulos.tit_dig_ver = '".$verif."' ";

     $datos = DB::connection('sybase')->select($query);
     $info = array();
     foreach ($datos as $key => $value) {
        $info[$key] = (array)$value;
        $info[$key]['solicitud'] = $this->consultaSolicitudSep($cuenta.$verif, $value->tit_plancarr);
     }
     return $info;
  }

  public function consultaSolicitudSep($cuenta, $cveCarrera){
    $info = DB::connection('condoc_eti')->table('solicitudes_sep')->where('num_cta', $cuenta)->where('cve_carrera', $cveCarrera)->get();
        if($info->isEmpty())
        {
           $info = false;
        }
        else {
           $info = true;
        }
    return $info;
   }

   public function createSolicitudSep( $num_cta, $nombre, $nivel, $cve_carrera,
                                       $libro,$foja,$folio,$fechaEmision, $sistema,
                                       $user_id)
   {
      $cuentas = array(); /*El indice cero es alta, cambio, no se actualiza*/
      $dato = SolicitudSep::where('num_cta',$num_cta)->
                            where('cve_carrera',$cve_carrera)->first();
      // Realizamos la consulta que contiene datos y (si tiene) errores;
      $datosyerrores = $this->integraConsulta(substr($num_cta,0,8),substr($num_cta,8,1),$cve_carrera);
      if (!count($dato))
      {
         if($sistema == 2){
            $sistema =  "DGIRE";
         }
         else {
            $sistema = "SIAE";
         }
         //  No se encuentra el registro, se da de alta.
         $solicitud = new SolicitudSep();
         $solicitud->num_cta = $num_cta;
         $solicitud->nombre_completo = $nombre;
         $solicitud->nivel = $nivel;
         $solicitud->cve_carrera = $cve_carrera;
         $solicitud->fec_emision_tit = $fechaEmision;
         $solicitud->libro = $libro;
         $solicitud->foja  = $foja;
         $solicitud->folio  = $folio;
         $solicitud->datos = serialize($datosyerrores[0]);
         $solicitud->errores = serialize($datosyerrores[1]);
         $solicitud->paridad = serialize($datosyerrores[2]);
         $solicitud->sistema = $sistema;
         $solicitud->user_id = $user_id;
         $solicitud->ws_ati = $datosyerrores[3];
         $solicitud->save();
         $cuenta = array(1, 0, 0);
      } else
      {
         // Se encuentra el registro, solo se actualiza libro, foja, folio, datos y errores
         if ($dato->status == 1) {
            // El registro ya habia sido autorizado
            DB::table('solicitudes_sep')
                       ->where('id', $dato->id)
                       ->update(['libro'   => $libro,
                                 'foja'    => $foja,
                                 'folio'   => $folio,
                                 'datos'   => serialize($datosyerrores[0]),
                                 'errores' => serialize($datosyerrores[1]),
                                 'paridad' => serialize($datosyerrores[2]),
                                 'ws_ati'  => $datosyerrores[3]
                        ]);
            $cuenta = array(0, 1, 0);
         }
         else{
            $cuenta = array(0, 0, 1);
         }
      }
      return $cuenta;
   }
   public function createUserLogin($num_cta, $pass, $apellido1, $apellido2, $nombres, $curp, $correo){

      $usuario = new Alumno();
      $usuario->num_cta = $num_cta;
      $usuario->password = bcrypt($pass);
      $usuario->apellido1 = $apellido1;
      $usuario->apellido2 = $apellido2;
      $usuario->nombres = $nombres;
      $usuario->curp = $curp;
      $usuario->correo = $correo;
      $usuario->save();
   }
   public function consultaFechaNac($cuenta, $verif){
      $fecha_nac = DB::connection('sybase')->table('Datos')->select('dat_fec_nac')->where('dat_ncta', $cuenta)->where('dat_dig_ver', $verif)->orderBy('dat_fecha_alta', 'desc')->first();
      $fecha_nac = substr($fecha_nac->dat_fec_nac, 0, 10);
      if($fecha_nac == null)
      {
        $ws_SIAE = Web_Service::find(2);
        $identidad = new WSController();
        $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, $cuenta.$verif, $ws_SIAE->key);

        //Verificamos si el alumno se encuentra en SIAE
        if(isset($identidad) && (isset($identidad->mensaje) && $identidad->mensaje == "El Alumno existe")){
          //Obtenemos la fecha de nacimiento - SIAE
          $fecha_nac = $identidad->nacimiento;
          $fecha_nac = str_replace("/", "-", $fecha_nac);
        }else{//Obtener fecha de nacimiento de DGIRE
          $ws_DGIRE = new WSController();
          $ws_DGIRE = $ws_DGIRE->ws_DGIRE($num_cta);
          $info = $ws_DGIRE->respuesta->datosAlumnos->datosAlumno;
          $fecha_nac = $info->fechaNacimiento;
          $fecha_nac = str_replace("/", "-", $fecha_nac);
        }
      }
      return $fecha_nac;
   }
   public function consultaSistema($cuenta, $verif, $carrera, $nivel){
      $sistema = DB::connection('sybase')->table('Datos')
                                             ->select('dat_sistema')
                                                ->where('dat_ncta', $cuenta)
                                                ->where('dat_dig_ver', $verif)
                                                ->where('dat_car_actual', $carrera)
                                                ->where('dat_nivel', $nivel)
                                             ->orderBy('dat_fecha_alta', 'desc')
                                             ->first();
      return $sistema->dat_sistema;
   }
   public function consultaCURP($cuenta, $verif){
      $curp = DB::connection('sybase')->table('Datos')->select('dat_curp')->where('dat_ncta', $cuenta)->where('dat_dig_ver', $verif)->orderBy('dat_fecha_alta', 'desc')->first();
      $curp = $curp->dat_curp;
      // $curp = null;carreraNombre
      if($curp == null)
      {
        $ws_SIAE = Web_Service::find(2);
        $identidad = new WSController();
        $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, $cuenta.$verif, $ws_SIAE->key);

        //Verificamos si el alumno se encuentra en SIAE
        if(isset($identidad) && (isset($identidad->mensaje) && $identidad->mensaje == "El Alumno existe"))
        {
           //Obtenemos el curp - SIAE
          $curp = $identidad->curp;
        }
        else{ //Si no, obtenemos curp de DGIRE
          $ws_DGIRE = new WSController();
          $ws_DGIRE = $ws_DGIRE->ws_DGIRE($cuenta.$verif);
          $info = $ws_DGIRE->respuesta->datosAlumnos->datosAlumno;
          $curp = $info->curp;
        }

      }
      return $curp;
   }
   public function carreraNombre($clv_carrera){
      $query = "SELECT carrp_nombre FROM Carrprog ";
      $query .= "WHERE carrp_cve = '".$clv_carrera."'";
      $nombreCarrera = DB::connection('sybase')->select($query);
      if(!empty($nombreCarrera)){
         $nombreCarrera = $nombreCarrera[0]->carrp_nombre;
      }
      else {
         $nombreCarrera = "";
      }
     return $nombreCarrera;
   }

   public function titulosA($anio)
   {
      // estadisticas de titulos, enviados, no enviados y pendientes por año.

      // Año y mes para filtrar la consulta.
      $mysql        = "DATE_FORMAT(fec_emision_tit,'%Y-%m-%d') as emisionYmd,";
      $mysql       .= "SUM(CASE WHEN status <> 1 THEN 1 ELSE 0 END) AS enviadas, ";
      $mysql       .= "SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS noenviadas ";
      $mysqlWhere   = "DATE_FORMAT(fec_emision_tit, '%Y') >= '".$anio."'";
      $mysqlData    = DB::table('solicitudes_sep')
                    ->select(DB::raw($mysql))
                    ->whereRaw($mysqlWhere)
                    ->groupBy('fec_emision_tit')->get();

      $sybase        = " tit_fec_emision_tit AS emision, ";
      $sybase       .= " COUNT(*) AS total ";
      $sybasewhere   = " datepart(year,  tit_fec_emision_tit) >= ".$anio;
      $sybaseData  = DB::connection('sybase')
                    ->table('Titulos')
                    ->select(DB::raw($sybase))
                    ->whereRaw($sybasewhere)
                    ->groupBy('tit_fec_emision_tit')
                    ->get();

      // EL arreglo de referencia para fechas de emision de Titulos es Títulos, no solicitudes_sep
      // por lo que el ciclo exterior itera en ese arreglo.

      $resultado = array();
      // $resultado integra los dos conjuntos de datos provenientes de mysql y sybase
      if ($sybaseData!=[]) {
         // la informacion proveniente de títulos tiene mayor prioridad que la de solicitudes_sep
         $sSep = array();
         if ($mysqlData!=[]) {
           foreach ($mysqlData as $registros) {
               $sSep[$registros->emisionYmd] = ["enviadas"=>$registros->enviadas,'noenviadas'=>$registros->noenviadas];
           }
         }
        foreach ($sybaseData as $valores) {
            $llave = substr($valores->emision,0,10);
            if (array_key_exists($llave,$sSep)) {
               // La llave existe en los dos arreglos
               // Se contabilizan las pendientes que son las cédulas que estan en titulos pero no en solicitudes_sep
               $pendientes = $valores->total-$sSep[$llave]['enviadas']-$sSep[$llave]['noenviadas'];
               $resultado[$llave] = ['total'=>$valores->total,
                                     'enviadas'=>$sSep[$llave]['enviadas'],
                                     'noenviadas'=>$sSep[$llave]['noenviadas'],
                                     'pendientes'=> $pendientes];
            } else {
               // solo se tienen los registros de Titulos
               $resultado[$llave] = ['total'=>$valores->total,'enviadas'=>0,'noenviadas'=>0,'pendientes'=>$valores->total];
            }
        }
      }
      if ($resultado!=[]) {
        ksort($resultado);
      }

      return $resultado;
   }

   //Devuelve la información de la solicitud dada en caso de haber sido cancelada
   public function consultaCancelacionesS($num_cta){
     $res = DB::connection('condoc_eti')->select("select * from solicitudes_canceladas WHERE num_cta = '".$num_cta."'");
     return $res;
   }

   //Devuelve la descripción del motivo dado
   public function motivoCom($motivo){
     $mot = DB::connection('condoc_eti')->select("select DESCRIPCION_CANCELACION from _cancelacionesSep WHERE id = ".$motivo);
     return $mot;
   }

   //Devuelve las solicitudes del alumno seleccionado
   public function solicitud($num_cta){
     $sql = DB::connection('condoc_eti')->select("select * from solicitudes_sep where num_cta = '".$num_cta."'");
     return $sql;
   }

}
