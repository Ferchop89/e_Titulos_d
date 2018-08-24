<?php

namespace App\Http\Controllers;
use DB;

use Illuminate\Http\Request;


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
      $this->consultaTitulos($cuenta, $verif);
    }
    public function consultaTitulos($cuenta, $verif){
      $info = DB::connection('sybase')->table('Titulos')->where('tit_ncta', $cuenta)->where('tit_dig_ver', $verif)->get();
      // dd($info);
      return $info;
    }
    public function consultaDatos($cuenta, $verif){
      $info = DB::connection('sybase')->table('Datos')->where('dat_ncta', $cuenta)->where('dat_dig_ver', $verif)->get();

      // dd($info);
      return $info;
    }
}
