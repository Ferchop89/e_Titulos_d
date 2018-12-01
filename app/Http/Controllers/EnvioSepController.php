<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Web_Service;
use App\Http\Controllers\Admin\WSController;

class EnvioSepController extends Controller
{
   public function index(){
      $valid = new WSController();
      $respuesta = $valid->ws_DGP();
   }
}
