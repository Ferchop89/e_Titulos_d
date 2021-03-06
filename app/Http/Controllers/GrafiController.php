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
        // dd($this->dataBarraTotal());
        // dd($data);
        // Inicializacion de los totales
        $totales['AutorizaAlumno']=$totales['NoAutorizaAlumno']=0;
        $totales['Titulos']=$totales['Pendientes']=$totales['AutorizaAlumnoCE']=$totales['AutorizaAlumnoSE']=0;
        $totales['NoAutorizaAlumnoCE']=$totales['NoAutorizaAlumnoSE']=$totales['RevisadasJtit']=0;
        $totales['AutorizadasJtit']=$totales['FirmadasDG']=$totales['EnviadasDGP']=$totales['DescargaDGP']=0;
        $totales['TEA']=$totales['TER']=0;
               // Generamos los Totales en un arreglo [0] Solicitudes y [1] Citatorios
        foreach ($data as $value) {
            $totales['Titulos']           = $totales['Titulos']           + $value['Titulos'];
            $totales['Pendientes']        = $totales['Pendientes']        + $value['Pendientes'];

            $totales['AutorizaAlumnoCE']  = $totales['AutorizaAlumnoCE']  + $value['AutorizaAlumnoCE'];
            $totales['AutorizaAlumnoSE']  = $totales['AutorizaAlumnoSE']  + $value['AutorizaAlumnoSE'];
            $totales['AutorizaAlumno']    = $totales['AutorizaAlumnoCE'] + $totales['AutorizaAlumnoSE'];

            $totales['NoAutorizaAlumnoCE']= $totales['NoAutorizaAlumnoCE']+ $value['NoAutorizaAlumnoCE'];
            $totales['NoAutorizaAlumnoSE']= $totales['NoAutorizaAlumnoSE']+ $value['NoAutorizaAlumnoSE'];
            $totales['NoAutorizaAlumno']  = $totales['NoAutorizaAlumnoCE'] + $totales['NoAutorizaAlumnoSE'];

            $totales['RevisadasJtit']     = $totales['RevisadasJtit']     + $value['RevisadasJtit'];
            $totales['AutorizadasJtit']   = $totales['AutorizadasJtit']   + $value['AutorizadasJtit'];
            $totales['FirmadasDG']        = $totales['FirmadasDG']        + $value['FirmadasDG'];
            $totales['EnviadasDGP']       = $totales['EnviadasDGP']       + $value['EnviadasDGP'];
            $totales['DescargaDGP']       = $totales['DescargaDGP']       + $value['DescargaDGP'];
            $totales['TER']               = $totales['TER']               + $value['TER'];
            $totales['TEA']               = $totales['TEA']               + $value['TEA'];
        }
        // Grafico pie
        $nombreGraf = 'Nombre Pie';
        $chart2 = $this->pie_Genera($aSel,substr($mSel,0,2),$totales,$nombreGraf,$paletaActual);

        // Titulo de la vista, Tablero de Control
        $title = 'Tablero Control Cédulas Electrónicas';
         // Renderizamos en la vista.
         $chart2 = $this->grafica();

         $c_a=0;$c_e=1; // No autoriza y tiene errores.
         $lista = $this->listaErrores($aSel,substr($mSel,0,2),$c_a,$c_e); // año y mes, $a_utoriza,$e_errores
         $listaHtml = $this->listaErroresHMTL($lista,'collapse1');

         $c_a=1;$c_e=1; // Autoriza y tiene errores.
         $lista1 = $this->listaErrores($aSel,substr($mSel,0,2),$c_a,$c_e); // año y mes, $a_utoriza,$e_errores
         $listaHtml1 = $this->listaErroresHMTL($lista1,'collapse2');

         $dataPendientes = $this->dataPendientes($aSel,substr($mSel,0,2));

         // Si no existen cuentas pendientes en todo el mes, la variable $pendientesHTML va a '' y no se despliega en la vista
         $pendientesHTML = $this->pendientesHTML($dataPendientes);

         $chart2=null;

         return view('graficas/cedulas', compact('chart1','chart2','a', 'aSel',
                                                 'mesHtml','data','title','totales',
                                                 'listaHtml','listaHtml1',
                                                 'pendientesHTML'));
    }

   public function listaErroresHMTL($lista,$collapse)
   {
      // Crea el html de la lista de errores de la lista y el collapse especifico para ese segmento.
      $html = ''; $listaErr = array();
      if ($lista!=[]) { // Si existe una lista de errores.
         // Iteramos para cada fecha para formar una sola lista de errores
         foreach ($lista as $key => $errores) {
            // iteramos para cada error
            foreach ($errores as $error => $valor) {
               if (isset($listaErr[$error])==null) { // no existe la llave en el arreglo, lo agregamos
                  $listaErr[$error] = $valor;
               } else { // Si existe la llave en el arreglo, le sumamos el valor para totalizar los errores
                  $listaErr[$error] += $valor;
               }
            }
         }
         // iteramos sobre el arreglo original para formar el HTML final y definitivo.
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
         $composite=        "<div id='$collapse' class='panel-collapse collapse'>";
         $composite .=       "<div class='divTableRow header'>";
         $composite .=         "<div class='divTableCell' style='background-color:white;'>";
         $composite .=              "<strong></strong>";
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
            $composite .=        "<div class='divTableCell' style='padding-left: 5%;'>";
            $composite .=           "<strong>".$error."</strong>";
            $composite .=        "</div>";
            foreach ($fechas as $fecha => $cantidad)  {
               $composite .=        "<div class='divTableCell'>";
               // $cantidad = ($cantidad==0)? "": $cantidad;
               $composite .=           "<strong>".$cantidad."</strong>";
               $composite .=        "</div>";
            }
            $composite .=        "<div class='divTableCell'>";
            // $total es la ultima columna, se agrega como la primera.
            // Se busca en el arreglo de totales y si se encuentra se coloca el valor.
            $total = (array_key_exists($error,$listaErr))? $listaErr[$error]: 0;
            $composite .=           "<strong>".$total."</strong>";
            $composite .=        "</div>";
            $composite .=      "</div>";
         }
         $composite .= "</div>";
      }
      // dd('hola', $salida, $composite);
      // if($collapse=='collapse2'){
      //    dd('hola', $composite);
      // }
      return $composite;
   }

   public function pendientesHTML($lista)
   {
      // Revisamos si no existen pendientes en ninguna de las fechas de emisión del titulo en el meses
      $cantidad = 0;
      foreach ($lista as $key => $value) {
         $cantidad .= count($value);
      }
      // Salimos no hubo numeros de cuenta pendientes en ninguna fecha de emisión de titulos del mes.
      if ($cantidad == 0) {
         return '';
      }
      $html = $composite = '';
      if ($lista!=[]) { // Si existe una lista de errores.
         // iteramos sobre el arreglo original para formar el HTML final y definitivo.

         // El numero de iteraciones verticales se refieren a la cantidad de cuentas.
         // Impresion del encabezado con fechas
         $composite=        "<div id='collapse3' class='panel-collapse collapse'>";
         $composite .=       "<div class='divTableRow header'>";
         $composite .=         "<div class='divTableCell'>";
         $composite .=              "<strong>Número de Cuenta</strong>";
         $composite .=         "</div>";
         foreach ($lista as $key1 => $value1) {
            $fechaDma = substr($key1,8,2) .'-'. substr($key1,5,2) .'-'. substr($key1,0,4);
            $composite .=         "<div class='divTableCell'>";
            $composite .=        "<strong>".$fechaDma."</strong>";
            $composite .=         "</div>";
            // numero de cuentas (renglones)
            $cuentas = count($value1);
         }
         $composite .=         "<div class='divTableCell'>";
         $composite .=              "<strong></strong>";
         $composite .=         "</div>";
         $composite .=       "</div>";
         // foreach ($salida as $error => $fechas) {
         for ($i=0; $i < $cuentas; $i++) {
            // error es la primera columna y nos especifica el error.
            $composite .=      "<div class='divTableRow'>";
            $composite .=        "<div class='divTableCell'>";
            $composite .=           "<strong>---></strong>";
            $composite .=        "</div>";
            foreach ($lista as $key2 => $value2)  {
               $composite .=        "<div class='divTableCell'>";
                  if ($value2[$i]!='') {
                     $composite .= $value2[$i];
                  } else {
                     $composite .= '';
                  }
               $composite .=        "</div>";
            }
            $composite .=        "<div class='divTableCell'>";
            $composite .=        "</div>";
            $composite .=      "</div>";
         }
         $composite .= "</div>";
         // $composite .=      "</a>";
      }
      return $composite;
   }

   public function listaErrores($anio,$mes,$c_a,$c_e)
   {
      // Elabora un analisis de todos los errores en una fecha en particular
      $anioMes = "'".$anio.str_pad($mes,2,0,STR_PAD_LEFT)."'";
      $mysql        = "DATE_FORMAT(fec_emision_tit,'%Y-%m-%d') as emisionYmd, errores";
      $mysqlWhere   = "DATE_FORMAT(fec_emision_tit, '%Y%m') = ".$anioMes." AND ";
      $mysqlWhere  .= "conAutorizacion=$c_a AND conErrores=$c_e";
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
      // Extendemos todas las fechas en el arreglo aunque no hayan sido cargadas.

      $fechas = $this->femisionxMes($anio,$mes);
      foreach ($fechas as $key=>$value) {
         if (!array_key_exists($key,$libro)) {
            $libro[$key]=array();
         }
      }
      ksort($libro,SORT_STRING);
      // dd('248',$libro,$fechas);
      // dd($libro);
      return $libro;
   }

   public function femisionxMes($anio,$mes){
      // Consultamos la tabla de titulos para obtener todas las fechas de emision de Titulos para un año-mes
      $sybase        = " DISTINCT tit_fec_emision_tit ";
      $sybasewhere   = " datepart(year,  tit_fec_emision_tit) = ".$anio." AND";
      $sybasewhere  .= " datepart(month, tit_fec_emision_tit) = ".$mes." ";
      $fechasAmes    = DB::connection('sybase')
                        ->table('Titulos')
                        ->select(DB::raw($sybase))
                        ->whereRaw($sybasewhere)
                        ->orderBy('tit_fec_emision_tit')
                        ->get();
      $fechas = array();
      foreach ($fechasAmes as $key => $value) {
         $fechas[substr($value->tit_fec_emision_tit,0,10)]=[];
      }
      return $fechas;
   }

    public function bar_Genera($anio,$mes,$Titulo,$ejeX,$ejeY,$paleta)
    {
        // Generamos el grafico de barra a partir de los datos

        // $arreglo contiene los datos de la consulta en arrenglo de llave-pair
        $arreglo = $this->dataBarra($anio,$mes);
        // El $arreglo se pasa a tres arreglos uno de etiquetas (dias de mes), otro de cedulas En proceso ($data1) y cedulas pendientes ($data2)
        $labels = $data1 = $data2 = $data3 = $data4 = $data5 = $data6 = $data7 = $data8 = $data9 = $data10 = array();
        foreach ($arreglo as $key => $value) {
          array_push($labels,$key);             // fecha de emison de titulo
          array_push($data1,$value['Titulos']); // titulos por fecha
          array_push($data2,$value['Pendientes']); // cedulas pendientes de trasferir de titulos a solicitudes_sep
          array_push($data3,$value['AutorizaAlumnoSE']); //
          array_push($data4,$value['AutorizaAlumnoCE']); //
          array_push($data3,$value['NoAutorizaAlumnoSE']); //
          array_push($data4,$value['NoAutorizaAlumnoCE']); //
          array_push($data4,$value['RevisadasJtit']); //
          array_push($data5,$value['AutorizadasJtit']); // cedulas sin errores que pasaron a firma pero no tiene aún firma alguna
          array_push($data6,$value['FirmadasDG']); // cedulas que tienen una o varias firmas
          array_push($data8,$value['EnviadasDGP']); // cedulas con todas las firmas y que ya han sido enviadas a la sep
          array_push($data9,$value['TEA']);     // Titulos Electrónicos Aprobados
          array_push($data10,$value['TER']);     // Títulos Electrónicos Rechazados
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
                         'Autorizadas'=>0,'Firmadas'=>0,'Enviadas'=>0];
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
                "label" => "Firmadas",
                'backgroundColor' => $paleta[5],
                'borderColor' => $paleta[5],
                "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                "pointHoverBackgroundColor" => "#fff",
                "pointHoverBorderColor" => "rgba(220,220,220,1)",
                'data' => $data6],
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
       $mysql       .= "SUM(CASE WHEN (conAutorizacion=1) THEN 1 ELSE 0 END ) AS AutorizaAlumno, ";
       $mysql       .= "SUM(CASE WHEN (conAutorizacion=0) THEN 1 ELSE 0 END ) AS NoAutorizaAlumno, ";
       $mysql       .= "SUM(CASE WHEN (conAutorizacion=1) AND (conErrores = 0) THEN 1 ELSE 0 END) AS  AutorizaAlumnoSE, ";
       $mysql       .= "SUM(CASE WHEN (conAutorizacion=1) AND (conErrores = 1) THEN 1 ELSE 0 END) AS  AutorizaAlumnoCE, ";
       $mysql       .= "SUM(CASE WHEN (status = 1) AND (conAutorizacion=0) AND (conErrores = 0) THEN 1 ELSE 0 END) AS NoAutorizaAlumnoSE, ";
       $mysql       .= "SUM(CASE WHEN (status = 1) AND (conAutorizacion=0) AND (conErrores = 1) THEN 1 ELSE 0 END) AS NoAutorizaAlumnoCE, ";
       $mysql       .= "SUM(CASE WHEN  status = 2 THEN 1 ELSE 0 END) AS RevisadasJtit, ";
       $mysql       .= "SUM(CASE WHEN  status = 3 THEN 1 ELSE 0 END) AS AutorizadasJtit, ";
       $mysql       .= "SUM(CASE WHEN  status = 4 THEN 1 ELSE 0 END) AS FirmadasDG, ";
       $mysql       .= "SUM(CASE WHEN  status = 5 THEN 1 ELSE 0 END) AS EnviadasDGP, "; // general lote dgp
       $mysql       .= "SUM(CASE WHEN  status = 6 THEN 1 ELSE 0 END) AS DescargaDGP, "; // descarga zip-xls
       $mysql       .= "SUM(CASE WHEN  status = 7 THEN 1 ELSE 0 END) AS TER, "; // titulo Electronico Rechazado
       $mysql       .= "SUM(CASE WHEN  status = 8 THEN 1 ELSE 0 END) AS TEA "; // titulo Electrónico Aceptado
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
                                                 "AutorizaAlumno"    => $registros->AutorizaAlumno,
                                                 "AutorizaAlumnoCE"  => $registros->AutorizaAlumnoCE,
                                                 "AutorizaAlumnoSE"  => $registros->AutorizaAlumnoSE,
                                                 "NoAutorizaAlumno"  => $registros->NoAutorizaAlumno,
                                                 "NoAutorizaAlumnoCE"=> $registros->NoAutorizaAlumnoCE,
                                                 "NoAutorizaAlumnoSE"=> $registros->NoAutorizaAlumnoSE,
                                                 "RevisadasJtit"     => $registros->RevisadasJtit,
                                                 "AutorizadasJtit"   => $registros->AutorizadasJtit,
                                                 "FirmadasDG"        => $registros->FirmadasDG,
                                                 "EnviadasDGP"       => $registros->EnviadasDGP,
                                                 "DescargaDGP"       => $registros->DescargaDGP,
                                                 "TEA"               => $registros->TEA,
                                                 "TER"               => $registros->TER];
            }
          }
          // Ordenamos el resultado en un arreglo adecuado para graficar
         foreach ($sybaseData as $valores) {
             $llave = substr($valores->emision,0,10);
             $key = explode('-',$llave); // cambiamos el formato de fecha de Ymd a dmY
             if (array_key_exists($llave,$sSep)) {
                // La llave existe en los dos arreglos
                // Se contabilizan las pendientes que son las cédulas que estan en titulos pero no en solicitudes_sep
                $pendientes = $valores->titulos-$sSep[$llave]['AutorizaAlumnoCE']-
                                                $sSep[$llave]['AutorizaAlumnoSE']-
                                                $sSep[$llave]['NoAutorizaAlumnoCE']-
                                                $sSep[$llave]['NoAutorizaAlumnoSE'];
                // almacenasmo en la fecha el resultado de todos los campos.
                $resultado[$key[2].'-'.$key[1].'-'.$key[0]] = ['Titulos'=>$valores->titulos,
                                                               'Pendientes'=> $pendientes,
                                                               'AutorizaAlumno'=>$sSep[$llave]['AutorizaAlumno'],
                                                               'AutorizaAlumnoCE'=>$sSep[$llave]['AutorizaAlumnoCE'],
                                                               'AutorizaAlumnoSE'=>$sSep[$llave]['AutorizaAlumnoSE'],
                                                               'NoAutorizaAlumno'=>$sSep[$llave]['NoAutorizaAlumno'],
                                                               'NoAutorizaAlumnoCE'=>$sSep[$llave]['NoAutorizaAlumnoCE'],
                                                               'NoAutorizaAlumnoSE'=>$sSep[$llave]['NoAutorizaAlumnoSE'],
                                                               'RevisadasJtit'=>$sSep[$llave]['RevisadasJtit'],
                                                               'AutorizadasJtit'=>$sSep[$llave]['AutorizadasJtit'],
                                                               'FirmadasDG'=>$sSep[$llave]['FirmadasDG'],
                                                               'EnviadasDGP'=>$sSep[$llave]['EnviadasDGP'],
                                                               'DescargaDGP'=>$sSep[$llave]['DescargaDGP'],
                                                               'TEA'=>$sSep[$llave]['TEA'],
                                                               'TER'=>$sSep[$llave]['TER']];

             } else {
                // solo se tienen los registros de Titulos
                $resultado[$key[2].'-'.$key[1].'-'.$key[0]] = ['Titulos'=>$valores->titulos,
                                                               'Pendientes'=>$valores->titulos,
                                                               'AutorizaAlumno'=>0,
                                                               'AutorizaAlumnoCE'=>0,
                                                               'AutorizaAlumnoSE'=>0,
                                                               'NoAutorizaAlumno'=>0,
                                                               'NoAutorizaAlumnoCE'=>0,
                                                               'NoAutorizaAlumnoSE'=>0,
                                                               'RevisadasJtit'=>0,
                                                               'AutorizadasJtit'=>0,
                                                               'FirmadasDG'=>0,
                                                               'EnviadasDGP'=>0,
                                                               'DescargaDGP'=>0,
                                                               'TEA'=>0,
                                                               'TER'=>0];
             }
         }
       }
       // Ordemanos el arreglo por key que contiene la fecha
       ksort($resultado);
       return $resultado;
    }

    public function resumenEnvios()
    {
      $limites = $this->limitesFechaEmision();
      $data = $this->dataBarraTotal($limites);
      $title = 'Resumen de envíos a la DGP';
      return view('graficas/cedulasResumen', compact('data','title','limites'));
   }


    public function dataBarraTotal($limites)
    {
      // Consulta de datos totales en las fechas de emisión de títulos contenidas en Solicitudes_sep

      // fechas limite de emision de títulos en la tabla solicitudes_sep
      $inicio = $limites['inicio']; $fin = $limites['fin'];
       if (!$inicio==null) {  // las fechas si existen
          $mysql        = "SUM(CASE WHEN (conAutorizacion=1) THEN 1 ELSE 0 END ) AS AutorizaAlumno, ";
          $mysql       .= "SUM(CASE WHEN (conAutorizacion=0) THEN 1 ELSE 0 END ) AS NoAutorizaAlumno, ";
          $mysql       .= "SUM(CASE WHEN (conAutorizacion=1) AND (conErrores = 0) THEN 1 ELSE 0 END) AS  AutorizaAlumnoSE, ";
          $mysql       .= "SUM(CASE WHEN (conAutorizacion=1) AND (conErrores = 1) THEN 1 ELSE 0 END) AS  AutorizaAlumnoCE, ";
          $mysql       .= "SUM(CASE WHEN (status = 1) AND (conAutorizacion=0) AND (conErrores = 0) THEN 1 ELSE 0 END) AS NoAutorizaAlumnoSE, ";
          $mysql       .= "SUM(CASE WHEN (status = 1) AND (conAutorizacion=0) AND (conErrores = 1) THEN 1 ELSE 0 END) AS NoAutorizaAlumnoCE, ";
          $mysql       .= "SUM(CASE WHEN  status = 2 THEN 1 ELSE 0 END) AS RevisadasJtit, ";
          $mysql       .= "SUM(CASE WHEN  status = 3 THEN 1 ELSE 0 END) AS AutorizadasJtit, ";
          $mysql       .= "SUM(CASE WHEN  status = 4 THEN 1 ELSE 0 END) AS FirmadasDG, ";
          $mysql       .= "SUM(CASE WHEN  status = 5 THEN 1 ELSE 0 END) AS EnviadasDGP, "; // general lote dgp
          $mysql       .= "SUM(CASE WHEN  status = 6 THEN 1 ELSE 0 END) AS DescargaDGP, "; // descarga zip-xls
          $mysql       .= "SUM(CASE WHEN  status = 7 THEN 1 ELSE 0 END) AS TER, "; // titulo Electronico Rechazado
          $mysql       .= "SUM(CASE WHEN  status = 8 THEN 1 ELSE 0 END) AS TEA "; // titulo Electrónico Aceptado
          $mysqlWhere   = "fec_emision_tit BETWEEN '$inicio' AND '$fin'";
          $mysqlData    = DB::table('solicitudes_sep')
                        ->select(DB::raw($mysql))->whereRaw($mysqlWhere)->get()[0];
                        // groupBy('fec_emision_tit')->get()[0];
         // Títulos totales entre dos fechas.
          $sybase        = "COUNT(*) AS titulos ";
          $sybasewhere   = "tit_fec_emision_tit BETWEEN '$inicio' AND '$fin'";
          $sybaseData  = DB::connection('sybase')
                        ->table('Titulos')
                        ->select(DB::raw($sybase))
                        ->whereRaw($sybasewhere)
                        ->get()[0];
            // armamos un arreglo único con las dos consultas
            $salida = array();
            $salida['Titulos'] = $sybaseData->titulos;
            foreach ($mysqlData as $key => $value) {
               $salida[$key] = $value;
            }
       }
       return $salida;
    }

    public function limitesFechaEmision()
    {
      // Fechas mayor y menor de emisión de títulos en el archivo Solicitudes_sep
      $dataFEI = DB::table('solicitudes_sep')->select('fec_emision_tit')->groupBy('fec_emision_tit')
                      ->orderBy('fec_emision_tit','ASC')->get();
      $cuentaFechas = count($dataFEI);
      $inicio = $fin = null; // Primera y última fechas de emisión de títulos
      if ($cuentaFechas>0) { // consulta vacía
          if ($cuentaFechas==1) {  // solo una fecha de emisión de títulos
             $inicio = $fin = $dataFEI['fec_emision_tit'];
          } else { // mas de una fecha de emisión de títulos
             $inicio = $dataFEI[0]->fec_emision_tit;
             $fin = $dataFEI[$cuentaFechas-1]->fec_emision_tit;
          }
      }
      $salida = array();
      $salida['inicio'] = $inicio;
      $salida['fin'] = $fin;
      return $salida;
   }

   public function dataPendientes($anio,$mes)
   {
      // Regresa los numeros de cuenta que no pasaron de la tabla Títulos a la tabla solicitudes_sep
      $anioMes = "'".$anio.str_pad($mes,2,0,STR_PAD_LEFT)."'";

      // Obtenemos las fechas de emision de título que ya fueron cargadas
      $mysql        = "fec_emision_tit";
      $mysqlWhere   = "DATE_FORMAT(fec_emision_tit, '%Y%m') = ".$anioMes."";
      $mysqlData    = DB::table('solicitudes_sep')
                    ->select(DB::raw($mysql))
                    ->whereRaw($mysqlWhere)
                    ->groupBy('fec_emision_tit')
                    ->get();
      // Fechas que condicionan los registros en la tabla Títulos
      $arrayIn = array();
      $in='';
      foreach ($mysqlData as $key => $value) {
         $fecha  = $value->fec_emision_tit;
         $item   = "'$fecha'";
         $in .= $item.',';
         array_push($arrayIn,$fecha);
      }
      $in = '('.substr($in,0,strlen($in)-1).')';
      // Set de registros en la Tabla Títulos
      $sybase        = " tit_ncta+tit_dig_ver as cuenta  ";
      $sybasewhere   = " tit_fec_emision_tit in $in ";
      $sybaseData    = DB::connection('sybase')
                        ->table('Titulos')
                        ->select(DB::raw($sybase))
                        ->whereRaw($sybasewhere)
                        ->orderBy('cuenta')
                        ->get();
      // La coleccion se convierte en arreglo para ejecutar una diferencia entre arreglos
      $arregloA = array();
      foreach ($sybaseData as $key => $value) {
         array_push($arregloA, $value->cuenta);
      }

      // Formato año-mes para recuperar las cédulas de una mes que incluya todas las fechas de emison de títulos
      $anioMes = "'".$anio.str_pad($mes,2,0,STR_PAD_LEFT)."'";

      $mysql        = "num_cta as cuenta";
      $mysqlWhere   = "DATE_FORMAT(fec_emision_tit, '%Y%m') = ".$anioMes."";
      $mysqlData    = DB::table('solicitudes_sep')
                    ->select(DB::raw($mysql))
                    ->whereRaw($mysqlWhere)
                    ->orderBy('cuenta')
                    ->get();
      // Coleccion de numeros de cuenta en Solicitudes Sep para un año-mes
     $arregloB = array();
     foreach ($mysqlData as $key => $value) {
        array_push($arregloB, $value->cuenta);
     }

     // Obtenemos la diferecia entre los dos arreglos para conocer las cédulas pendientes
     // Si no existen cedulas pendientes, genera un arreglo vacio
      $pendientes = array_diff($arregloA,$arregloB);

      // Consultamos la tabla de titulos para obtener todas las fechas de emision de Titulos
      $sybase        = " DISTINCT tit_fec_emision_tit ";
      $sybasewhere   = " datepart(year,  tit_fec_emision_tit) = ".$anio." AND";
      $sybasewhere  .= " datepart(month, tit_fec_emision_tit) = ".$mes." ";
      $fechasAmes    = DB::connection('sybase')
                        ->table('Titulos')
                        ->select(DB::raw($sybase))
                        ->whereRaw($sybasewhere)
                        ->orderBy('tit_fec_emision_tit')
                        ->get();
     // Armamos los numeros de cuenta como una condicion para limitar los resultados de la tab la titulos
     // Si no existen pendientes, no se agrega las cuentas no agregadas a la condicion where
      $inCtas = '';
      foreach ($pendientes as $key => $value) {
         $item   = "'$value'";
         $inCtas .= $item.',';
      }
      $inCtas = ($pendientes==[])? "('')" : '('.substr($inCtas,0,strlen($inCtas)-1).')';
      // Cédulas en titulos con su fecha de emisión
      $sybase        = " DISTINCT tit_fec_emision_tit, tit_ncta+tit_dig_ver as cuenta  ";
      $sybasewhere   = " tit_fec_emision_tit in $in ";
      $sybasewhere  .= " AND tit_ncta+tit_dig_ver in $inCtas ";
      $sybaseData    = DB::connection('sybase')
                        ->table('Titulos')
                        ->select(DB::raw($sybase))
                        ->whereRaw($sybasewhere)
                        ->orderBy('tit_fec_emision_tit')
                        ->get();
      $fechasCargadas = array();
      foreach ($sybaseData as $key => $value) {
         array_push($fechasCargadas, $value->tit_fec_emision_tit);
      }
      // Integramos cuentas y fechas
      $fechasyCedulas = array();
      // Recorremos las fechas del mes y le agregamos los numeros de cuenta existentes
      $integra = array();
      foreach ($fechasAmes as $key1 => $value1) {
         $cuentas = array();
         foreach ($sybaseData as $key2 => $value2) {
            if ($value1->tit_fec_emision_tit == $value2->tit_fec_emision_tit) {
               if ($value2->cuenta!='') {
                  array_push($cuentas,$value2->cuenta);
               }
            } else {
               // Si la fecha no ha sido cargada, mensaje 'sin carga', si la fecha ya se cargo, entonces vacio
               if (in_array($value1->tit_fec_emision_tit,$arrayIn)) {
                  array_push($cuentas,'');
               } else {
                  array_push($cuentas,'sin carga');
               }
            }
         }
         $integra[$value1->tit_fec_emision_tit] = $cuentas;
      }
      return $integra;
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
