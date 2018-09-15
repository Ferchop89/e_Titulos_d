<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use DB;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\{SolicitudSep, Web_Service, AutTransInfo};
use App\Http\Controllers\Admin\WSController;

use App\Http\Traits\Consultas\TitulosFechas;

class SolicitudTituloeController extends Controller
{
   use TitulosFechas;

   public function searchAlum()
   {
        return view('/menus/search_eTitulos');
   }

   public function postSearchAlum(Request $request)
   {
        $request->validate([
            'num_cta' => 'required|numeric|digits:9'
        ],[
            'num_cta.required' => 'El campo es obligatorio',
            'num_cta.numeric' => 'El campo debe contener solo números',
            'num_cta.digits'  => 'El campo debe ser de 9 dígitos',
        ]);
        return redirect()->route('eSearchInfo', ['num_cta'=>$request->num_cta]);
   }
   public function showInfo($num_cta)
   {
      $cuenta = substr($num_cta, 0, 8);
      $verif = substr($num_cta, 8, 1);
      $foto = $this->consultaFotos($cuenta);
      $identidad = $this->consultaDatos($cuenta, $verif);
      if($identidad == null)
      {
         $msj = "No se encontraron registros en Títulos, con el número de cuenta ".$num_cta;
         Session::flash('error', $msj);
      }
      $trayectorias = $this->consultaTitulos($cuenta, $verif);
      return view('/menus/search_eTitulosInfo', ['numCta' => $num_cta,'foto' => $foto, 'identidad' => $identidad, 'trayectorias' => $trayectorias]);
    }
   public function consultaFotos($num_cta){
      // $info = DB::connection('sybase_fotos')->table('Fotos')->where('foto_ncta', $num_cta)->get();
      $info = '';
      // if($info->isEmpty())
      if($info == '')
      {
         $info = "<img src ='http://localhost:8000/images/sin_imagen.png' />";
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
      $query = "SELECT tit_plancarr, tit_nivel, carrp_nombre, carrp_plan, plan_nombre FROM Titulos ";
      // $query = "SELECT tit_plancarr, tit_nivel, carrp_nombre FROM Titulos ";
      $query .= "INNER JOIN Datos ON Titulos.tit_ncta = Datos.dat_ncta AND Titulos.tit_plancarr = Datos.dat_car_actual ";
      $query .= "INNER JOIN Carrprog ON Datos.dat_car_actual = Carrprog.carrp_cve ";
      // $query .= "INNER JOIN Orientaciones ON Datos.dat_car_actual = Orientaciones.ori_plancarr ";
      // $query .= "AND Datos.dat_orientacion = Orientaciones.ori_orienta ";
      // $query .= "INNER JOIN Carreras_Profesiones ON convert(int, Orientaciones.ori_cve_profesiones)=Carreras_Profesiones.clave_carrera ";
      $query .= "INNER JOIN Planteles ON plan_cve = carrp_plan ";
      $query .= "WHERE Titulos.tit_ncta = '".$cuenta."' ";
      $query .= "AND Titulos.tit_dig_ver = '".$verif."' ";
      $query .= "AND tit_nivel != '07'";
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
   public function existRequest($num_cta, $nombre,$carrera, $nivel){
      $solicitud = $this->consultaSolicitudSep($num_cta, $carrera);
      if($solicitud != false)
      {
         $msj = "Ya existe un registro del número de cuenta ".$num_cta." con la carrera ".$carrera;
         Session::flash('error', $msj);
      }
      else {
         $cve_carrera_sep = '000000';
         $fecha_nac = $this->consultaFechaNac(substr($num_cta, 0, 8), substr($num_cta, 8, 1));
         //$pass = str_replace("-", "", $fecha_nac);
         $year = substr($fecha_nac, 0, 4);
         $month = substr($fecha_nac, 5, 2);
         $day = substr($fecha_nac, 8, 2);
         $pass = $day.$month.$year;
         //dd($pass);
         $curp = $this->consultaCURP(substr($num_cta, 0, 8), substr($num_cta, 8, 1));
         // dd($fecha_nac, $pass);
         $this->createSolicitudSep($num_cta, $nombre, $nivel, $carrera, $cve_carrera_sep, Auth::id());

         $this->createUserLogin($num_cta, $pass, $nombre, $curp, $fecha_nac);
         $msj = "La solicitud con el número de cuenta ".$num_cta." y carrera ".$carrera." fue recibida";
         Session::flash('success', $msj);
      }
      return redirect()->route('eSearchInfo', ['numCta' => $num_cta]);
   }
   public function showPendientes(){
      $total = SolicitudSep::count();
      $lists = SolicitudSep::paginate(10);
      $title = 'Solicitudes en Evaluación para Firma';
      return view('menus/lista_solicitudes', compact('title','lists', 'total'));
   }
   public function createSolicitudSep($num_cta, $nombre, $nivel, $cve_carrera, $cve_carrera_sep, $user_id)
   {
      $solicitud = new SolicitudSep();
      $solicitud->num_cta = $num_cta;
      $solicitud->nombre_completo = $nombre;
      $solicitud->nivel = $nivel;
      $solicitud->cve_carrera = $cve_carrera;
      $solicitud->cve_registro_sep = $cve_carrera_sep;
      $solicitud->user_id = $user_id;
      $solicitud->save();
   }
   public function createUserLogin($num_cta, $pass, $nombre, $curp, $fecha_nac){
      $usuario = new AutTransInfo();
      $usuario->num_cta = $num_cta;
      $usuario->password = bcrypt($pass);
      $usuario->nombre_completo = $nombre;
      $usuario->curp = $curp;
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
   public function searchAlumDate()
   {
        return view('/menus/search_eTitulosDate');
   }
   public function postSearchAlumDate(Request $request)
   {
        $request->validate([
            'fecha' => 'required'
        ],[
            'fecha.required' => 'El campo es obligatorio',
            // 'num_cta.numeric' => 'El campo debe contener solo números',
            // 'num_cta.digits'  => 'El campo debe ser de 9 dígitos',
        ]);
        return redirect()->route('eSearchInfoDate', ['fecha'=>$request->fecha]);
   }
   public function showInfoDate($fecha){
      // dd($fecha);
      $datos = $this->consultaTitulosDate($fecha);
      // dd($datos);
      $registros=0;
      // $fechaView  = Carbon::createFromDate($fecha);
      $fechaView = Carbon::createFromFormat('Y-m-d', $fecha);
      foreach ($datos as $key => $value) {
         // dd($value);
         $cve_carrera_sep = '000000';
         $fecha_nac = $this->consultaFechaNac(substr($value->num_cta, 0, 8), substr($value->num_cta, 8, 1));
         //$pass = str_replace("-", "", $fecha_nac);
         $year = substr($fecha_nac, 0, 4);
         $month = substr($fecha_nac, 5, 2);
         $day = substr($fecha_nac, 8, 2);
         $pass = $day.$month.$year;
         //dd($pass);
         $curp = $this->consultaCURP(substr($value->num_cta, 0, 8), substr($value->num_cta, 8, 1));
         // dd($fecha_nac, $pass);

         $this->createSolicitudSep($value->num_cta, $value->dat_nombre, $value->tit_nivel, $value->tit_plancarr, $cve_carrera_sep, Auth::id());
         $registros++;
      }
      $msj = "Se solicitaron ".$registros." regitros con fecha ".$fechaView->format('d-m-Y');
      Session::flash('info', $msj);
      return view('/menus/search_eTitulosDate');
   }
   // public function consultaTitulosDate($fecha)
   // {
   //    $query = "SELECT tit_ncta+tit_dig_ver AS num_cta, dat_nombre, tit_plancarr, tit_nivel, carrp_nombre, carrp_plan, plan_nombre FROM Titulos ";
   //    $query .= "INNER JOIN Datos ON Titulos.tit_ncta = Datos.dat_ncta AND Titulos.tit_plancarr = Datos.dat_car_actual ";
   //    $query .= "INNER JOIN Carrprog ON Datos.dat_car_actual = Carrprog.carrp_cve ";
   //    $query .= "INNER JOIN Planteles ON plan_cve = carrp_plan ";
	//    $query .= "WHERE Titulos.tit_fec_emision_tit = '".$fecha."'";
   //    $query .= "AND tit_nivel != '07'";
   //    $datos = DB::connection('sybase')->select($query);
   //    return $datos;
   // }
}
