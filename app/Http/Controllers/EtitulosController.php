<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use DB;
use Session;
use Illuminate\Http\Request;
use App\Models\SolicitudSep;


class EtitulosController extends Controller
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
      // $query = "SELECT * FROM Titulos ";
      $query = "SELECT tit_plancarr, tit_nivel, carrp_nombre, carrp_unidad FROM Titulos ";
      $query .= "INNER JOIN Datos ON Titulos.tit_ncta = Datos.dat_ncta AND Titulos.tit_plancarr = Datos.dat_car_actual ";
      $query .= "INNER JOIN Carrprog ON Datos.dat_car_actual = Carrprog.carrp_cve ";
      $query .= "WHERE Titulos.tit_ncta = '".$cuenta."' ";
      $query .= "AND Titulos.tit_dig_ver = '".$verif."' ";
      $datos = DB::connection('sybase')->select($query);
      // dd($datos);
      $info = array();
      // dd($info);

      return $datos;
   }
   public function consultaSolicitudSep2($cuenta, $cveCarrera){
      // $info = DB::connection('condoc_eti')->table('solicitudes_sep')->get();
      // dd($info);
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
   public function consultaSolicitudSep($cuenta, $cveCarrera){
      // $info = DB::connection('condoc_eti')->table('solicitudes_sep')->get();
      // dd($info);
      $info = DB::connection('condoc_eti')->table('solicitudes_sep')->where('cuenta', $cuenta)->where('cve_carrera', $cveCarrera)->get();
         // if($info->isEmpty())
         // {
         //    $info = false;
         // }
         // else {
         //    $info = true;
         // }
      return $info;
   }
   public function existRequest($num_cta, $carrera, $nivel){
      $solicitud = $this->consultaSolicitudSep($num_cta, $carrera);
      if($solicitud->isNotEmpty())
      {
         $msj = "Ya existe un registro del número de cuenta ".$num_cta." con la carrera ".$carrera;
         Session::flash('error', $msj);
      }
      else {
         $solicitud = new SolicitudSep();
         $solicitud->cuenta = $num_cta;
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

}
