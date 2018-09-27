<?php

namespace App\Http\Controllers;
use \Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{LotesUnam};
use Illuminate\Support\Facades\Auth;

class FirmasCedulaController extends Controller
{
  public function showFirmasP(){
    $title = "Cédulas a Firmar";
    $rol = Auth::user()->roles()->get();
    $roles_us = array();
    foreach($rol as $actual){
      array_push($roles_us, $actual->nombre);
    }
    dd($roles_us);
    $lists = LotesUnam::all();
    $total = count($lists);
    $acordeon = $this->generaListas($lists);
    return view('menus/lista_firmarSolicitudes', compact('title', 'lists', 'total', 'acordeon'));
  }

  public function generaListas($lists){

  }

}
