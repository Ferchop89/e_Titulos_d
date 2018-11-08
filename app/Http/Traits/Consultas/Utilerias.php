<?php

namespace App\Http\Traits\Consultas;

use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
trait Utilerias {
   public function conversionFechaEsp($fecha){
      $date = Carbon::parse($fecha)->formatLocalized('%A %d de %B de %Y, %H:%M:%S horas');
      return $date;
   }
}
