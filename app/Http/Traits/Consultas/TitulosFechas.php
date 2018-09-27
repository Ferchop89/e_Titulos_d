<?php

namespace App\Http\Traits\Consultas;
use DB;
use App\Models\{SolicitudSep, Web_Service, Alumno};
use App\Http\Controllers\Admin\WSController;

trait TitulosFechas {

  public function consultaTitulosDate($fecha)
  {
     $query = "SELECT tit_ncta+tit_dig_ver AS num_cta, dat_nombre, tit_plancarr, tit_nivel, carrp_nombre, carrp_plan, plan_nombre, tit_fec_emision_tit, tit_libro, tit_foja, tit_folio FROM Titulos ";
     $query .= "INNER JOIN Datos ON Titulos.tit_ncta = Datos.dat_ncta AND Titulos.tit_plancarr = Datos.dat_car_actual ";
     $query .= "INNER JOIN Carrprog ON Datos.dat_car_actual = Carrprog.carrp_cve ";
     $query .= "INNER JOIN Planteles ON plan_cve = carrp_plan ";
     $query .= "WHERE Titulos.tit_fec_emision_tit = '".$fecha."'";
     // $query .= "AND tit_nivel != '07'";
     // dd($query);
     $datos = DB::connection('sybase')->select($query);
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
     $query .= "INNER JOIN Planteles ON plan_cve = carrp_plan ";
     $query .= "WHERE Titulos.tit_ncta = '".$cuenta."' ";
     $query .= "AND Titulos.tit_dig_ver = '".$verif."' ";
     // $query .= "AND tit_nivel != '07'";
     dd($query,'consultaDatos');
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
                                       $libro,$foja,$folio,$fechaEmision,
                                       $user_id)
   {
      $dato = SolicitudSep::where('num_cta',$num_cta)->
                            where('cve_carrera',$cve_carrera)->first();
      // Realizamos la consulta que contiene datos y (si tiene) errores;
      $datosyerrores = $this->integraConsulta(substr($num_cta,0,8),substr($num_cta,8,1),$cve_carrera);
      // dd($num_cta,$nombre,$cve_carrera,$dato->nombre_completo);
      if (!count($dato))
      {
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
         $solicitud->user_id = $user_id;
         $solicitud->save();
      } else
      {
         // Se encuentra el registro, solo se actualiza libro, foja, folio, datos y errores
         DB::table('solicitudes_sep')
                    ->where('id', $dato->id)
                    ->update(['libro'   => $libro,
                              'foja'    => $foja,
                              'folio'   => $folio,
                              'datos'   => serialize($datosyerrores[0]),
                              'errores' => serialize($datosyerrores[1])
                     ]);
      }
   }
   public function createUserLogin($num_cta, $pass, $apellido1, $apellido2, $nombres, $curp, $correo, $fecha_nac){

      $usuario = new Alumno();
      $usuario->num_cta = $num_cta;
      $usuario->password = bcrypt($pass);
      $usuario->apellido1 = $apellido1;
      $usuario->apellido2 = $apellido2;
      $usuario->nombres = $nombres;
      $usuario->curp = $curp;
      $usuario->correo = $correo;
      $usuario->fecha_nac = $fecha_nac;
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
   public function consultaCURP($cuenta, $verif){
      $curp = DB::connection('sybase')->table('Datos')->select('dat_curp')->where('dat_ncta', $cuenta)->where('dat_dig_ver', $verif)->orderBy('dat_fecha_alta', 'desc')->first();
      $curp = $curp->dat_curp;
      // $curp = null;
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


}
