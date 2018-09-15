<?php

namespace App\Http\Traits\Consultas;
use DB;

trait TitulosFechas {

  public function share2()
  {
    return 'Esta cadena';
  }
  public function consultaTitulosDate($fecha)
  {
     $query = "SELECT tit_ncta+tit_dig_ver AS num_cta, dat_nombre, tit_plancarr, tit_nivel, carrp_nombre, carrp_plan, plan_nombre FROM Titulos ";
     $query .= "INNER JOIN Datos ON Titulos.tit_ncta = Datos.dat_ncta AND Titulos.tit_plancarr = Datos.dat_car_actual ";
     $query .= "INNER JOIN Carrprog ON Datos.dat_car_actual = Carrprog.carrp_cve ";
     $query .= "INNER JOIN Planteles ON plan_cve = carrp_plan ";
     $query .= "WHERE Titulos.tit_fec_emision_tit = '".$fecha."'";
     $query .= "AND tit_nivel != '07'";
     $datos = DB::connection('sybase')->select($query);
     return $datos;
  }

}
