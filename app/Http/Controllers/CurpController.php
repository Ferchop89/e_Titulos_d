<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Admin\WSController;

use Illuminate\Http\Request;

class CurpController extends Controller
{
   public function validacionCurp(){
      $curp = 'PAEF890101HDFCSR07';
      $valido = new WSController();
      $valido = $valido->ws_RENAPO($curp);
      dd($valido);
   }
}
