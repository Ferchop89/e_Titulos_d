<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Imports\SepActual;
use Maatwebsite\Excel\Facades\Excel;
// use Excel;

use App\Http\Controllers\Controller;

class HojasCalculo extends Controller
{
   public function respuestaSep()
   {
      // dd(public_path(),storage_path());
      // El archivo se debe almacenar previamente en el directorio storage//app
      $array = Excel::toArray(new SepActual, 'HojaXLS.xls')[0];
      dd($array);
      return;
   }
}
