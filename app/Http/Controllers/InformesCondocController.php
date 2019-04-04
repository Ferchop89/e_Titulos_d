<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
class InformesCondocController extends Controller
{
   public function materialesInforme()
   {
      // Informe de grados por tipo de material en que se elabora el título.

      // En la primera visita, tomamos el primero y ultimo dia del mes anterior
      // dd(request()->all());
      if (request()->all()==[]) { // es la primera vista|
         $start = new Carbon('first day of last month');
         $start->startOfMonth();
         $end = new Carbon('last day of last month');
         $end->endOfMonth()->format('Y-m-d');
         $inicio = $start->format('Y-m-d');
         $fin = $end->format('Y-m-d');
         // tipo o material
         $orden  = 'material';
      }
      $ordenHTML = $this->radioHTML($orden);
      $data   = $this->dataMaterialesGroup($inicio,$fin, $orden);
      $resumenHTML = $this->materialesHTML($data,$orden);
      $title = 'Material Títulos';
      return view('graficas/materialTitulos', compact('title','inicio','fin','resumenHTML','ordenHTML'));
   }

   public function materialesPost()
   {
      $data = request()->validate([
       'inicio' => 'required|date',
       'fin' => 'required|date|after_or_equal:inicio',
       ],[
       'inicio.required' => 'fecha inicial obligatoria',
       'fin.required' => 'fecha Final obligatorioa',
       'fin.after_or_equal' => 'La fecha final debe ser mayor o igual a la inicial',
      ]);
      $orden = request()->orden;
      $inicio = request()->inicio;
      $fin = request()->fin;
      // generalmos html y enviamos a pantalla
      $ordenHTML = $this->radioHTML($orden);
      $data   = $this->dataMaterialesGroup($inicio,$fin, $orden);
      $resumenHTML = $this->materialesHTML($data,$orden);
      $title = 'Material Títulos';
      return view('graficas/materialTitulos', compact('title','inicio','fin','resumenHTML','ordenHTML'));
   }

   public function radioHTML($orden)
   {
      $html  = "<div>";
      if ($orden=='nivel') {
         $html .= "<input type='radio' name='orden' value='nivel' checked> Nivel<br>";
         $html .= "<input type='radio' name='orden' value='material'> Material<br>";
      } else {
         $html .= "<input type='radio' name='orden' value='nivel'> Nivel<br>";
         $html .= "<input type='radio' name='orden' value='material' checked> Material<br>";
      }
      $html .=  "</div>";
      return $html;
   }

   public function materialesHTML ($data,$orden){
      $html = "<table class='table'>";
      $html .= "<tbody>";
      $html .= "<thead>";
      $html .= "<tr>";
      $html .= "<th>Nivel Escolar</th>";
      $html .= "<th>Tipo de Material</th>";
      $html .= "<th>01</th><th>02</th><th>03</th>";
      $html .= "<th>04</th><th>05</th><th>06</th>";
      $html .= "<th>07</th><th>08</th><th>09</th>";
      $html .= "<th>10</th><th>11</th><th>ST</th>";
      $html .= "<th>Total</th>";
      $html .= "</tr>";
      $html .= "</thead>";
      $cveSubtotal = ($orden=='nivel')? $data[0]->nivel:$data[0]->material;
      // subtotal por nivel o material
      $columnas = array('01'=>0,'02'=>0,'03'=>0,'04'=>0,'05'=>0,'06'=>0,
                        '07'=>0,'08'=>0,'09'=>0,'10'=>0,'11'=>0,'ST'=>0);
      // total de totales
      $totales  = array('01'=>0,'02'=>0,'03'=>0,'04'=>0,'05'=>0,'06'=>0,
                        '07'=>0,'08'=>0,'09'=>0,'10'=>0,'11'=>0,'ST'=>0);
      for ($i=0; $i < count($data); $i++) {
         $value = $data[$i];
         $columnas = $this->agregaXnivel($columnas,$value);
         $totales  = $this->agregaXnivel($totales,$value);
         $html .= '<tr>';
         $html .= "<td>$value->nivel</td>";
         $html .= "<td>$value->material</td>";
         $html .= "<td>$value->E01</td><td>$value->E02</td><td>$value->E03</td>";
         $html .= "<td>$value->E04</td><td>$value->E05</td><td>$value->E06</td>";
         $html .= "<td>$value->E07</td><td>$value->E08</td><td>$value->E09</td>";
         $html .= "<td>$value->E10</td><td>$value->E11</td><td>$value->ST</td>";
         $subtotal = $value->E01+$value->E02+$value->E03+$value->E04+$value->E05+
                     $value->E06+$value->E07+$value->E08+$value->E09+$value->E10+
                     $value->E11+$value->ST;
         $html .= "<td><strong>$subtotal</strong></td>";
         $html .= '</tr>';
         if ($i < (count($data)-1)) {
            $test = ($orden=='nivel')? $data[$i+1]->nivel: $data[$i+1]->material;
            if ($test!=$cveSubtotal) {
               // imprimimos el $cveSubtotal
               $html .= '<tr>';
               $html .= "<td></td>";
               $html .= "<td><strong>SUBTOTAL</strong></td>";
               $html .= "<td><strong>".$columnas['01']."</strong></td><td><strong>".$columnas['02']."</strong></td><td><strong>".$columnas['03']."</strong></td>";
               $html .= "<td><strong>".$columnas['04']."</strong></td><td><strong>".$columnas['05']."</strong></td><td><strong>".$columnas['06']."</strong></td>";
               $html .= "<td><strong>".$columnas['07']."</strong></td><td><strong>".$columnas['08']."</strong></td><td><strong>".$columnas['09']."</strong></td>";
               $html .= "<td><strong>".$columnas['10']."</strong></td><td><strong>".$columnas['11']."</strong></td><td><strong>".$columnas['ST']."</strong></td>";
               $html .= "<td><strong>{$this->sumaArreglo($columnas)}</strong></td>";
               $html .= '</tr>';
               // Actualizamos el valor de las columnas a subtotales cero
               $columnas = array('01'=>0,'02'=>0,'03'=>0,'04'=>0,'05'=>0,'06'=>0,
                                 '07'=>0,'08'=>0,'09'=>0,'10'=>0,'11'=>0,'ST'=>0);
               $cveSubtotal = ($orden=='nivel')? $data[$i+1]->nivel:$data[$i+1]->material;
            }
         }
      }
      // ultimo subtotal
      $html .= '<tr>';
      $html .= "<td></td>";
      $html .= "<td><strong>SUBTOTAL</strong></td>";
      $html .= "<td><strong>".$columnas['01']."</strong></td><td><strong>".$columnas['02']."</strong></td><td><strong>".$columnas['03']."</strong></td>";
      $html .= "<td><strong>".$columnas['04']."</strong></td><td><strong>".$columnas['05']."</strong></td><td><strong>".$columnas['06']."</strong></td>";
      $html .= "<td><strong>".$columnas['07']."</strong></td><td><strong>".$columnas['08']."</strong></td><td><strong>".$columnas['09']."</strong></td>";
      $html .= "<td><strong>".$columnas['10']."</strong></td><td><strong>".$columnas['11']."</strong></td><td><strong>".$columnas['ST']."</strong></td>";
      $html .= "<td><strong>{$this->sumaArreglo($columnas)}</strong></td>";
      $html .= "</tr>";

      // total
      $html .= '<tr>';
      $html .= "<td></td>";
      $html .= "<td><strong>TOTAL</strong></td>";
      $html .= "<td><strong>".$totales['01']."</strong></td><td><strong>".$totales['02']."</strong></td><td><strong>".$totales['03']."</strong></td>";
      $html .= "<td><strong>".$totales['04']."</strong></td><td><strong>".$totales['05']."</strong></td><td><strong>".$totales['06']."</strong></td>";
      $html .= "<td><strong>".$totales['07']."</strong></td><td><strong>".$totales['08']."</strong></td><td><strong>".$totales['09']."</strong></td>";
      $html .= "<td><strong>".$totales['10']."</strong></td><td><strong>".$totales['11']."</strong></td><td><strong>".$totales['ST']."</strong></td>";
      $html .= "<td><strong>{$this->sumaArreglo($totales)}</strong></td>";
      $html .= '</tr>';

      $html .= "</tbody>";
      $html .= '</table>';
      return $html;
   }

   public function sumaArreglo($arreglo)
   {
      $total = 0;
      foreach ($arreglo as $key => $value) {
         $total += $value;
      }
      return $total;
   }

   public function agregaXnivel($columnas,$value)
   {
      $columnas['01'] += $value->E01; $columnas['02'] += $value->E02;
      $columnas['03'] += $value->E03; $columnas['04'] += $value->E04;
      $columnas['05'] += $value->E05; $columnas['06'] += $value->E06;
      $columnas['07'] += $value->E07; $columnas['08'] += $value->E08;
      $columnas['09'] += $value->E09; $columnas['10'] += $value->E10;
      $columnas['11'] += $value->E11; $columnas['ST'] += $value->ST;
      return $columnas;
   }

   public function dataMaterialesGroup($inicio,$fin,$orden)
   {
      // Titulos y materiales de elaboración entre dos fechas.
      $selectSybase  = "Select ";
      $selectSybase  .= "(CASE exa_status_tipotit ";
      $selectSybase  .= "        WHEN '01' THEN '01. PERGAMINO' ";
      $selectSybase  .= "        WHEN '02' THEN '02. PAPEL APROBADO' ";
      $selectSybase  .= "        WHEN '03' THEN '03. PAPEL MENCHON' ";
      $selectSybase  .= "        WHEN '04' THEN '04. PIEL (IMITACION)'  ";
      $selectSybase  .= "        ELSE '99. ND' END) as material, exa_status_tipotit, ";
      $selectSybase  .= "(CASE exa_nivel ";
      $selectSybase  .= "        WHEN '01' THEN '01. INICIACIÓN UNIVERSITARIA' ";
      $selectSybase  .= "        WHEN '02' THEN '02. BACHILLERATO' ";
      $selectSybase  .= "        WHEN '03' THEN '03. TÉCNICO' ";
      $selectSybase  .= "        WHEN '04' THEN '04. TÉCNICO PROFESIONAL' ";
      $selectSybase  .= "        WHEN '05' THEN '05. LICENCIATURA' ";
      $selectSybase  .= "        WHEN '06' THEN '06. LICENCIATURA (SUA)' ";
      $selectSybase  .= "        WHEN '07' THEN '07. ESPECIALIDAD' ";
      $selectSybase  .= "        WHEN '08' THEN '08. MAESTRIA' ";
      $selectSybase  .= "        WHEN '09' THEN '09. DOCTORADO' 	 ";
      $selectSybase  .= "        ELSE 'ND' END) as nivel, exa_nivel, ";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when '01' THEN 1 ELSE 0 END ) AS 'E01',";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when '02' THEN 1 ELSE 0 END ) AS 'E02',";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when '03' THEN 1 ELSE 0 END ) AS 'E03',";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when '04' THEN 1 ELSE 0 END ) AS 'E04',";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when '05' THEN 1 ELSE 0 END ) AS 'E05',";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when '06' THEN 1 ELSE 0 END ) AS 'E06',";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when '07' THEN 1 ELSE 0 END ) AS 'E07',";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when '08' THEN 1 ELSE 0 END ) AS 'E08',";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when '09' THEN 1 ELSE 0 END ) AS 'E09',";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when '10' THEN 1 ELSE 0 END ) AS 'E10',";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when '11' THEN 1 ELSE 0 END ) AS 'E11',";
      $selectSybase  .= "	SUM ( case exa_tipo_examen when ''   THEN 1 ELSE 0 END ) AS 'ST' ";
      $selectSybase  .= "FROM Titulos ";
      $selectSybase  .= "JOIN Examenes ON";
      $selectSybase  .= "     exa_ncta     = tit_ncta AND ";
      $selectSybase  .= "     exa_plancarr = tit_plancarr AND ";
      $selectSybase  .= "     exa_nivel    = tit_nivel ";
      $selectSybase  .= "WHERE tit_fec_emision_tit BETWEEN '$inicio' AND '$fin' ";
      // orden de agrupamiento, primero nivel, luego tipo de material del elaboracion del titulo
      if ($orden=='nivel') {
         $selectSybase  .= "GROUP BY exa_nivel, exa_status_tipotit ";
         $selectSybase  .= "ORDER BY nivel, material  ";
      } else {  // primero tipo luego nivel.
         $selectSybase  .= "GROUP BY exa_status_tipotit, exa_nivel ";
         $selectSybase  .= "ORDER BY material, nivel  ";
      }

      $sybaseData = DB::connection('sybase')->select($selectSybase);
      return $sybaseData;
   }

}
