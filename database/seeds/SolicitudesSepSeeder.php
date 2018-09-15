<?php

use App\Models\SolicitudSep;
use Illuminate\Database\Seeder;

class SolicitudesSepSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
      $solicitud = new SolicitudSep();
      $solicitud->num_cta = '307255482';
      $solicitud->nivel = '05';
      $solicitud->cve_carrera = '00531';
      $solicitud->cve_registro_sep = '000000';
      $solicitud->user_id = 1;
      $solicitud->save();
   }
}
