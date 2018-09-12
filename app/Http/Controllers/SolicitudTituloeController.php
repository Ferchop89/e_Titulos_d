<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use DB;
use Session;
use Illuminate\Http\Request;
use App\Models\SolicitudSep;

class SolicitudTituloeController extends Controller
{
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
      // $foto = $this->consultaFotos($cuenta);
      $foto = null;
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
      $info = DB::connection('sybase_fotos')->table('Fotos')->where('foto_ncta', $num_cta)->get();
      if($info->isEmpty())
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
      $query = "SELECT tit_plancarr, tit_nivel, carrp_nombre, carrp_unidad, plan_nombre FROM Titulos ";
      // $query = "SELECT tit_plancarr, tit_nivel, carrp_nombre FROM Titulos ";
      $query .= "INNER JOIN Datos ON Titulos.tit_ncta = Datos.dat_ncta AND Titulos.tit_plancarr = Datos.dat_car_actual ";
      $query .= "INNER JOIN Carrprog ON Datos.dat_car_actual = Carrprog.carrp_cve ";
      // $query .= "INNER JOIN Orientaciones ON Datos.dat_car_actual = Orientaciones.ori_plancarr ";
      // $query .= "AND Datos.dat_orientacion = Orientaciones.ori_orienta ";
      // $query .= "INNER JOIN Carreras_Profesiones ON convert(int, Orientaciones.ori_cve_profesiones)=Carreras_Profesiones.clave_carrera ";
      $query .= "INNER JOIN Planteles ON plan_cve = carrp_unidad ";
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
      $info = DB::connection('condoc_eti')->table('solicitudes_sep')->where('cuenta', $cuenta)->where('cve_carrera', $cveCarrera)->get();
         if($info->isEmpty())
         {
            $info = false;
         }
         else {
            $info = true;
         }
      return $info;
   }
   public function existRequest($num_cta, $nombre, $carrera, $nivel){
      $solicitud = $this->consultaSolicitudSep($num_cta, $carrera);
      if($solicitud != false)
      {
         $msj = "Ya existe un registro del número de cuenta ".$num_cta." con la carrera ".$carrera;
         Session::flash('error', $msj);
      }
      else {
         $solicitud = new SolicitudSep();
         $solicitud->cuenta = $num_cta;
         $solicitud->nombre_compl = $nombre;
         $solicitud->nivel = $nivel;
         $solicitud->cve_carrera = $carrera;
         $solicitud->cve_registro_sep = '000000';
         $solicitud->user_id = Auth::id();
         $solicitud->save();
         $msj = "La solicitud con el número de cuenta ".$num_cta." y carrera ".$carrera." fue recibida";
         Session::flash('success', $msj);
      }
      return redirect()->route('eSearchInfo', ['numCta' => $num_cta]);
   }
   public function showPendientes(){
      $lists = SolicitudSep::paginate(10);
      $title = 'Solicitudes de Títulos Electrónicos Pendientes';
      return view('menus/lista_solicitudes', compact('title','lists'));
   }
}
