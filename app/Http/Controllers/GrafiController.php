<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Procedencia;
use DB;

class GrafiController extends Controller
{
    //
    public function cedulas()
    {
        // Genera las graficas de solicitudes y citatorios

        // En su primera carga, toma varores de la base de datos, sino, selecciona y enviados por submit
        $a = $m = array(); // datos de años y meses para los menus de la vista
        // Cedulas por año
        $a = $this->cedulasAnio();
        // Ingreso de primera vez else se ingresa por segunda ocasión.
        if ((request()->anio_id==null)||request()->mes_id==null) {
           // primera vez que se ingresa; variables en cero si no existen datos.
          $aSel = ($a   !=[])? max($a): 0;
          $m    = ($a   !=[])? $this->cedulasMes($aSel): [];
          $mSel = ($m   !=[])? max($m): 0;
        } else {
          // Se ingresa mas de una vez, direccion contienen las fechas seleccionadas
          $aSel = request()->anio_id;
          $m = $this->cedulasMes($aSel);
          // se obtiene manualmente el maximo del mes, porque el ddmenu trae valores menores a 9 como caracteres
          $mSel = (array_key_exists(request()->mes_id,$m))? request()->mes_id : max($m);
        }
        // verificamos si el mes es cero (no aay registros), salimos del control sin Datos
        if ($aSel==0) {
           // Todos los parametros en cero.
           $title = 'Tablero Control Cédulas Electrónicas';
           $chart1='';$cart2='';$a=[];$aSel=0;$mesHtml='';$data=[];$totales=[];
           return view('graficas/cedulas', compact('chart1','chart2','a', 'aSel','mesHtml','data','title','totales'));
        }
        // La consulta si arroja resultados.
        $mesHtml = $this->mesHtml($m,$mSel);
        // patrones de colores
        $paleta['paleta1']  = ['#6ba083','#bcffa8','#5c3c10','#a2792f','#f7ca44','#c40018','#0960bd','#429ffd'];

        // $paleta['paletaxx'] = ['#','#','#','#'];

        $paletaActual = $paleta['paleta1']; //13
        // Grafico de barras
        $Titulo = 'SOLICITUDES DE CÉDULA ELECTRÓNICA';
        $ejeX   = 'FECHA DE EMISION DE TÍTULO';
        $ejeY   = 'CEDULAS';
        $chart1 = $this->bar_Genera($aSel,substr($mSel,0,2),$Titulo,$ejeX,$ejeY,$paletaActual);
        $data =   $this->dataBarra($aSel,substr($mSel,0,2));
        // Inicializacion de los totales
       $totales['Titulos']=$totales['Pendientes']=$totales['NoAutorConErr']=$totales['NoAutorSinErr']=0;
       $totales['Autorizadas']=$totales['EnFirma']=$totales['NoEnviadas']=$totales['Enviadas']=0;
        // Generamos los Totales en un arreglo [0] Solicitudes y [1] Citatorios
        foreach ($data as $value) {
            $totales['Titulos']        = $totales['Titulos']         + $value['Titulos'];
            $totales['Pendientes']     = $totales['Pendientes']      + $value['Pendientes'];
            $totales['NoAutorConErr']  = $totales['NoAutorConErr']   + $value['NoAutorConErr'];
            $totales['NoAutorSinErr']  = $totales['NoAutorSinErr']   + $value['NoAutorSinErr'];
            $totales['Autorizadas']    = $totales['Autorizadas']     + $value['Autorizadas'];
            $totales['EnFirma']        = $totales['EnFirma']         + $value['EnFirma'];
            $totales['NoEnviadas']     = $totales['NoEnviadas']      + $value['NoEnviadas'];
            $totales['Enviadas']       = $totales['Enviadas']        + $value['Enviadas'];
        }
        // Grafico pie
        $nombreGraf = 'Nombre Pie';
        $chart2 = $this->pie_Genera($aSel,substr($mSel,0,2),$totales,$nombreGraf,$paletaActual);

        // Titulo de la vista, Tablero de Control
        $title = 'Tablero Control Cédulas Electrónicas';
         // Renderizamos en la vista.
         $chart2 = $this->grafica();

         $lista = $this->listaErrores($aSel,substr($mSel,0,2));
         $listaHtml = $this->listaErroresHMTL($lista);

         return view('graficas/cedulas', compact('chart1','chart2','a', 'aSel','mesHtml','data','title','totales','listaHtml'));
    }

   public function listaErroresHMTL($lista)
   {
      $html = ''; $listaErr = array();
      if ($lista!=[]) { // Si existe una lista de errores.
         // Iteramos para cada fecha para formar una sola lista de errores
         foreach ($lista as $key => $errores) {
            // iteramos para cada error
            foreach ($errores as $error => $valor) {
               if (isset($listaErr[$error])==null) { // no existe la llave en el arreglo, lo agregamos
                  $listaErr[$error] = $valor;
               } else {
                  $listaErr[$error] += $valor;
               }
            }
         }
         asort($listaErr);
         if (array_key_exists('Sin errores/',$listaErr)) {
            unset($listaErr['Sin errores/']);
         }
         // iteramos sobre el arreglo original para formar el HTML final y definitivo.
         // dd($listaErr);
         $salida = array();
         foreach ($listaErr as $error => $valor) { // iteramos para cada fecha
            $html = array();
            foreach ($lista as $fecha => $errores) {
               $cantidad = (array_key_exists($error,$errores))? $errores[$error]: 0;
               $html[$fecha] = $cantidad;
            }
            $salida[$error] = $html;
         }
         // Impresion del encabezado con fechas
         // $composite =       "<a class='a-row' data-toggle='collapse' data-parent='#accordion' href='#collapse1'>";
         $composite=        "<div id='collapse1' class='panel-collapse collapse'>";
         $composite .=       "<div class='divTableRow header'>";
         $composite .=         "<div class='divTableCell'>";
         $composite .=              "<strong>Mensaje</strong>";
         $composite .=         "</div>";
         foreach ($lista as $key => $value) {
            $fechaDma = substr($key,8,2) .'-'. substr($key,5,2) .'-'. substr($key,0,4);
            $composite .=         "<div class='divTableCell'>";
            $composite .=        "<strong>".$fechaDma."</strong>";
            $composite .=         "</div>";
         }
         $composite .=         "<div class='divTableCell'>";
         $composite .=              "<strong>Total</strong>";
         $composite .=         "</div>";
         $composite .=       "</div>";
         foreach ($salida as $error => $fechas) {
            // error es la primera columna y nos especifica el error.
            $composite .=      "<div class='divTableRow'>";
            $composite .=        "<div class='divTableCell'>";
            $composite .=           "<strong>".$error."</strong>";
            $composite .=        "</div>";
            foreach ($fechas as $fecha => $cantidad)  {
               $composite .=        "<div class='divTableCell'>";
               // impresion de columnas de encabezado con fechas1136
               $composite .=           $cantidad;
                           $composite .=        "</div>";
            }
            $composite .=        "<div class='divTableCell'>";
            // $total es la ultima columna, se agrega como la primera.
            $total = (array_key_exists($error,$errores))? $listaErr[$error]: 0;
            $composite .=           $total;
            $composite .=        "</div>";
            $composite .=      "</div>";
         }
         $composite .= "</div>";
         // $composite .=      "</a>";
      }
      return $composite;
   }
   public function listaErrores($anio,$mes)
   {
      // Elabora un analisis de todos los errores en una fecha en particular
      $anioMes = "'".$anio.str_pad($mes,2,0,STR_PAD_LEFT)."'";
      $mysql        = "DATE_FORMAT(fec_emision_tit,'%Y-%m-%d') as emisionYmd, errores";
      $mysqlWhere   = "DATE_FORMAT(fec_emision_tit, '%Y%m') = ".$anioMes."";
      $datos        = DB::table('solicitudes_sep')
                    ->select(DB::raw($mysql))
                    ->whereRaw($mysqlWhere)->get();

      $cadena = ''; $libro = array(); // libro de errores por fecha de emisión de titulo
      foreach ($datos as $value) {
        $errores = unserialize($value->errores); $cadenaErrores='';
        asort($errores);
        foreach ($errores as $error) {
           $cadenaErrores .= $error.'/';
        }
        // Si existe la llave de error, incrementamos el valor anterior mas uno, sino, partimos del primer error.
        if (isset($libro[$value->emisionYmd][$cadenaErrores])!=null) {
            $libro[$value->emisionYmd][$cadenaErrores] += 1;
        } else {
            $libro[$value->emisionYmd][$cadenaErrores] = 1;
        }

      }
      return $libro;
   }

    public function bar_Genera($anio,$mes,$Titulo,$ejeX,$ejeY,$paleta)
    {
        // Generamos el grafico de barra a partir de los datos

        // $arreglo contiene los datos de la consulta en arrenglo de llave-pair
        $arreglo = $this->dataBarra($anio,$mes);
        // El $arreglo se pasa a tres arreglos uno de etiquetas (dias de mes), otro de cedulas En proceso ($data1) y cedulas pendientes ($data2)
        $labels = $data1 = $data2 = $data3 = $data4 = $data5 = $data6 = $data7 = $data8 = array();
        foreach ($arreglo as $key => $value) {
          array_push($labels,$key);             // fecha de emison de titulo
          array_push($data1,$value['Titulos']); // titulos por fecha
          array_push($data2,$value['Pendientes']); // cedulas pendientes de trasferir de titulos a solicitudes_sep
          array_push($data3,$value['NoAutorConErr']); // cedulas que contienen errores o sin errores que no han pasado a firma
          array_push($data4,$value['NoAutorSinErr']); // cedulas no contienen errores o sin errores que no han pasado a firma
          array_push($data5,$value['Autorizadas']); // cedulas sin errores que pasaron a firma pero no tiene aún firma alguna
          array_push($data6,$value['EnFirma']); // cedulas que tienen una o varias firmas
          array_push($data7,$value['NoEnviadas']); // cedulas con todas las firmas paro que aún no han sido enviadas a la sep
          array_push($data8,$value['Enviadas']); // cedulas con todas las firmas y que ya han sido enviadas a la sep
        }
        // Componemos el arreglo para el gráfico con etiquetas y datos
        // dd($labels,$data1,$data2,$Titulo,$ejeX,$ejeY);
        $chart = $this->bar_Grafico($labels,$data1,$data2,$data3,$data4,$data5,$data6,$data7,$data8,$Titulo,$ejeX,$ejeY,$paleta);

        return $chart;
    }

    public function pie_Genera($anio,$mes,$totales,$nombreGraf,$paleta)
    {
        // Generamos el grafico de pie a partir de los datos
        // Separamos los datos totales en dos arreglos: etiquetas(key) y Valores en el orden mismo de la grafica de barras
        $etiquetasVal = ['Titulos'=>0,'Pendientes'=>0,'NoAutorConErr'=>0,'NoAutorSinErr'=>0,
                         'Autorizadas'=>0,'EnFirma'=>0,'NoEnviadas'=>0,'Enviadas'=>0];
        $valores = array();
        foreach ($totales as $key => $value) {
           $etiquetasVal[$key] = $value;
        }
        // Separamos las etiquetas de los valores provenientes del arreglo ordenado de etiquetas y valores
        $etiquetas = array_keys($etiquetasVal);
        $valores   = array_values($etiquetasVal);
        $chart = $this->pie_Grafico($etiquetas,$valores,$nombreGraf,$paleta);
        return $chart;
    }

    public function bar_Opciones($Titulo,$ejeX,$ejeY)
    {
      $opciones = "{
         legend: {
                   display: true,
                   labels: {fontColor:'#000'}
         },
         title: {
                 display: true,
                 text: '$Titulo',
                 fontSize: 18
         },

         scales: {
            xAxes: [{
                    gridLines: {display:false},
                    stacked:false,
                    scaleLabel: {display: true, labelString: '$ejeX'}
                   }],
            yAxes: [{
                    gridLines: {display:true},
                    stacked:false,
                    scaleLabel: {display: true, labelString: '$ejeX'},
                    type: 'logarithmic',
                    ticks:{
                      min: 0,
                      callback: function(value, index, values) {return value;}
                    }
            }]
         }
      }";
      // dd($opciones);
      return $opciones;}

    public function bar_Grafico($labels,$data1,$data2,$data3,$data4,$data5,$data6,$data7,$data8,$Titulo,$ejeX,$ejeY,$paleta)
    {
      // grafico de barras de 2 conjuntos de datos
      $chartjs = app()->chartjs
        ->name('grafico')
        ->type('bar') //bar
        ->size(['width' => 900, 'height' => 380])
        ->labels($labels)
        ->datasets([
            [
                "label" => "Titulos",
                'backgroundColor' => $paleta[0],
                'borderColor' => $paleta[0],
                "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                "pointHoverBackgroundColor" => "#fff",
                "pointHoverBorderColor" => "rgba(220,220,220,1)",
                'data' => $data1],
            [
                "label" => "Pendientes",
                'backgroundColor' => $paleta[1],
                'borderColor' => $paleta[1],
                "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                "pointHoverBackgroundColor" => "#fff",
                "pointHoverBorderColor" => "rgba(220,220,220,1)",
                'data' => $data2],
            [
                "label" => "NoAutorizadas C/E",
                'backgroundColor' => $paleta[2],
                'borderColor' => $paleta[2],
                "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                "pointHoverBackgroundColor" => "#fff",
                "pointHoverBorderColor" => "rgba(220,220,220,1)",
                'data' => $data3],
            [
                "label" => "NoAutorizadas S/E",
                'backgroundColor' => $paleta[3],
                'borderColor' => $paleta[3],
                "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                "pointHoverBackgroundColor" => "#fff",
                "pointHoverBorderColor" => "rgba(220,220,220,1)",
                'data' => $data4],
            [
                "label" => "Autorizadas",
                'backgroundColor' => $paleta[4],
                'borderColor' => $paleta[4],
                "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                "pointHoverBackgroundColor" => "#fff",
                "pointHoverBorderColor" => "rgba(220,220,220,1)",
                'data' => $data5],
            [
                "label" => "EnFirma",
                'backgroundColor' => $paleta[5],
                'borderColor' => $paleta[5],
                "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                "pointHoverBackgroundColor" => "#fff",
                "pointHoverBorderColor" => "rgba(220,220,220,1)",
                'data' => $data6],
            [
                "label" => "NoEnviadas",
                'backgroundColor' => $paleta[6],
                'borderColor' => $paleta[6],
                "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                "pointHoverBackgroundColor" => "#fff",
                "pointHoverBorderColor" => "rgba(220,220,220,1)",
                'data' => $data7],
            [
                "label" => "Enviadas",
                'backgroundColor' => $paleta[7],
                'borderColor' => $paleta[7],
                "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                "pointHoverBackgroundColor" => "#fff",
                "pointHoverBorderColor" => "rgba(220,220,220,1)",
                'data' => $data8]
        ])
        ->options([]);

        // Generamos las opciones de la grafica de barras en una funcion aparte
        $opciones = $this->bar_Opciones($Titulo,$ejeX,$ejeY);
        $chartjs->optionsRaw($opciones);

        return $chartjs;
    }

    public function grafica (){
       // Ejemplo de grafica y opciones
       $chartjs = app()->chartjs
                ->name('barChartTest')
                ->type('bar')
                ->size(['width' => 900, 'height' => 380])
                ->labels(['Label x', 'Label y'])
                ->datasets([
                    [
                        "label" => "My First dataset",
                        'backgroundColor' => ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)'],
                        'data' => [69, 59]
                    ],
                    [
                        "label" => "My First dataset",
                        'backgroundColor' => ['rgba(255, 99, 132, 0.3)', 'rgba(54, 162, 235, 0.3)'],
                        'data' => [65, 12]
                    ]
                ])
                ->options([]);

                $chartjs->optionsRaw("{
                   legend: {
                      display:false
                      },
                   scales: {
                      xAxes:   [{
                                  gridLines: {
                                     display:false
                                  }
                               }],
                      yAxes: [{
                          ticks: {
                              // Include a dollar sign in the ticks
                              callback: function(value, index, values) {
                                  return '$' + value;
                              }
                          }
                      }]
                      }
                   }");
       return $chartjs;
    }

    public function pie_Grafico($etiquetas,$valores,$nombreGraf,$paleta)
    {
      // dd($etiquetas);
      $chartjs = app()->chartjs
        ->name('pieChartTest')
        ->type('doughnut') //
        ->size(['width' => 480, 'height' => 318])
        ->labels($etiquetas)
        ->datasets([
            [
                'backgroundColor' => $paleta,
                'hoverBackgroundColor' => $paleta,
                'data' => $valores
            ]
        ])
        ->options([]);
      return $chartjs;
    }

    public function dataBarra($anio,$mes)
    {
       // Consultas por mes y año de fechas de emision de titulo
       // Año y mes para filtrar la consulta.
       $anioMes = "'".$anio.str_pad($mes,2,0,STR_PAD_LEFT)."'";

       $mysql        = "DATE_FORMAT(fec_emision_tit,'%Y-%m-%d') as emisionYmd,";
       $mysql       .= "SUM(CASE WHEN (status = 1) AND NOT (errores LIKE '%Sin errores%') THEN 1 ELSE 0 END) AS NoAutorConErr, ";
       $mysql       .= "SUM(CASE WHEN (status = 1) AND     (errores LIKE '%Sin errores%') THEN 1 ELSE 0 END) AS NoAutorSinErr, ";
       $mysql       .= "SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS Autorizadas, ";
       $mysql       .= "SUM(CASE WHEN status between 3 and 5 THEN 1 ELSE 0 END) AS EnFirma, ";
       $mysql       .= "SUM(CASE WHEN status = 6 THEN 1 ELSE 0 END) AS NoEnviadas, ";
       $mysql       .= "SUM(CASE WHEN status = 7 THEN 1 ELSE 0 END) AS Enviadas ";
       $mysqlWhere   = "DATE_FORMAT(fec_emision_tit, '%Y%m') = ".$anioMes."";
       $mysqlData    = DB::table('solicitudes_sep')
                     ->select(DB::raw($mysql))
                     ->whereRaw($mysqlWhere)
                     ->groupBy('fec_emision_tit')->get();

       $sybase        = " tit_fec_emision_tit AS emision, ";
       $sybase       .= " COUNT(*) AS titulos ";
       $sybasewhere   = " datepart(year,  tit_fec_emision_tit) = ".$anio." AND";
       $sybasewhere  .= " datepart(month, tit_fec_emision_tit) = ".$mes." ";
       $sybaseData  = DB::connection('sybase')
                     ->table('Titulos')
                     ->select(DB::raw($sybase))
                     ->whereRaw($sybasewhere)
                     ->groupBy('tit_fec_emision_tit')
                     ->get();

       // EL arreglo de referencia para fechas de emision de Titulos es Títulos, no solicitudes_sep
       // por lo que el ciclo exterior itera en ese arreglo.

       $resultado = array();
       // $resultado integra los dos conjuntos de datos provenientes de mysql y sybase
       if ($sybaseData!=[]) {
          // la informacion proveniente de títulos tiene mayor prioridad que la de solicitudes_sep
          $sSep = array();
          if ($mysqlData!=[]) {
            foreach ($mysqlData as $registros) {
                $sSep[$registros->emisionYmd] = [
                                                 "NoAutorConErr"  => $registros->NoAutorConErr,
                                                 "NoAutorSinErr"  => $registros->NoAutorSinErr,
                                                 "Autorizadas"    => $registros->Autorizadas,
                                                 "EnFirma"        => $registros->EnFirma,
                                                 "NoEnviadas"     => $registros->NoEnviadas,
                                                 "Enviadas"       => $registros->Enviadas];
            }
          }
         foreach ($sybaseData as $valores) {
             $llave = substr($valores->emision,0,10);
             $key = explode('-',$llave); // cambiamos el formato de fecha de Ymd a dmY
             if (array_key_exists($llave,$sSep)) {
                // La llave existe en los dos arreglos
                // Se contabilizan las pendientes que son las cédulas que estan en titulos pero no en solicitudes_sep
                $pendientes = $valores->titulos-$sSep[$llave]['NoAutorConErr']-$sSep[$llave]['NoAutorSinErr']-
                              $sSep[$llave]['Autorizadas']-$sSep[$llave]['EnFirma']-$sSep[$llave]['NoEnviadas']-
                              $sSep[$llave]['Enviadas'];
                // almacenasmo en la fecha el resuptado de todos los campos.
                $resultado[$key[2].'-'.$key[1].'-'.$key[0]] = ['Titulos'=>$valores->titulos,
                                                               'Pendientes'=> $pendientes,
                                                               'NoAutorConErr'=>$sSep[$llave]['NoAutorConErr'],
                                                               'NoAutorSinErr'=>$sSep[$llave]['NoAutorSinErr'],
                                                               'Autorizadas'=>$sSep[$llave]['Autorizadas'],
                                                               'EnFirma'=>$sSep[$llave]['EnFirma'],
                                                               'NoEnviadas'=>$sSep[$llave]['NoEnviadas'],
                                                               'Enviadas'=>$sSep[$llave]['Enviadas'] ];

             } else {
                // solo se tienen los registros de Titulos
                $resultado[$key[2].'-'.$key[1].'-'.$key[0]] = ['Titulos'=>$valores->titulos,
                                                               'Pendientes'=>$valores->titulos,
                                                               'NoAutorConErr'=>0,
                                                               'NoAutorSinErr'=>0,
                                                               'Autorizadas'=>0,
                                                               'EnFirma'=>0,
                                                               'NoEnviadas'=>0,
                                                               'Enviadas'=>0];
             }
         }
       }
       // Ordemanos el arreglo por key que contiene la fecha
       ksort($resultado);
       return $resultado;
    }

    public function cedulasAnio()
    {
        // de las Solicitudes, obtenemos los años en un key-value array. ["2017"=>"2017","2108" => "2018"]
        $arreglo = array();
        $query =  'select DISTINCT DATE_FORMAT(fec_emision_tit, "%Y") as anio from solicitudes_sep ';
        $data = DB::select($query);
        foreach ($data as $value) {
          $arreglo[$value->anio] = $value->anio;
        }
        return $arreglo;
    }
    public function cedulasMes($anio)
    {
      // Genera las graficas de solicitudes y citatorios
        $meses = $arreglo = array();
        // $meses = ['01'=>'01. Enero','02'=>'02. Febrero','03'=>'03. Marzo','04'=>'04. Abril','05'=>'05. Mayo','06'=>'06. Junio',
        //           '07'=>'07. Julio','08'=>'08. Agosto','09'=>'09. Septiembre','10'=>'10. Octubre','11'=>'11. Noviembre','12'=>'12. Diciembre'];
        $meses = [1=>'01. Enero',2=>'02. Febrero',3=>'03. Marzo',4=>'04. Abril',
                  5=>'05. Mayo', 6=>'06. Junio',  7=>'07. Julio',8=>'08. Agosto',
                  9=>'09. Septiembre',10=>'10. Octubre',11=>'11. Noviembre',12=>'12. Diciembre'];

        // de las Solicitudes, obtenemos los meses en un key-value array ['05'=>'05', '06'=>'06']
        $arreglo = array();
        $query =  'select DISTINCT DATE_FORMAT(fec_emision_tit, "%m") as mes from solicitudes_sep ';
        $query .= 'where DATE_FORMAT(fec_emision_tit, "%Y")='.$anio;
        $data = DB::select($query);
        // creamos un arreglo key-value de los meses en los que hay información en solicitudes para un año determinado($anio)
        foreach ($data as $value) {
          // introducimos los valores del mes "02 Febrero" para aquellos meses disponibles en las Solicitudes
           $arreglo[intval($value->mes)] = $meses[intval($value->mes)];
        }
        return $arreglo;
    }
   public function mesHtml($arreglo,$seleccion)
   {
     ksort($arreglo);
     $html = "<select name='mes_id'>";
     foreach ($arreglo as $key => $value) {
        $actual = ($key==$seleccion)? 'selected':'';
        $html .= "<option value='".$key."' ".$actual.">".$value."</option>";
     }
     $html .= "</select>";
     return $html;
   }
}
