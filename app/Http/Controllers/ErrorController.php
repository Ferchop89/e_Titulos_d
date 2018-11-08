<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
   public function showError()
   {
      $title = "Fuera de servicio";
      $descripcion = "Temporalmente fuera de servicio, intente más tarde";
      return view('errors/error_info', compact('title', 'descripcion');
   }
}
