<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Corte;
use App\Models\Procedencia;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class ListadosController extends Controller
{
    public function Pdfs()
    {
        $corte = $_GET['corte']; // fecha de corte
        $lista = $_GET['lista']; // numero de lista a imprimir del corte
        $data = $this->lista_Corte($corte,$lista); // solicitudes de la lista y corte

        $rpp = 9; // registros por pagina del archivo PDF
        $limitesPDF = $this->paginas(count($data),$rpp); // limites de iteracion para registros del PDF
        $vista = $this->listaHTML($data,$corte,$lista,$limitesPDF); // generacion del content del PDF
        $view = \View::make('consultas.listasPDF', compact('vista'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('Corte_'.str_replace('.','-',$corte).'_lista '.$lista.'.pdf');
    }

    public function paginas($total_rpp,$rpp)
    {
        // Arreglo de registros por Pagina
        // $total_rpp Total de registros; $rpp Registros por pagina
        $entero = intdiv($total_rpp,$rpp);
        $residuo = $total_rpp % $rpp;
        $a_limites = array();
        // Si existe un residuo se aumenta un ciclo para cubrir los registros residuales
        $cuenta = ($residuo>0)? $entero+1: $entero;
        // dd($total_rpp,$rpp,$cuenta,$residuo);
        for ($i=0; $i < $cuenta ; $i++) {
          $inferior = $i * $rpp;
          $superior  = (($total_rpp-$inferior)<$rpp)? $inferior+$residuo: $inferior+$rpp;
          $limites = array($inferior,$superior);
          array_push($a_limites,$limites);
        }
        return $a_limites;
    }

    public function listaHTML($data,$corte,$lista,$limitesPDF)
    {
        // numero de hojas
        $composite = "";
        $paginas = count($limitesPDF);
        for ($i=0; $i < $paginas ; $i++)
        {
            $composite .= "<div id='details' class='clearfix'>";
            $composite .= "<table>";
            $composite .= "<tr>";
            $composite .= "<td><img src='images/escudo_unam_solow.svg' alt=''></td>";
            $composite .= "<td>";
            $composite .= "<h3>UNIVERSIDAD NACIONAL AUTONOMA DE MÉXICO</h3>";
            $composite .= "<h3>Dirección General de Administración Escolar</h3>";
            $composite .= "<h3>Departamente de Revisión de Estudios Profesionales</h3>";
            $composite .= "<h3>Listado de Solicitud de Expedientes. Corte:".str_replace('.','/',$corte)."-".$lista."</h3>";
            $composite .= "</td>";
            $composite .= "</tr>";
            $composite .= "</table>";
            $composite .= "<div id='invoice'>";
            // $composite .=     "<h1>CORTE: ".$corte."</h1>";
            $composite .= "</div>";
            $composite .= "</div>";
            $composite .= "<table border='0' cellspacing='0' cellpadding='0'>";
            $composite .= "<thead>";
            $composite .= "<tr>";
            $composite .= "<th scope='col'><strong>#</strong></th>";
            $composite .= "<th scope='col'><strong>NO. CTA.</strong></th>";
            $composite .= "<th scope='col'><strong>NOMBRE</strong></th>";
            $composite .= "<th scope='col'><strong>ESCUELA O FACULTAD</strong></th>";
            $composite .= "<th scope='col'><strong>FECHA; HORA</strong></th>";
            $composite .= "</tr>";
            $composite .= "</thead>";
            $composite .= "<tbody>";
            for ($x=$limitesPDF[$i][0]; $x < $limitesPDF[$i][1] ; $x++)
            {
                $composite .= "<tr>";
                $composite .= "<th scope='row'>".($x+1)."</th>";
                $composite .= "<td>".$data[$x]->cuenta."</td>";
                $composite .= "<td>".$data[$x]->nombre."</td>";
                $composite .= "<td>".$data[$x]->procedencia."</td>";
                $composite .= "<td>".explode('-',explode(' ',$data[$x]->created_at)[0])[2].'-'
                               .explode('-',explode(' ',$data[$x]->created_at)[0])[1].'-'
                               .explode('-',explode(' ',$data[$x]->created_at)[0])[0].'; '
                               .explode(' ',$data[$x]->created_at)[1]."</td>";
                $composite .= "</tr>";
            }
            $composite .= "</tbody>";
            $composite .= "</table>";
            $composite .= "<footer><strong>";
            $composite .= "Hoja ".($i+1)." de ".$paginas;
            $composite .= "   --   ";
            $composite .= "fecha ".date('d/m/Y');
            $composite .= "<strong></footer>";
            $composite .= (($i+1)!=$paginas)? "<div class='page-break'></div>": "";
        }
        return $composite;
    }

    public function listas()
    {
      $reqFecha = request()->input('datepicker');

      if ($reqFecha==null) {
        $corte = $this->ultimoCorte();
      } else {
        $vfecha = explode("/",$reqFecha);
        $xfecha = $vfecha[0].".".$vfecha[1].".".$vfecha[2];
        $corte = $xfecha;
      }

      if (isset($_GET['btnLista'])) {
        $afecha = explode('/',$_GET['datepicker']); // Cambiar fecha de formate mm/dd/aaaa a dd.mm.aaaa
        $corte = $afecha[0].'.'.$afecha[1].'.'.$afecha[2];
        $lista = $_GET['btnLista'];
        return redirect()->route('imprimePDF',compact('corte','lista'));
      } else {
        $data = $this->listasxCorte($corte);
        $nListas = count($data);
        $xListas = $this->acordionHtml($data,$corte);
        $xProcede  = $this->procedencias($data);
        return view('consultas.listasRev',[
                'data'=>$data,
                'listas'=>$xListas,
                'corte' =>$corte,
                'nListas' => $nListas, // la consulta no arrojo listas
                'procede' => $xProcede
                ]);
      }
    }
    public function procedencias($data)
    {
      // Procedimiento para determinar si las listas provienen de una escuelas
      $procede = array();
      for ($i=0; $i <count($data) ; $i++) {
        $escuela = $data[$i][0]->procedencia; $cuenta = 0;
        for ($x=0; $x <count($data[$i]) ; $x++) {
          $cuenta = ($escuela==$data[$i][$x]->procedencia)? $cuenta+1: $cuenta;
        }
        $procede[$i] = ($cuenta==count($data[$i]))? 'Escuela: '.$escuela : "";
      }
      return $procede;
    }

    public function acordionHtml($data,$corte)
    {
      // Elaboracion del acordion con listas.
      $composite = "<div class='panel-group' id='accordion'>";
      for ($i=0; $i < count($data) ; $i++) {
        $x_list = $i + 1;

        $composite .=    "<div class='panel panel-default'>";
        $composite .=         "<div class='panel-heading'>";
        $composite .=            "<h4 class='panel-title'>";
        $composite .=              "<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$x_list."'>";
        $composite .=              "Corte".$corte."; Lista ".$x_list.":    ".count($data[$i])." solicitudes.";
        $composite .=              "</a>";
        $composite .=            "</h4>";
        $composite .=         "</div>";
        // solo el primer listado se despliega, los demas se colapsan.
        $collapse   =       (count($data)==1)? 'in': '';
        $composite .=       "<div id='collapse".$x_list."' class='panel-collapse collapse ".$collapse."'>";
        $composite .=       "<div class='panel-body'>";
        // formularios para la impresion de listas
        $composite .=       "{{ Form::open(['action' => 'ListadosController@pdf', 'method' => 'PUT']) }} ";
        $composite .=           "<button name='btnLista_".$x_list."' type='submit' value='".$x_list."' class='btn btn-danger btn-xs'>PDF</button>";
        $composite .=       "{{ Form::close() }}";
        // Tabla con la lista de solicitudes
        $composite .=        "<div class='table-responsive'>";
        $composite .=         "<table class='table table-striped'>";
        $composite .=           "<thead>";
        $composite .=             "<tr>";
        $composite .=               "<th scope='col'>#</th>";
        $composite .=               "<th scope='col'><strong>No. Cta</strong></th>";
        $composite .=               "<th scope='col'><strong>Nombre</strong></th>";
        $composite .=               "<th scope='col'><strong>Escuela o Facultad</strong></th>";
        $composite .=               "<th scope='col'><strong>Fecha; Hora</strong></th>";
        $composite .=             "</tr>";
        $composite .=           "</thead>";
        $composite .=           "<tbody>";
        for ($x=0; $x < count($data[$i]) ; $x++) {
            $dateTime = new Carbon($data[$i][$x]->created_at);
            $composite .=           "<tr>";
            $composite .=             "<th scope='row'>".($x+1)."</th>";
            $composite .=               "<td>".$data[$i][$x]->cuenta."</td>";
            $composite .=               "<td>".$data[$i][$x]->nombre."</td>";
            $composite .=               "<td>".$data[$i][$x]->procedencia."</td>";
            $composite .=               "<td>".$dateTime->format("d/m/Y")."; ".$dateTime->toTimeString()."</td>";
            $composite .=           "</tr>";
        }
        $composite .=            "</tbody>";
        $composite .=         "</table>";
        $composite .=        "</div>"; // cierra el table responsive
        $composite .=       "</div>"; // cierra el panel-body
        $composite .=      "</div>"; // cierra el collapse
        $composite .=     "</div>"; // cierra el panel-default
      }
      $composite .= "<div>"; // cierra el acordeon

      return $composite;
    }
    public function ultimoCorte()
    {
      $lista_ini = Corte::all()->last()->listado_corte;
      return $lista_ini;
    }
    public function listasxCorte($corte)
    {
      $cortes = DB::table('cortes')
                         ->join('solicitudes','cortes.solicitud_id','=','solicitudes.id')
                         ->join('users','solicitudes.user_id','=','users.id')
                         ->join('procedencias','users.procedencia_id','=','procedencias.id')
                         ->select('cortes.listado_corte','cortes.listado_id',
                                  'solicitudes.cuenta','solicitudes.nombre','solicitudes.user_id',
                                  'procedencias.procedencia','solicitudes.created_at')
                         ->where('cortes.listado_corte',$corte)
                         ->orderBy('cortes.listado_id','ASC')
                         ->orderBy('solicitudes.cuenta','ASC')
                         ->GET()->toArray();

         // Convertimos el arreglo continuo en un arreglo de listados
         $objetos = array();  // arreglo de objetos
         $listados = array(); // Arreglo de listados.
         // Si se consulta una fecha sin cortes, no se realiza proceso alguno.
         if ($cortes!=[]) {
           $listado = $cortes[0]->listado_id;
           for ($i=0; $i < count($cortes); $i++) {
              if ($cortes[$i]->listado_id == $listado) {
                array_push($objetos,$cortes[$i]);
                if (($i+1)==count($cortes)) {
                  array_push($listados, $objetos);
                }
              } else {
                array_push($listados, $objetos);
                $listado = $cortes[$i]->listado_id;
                $objetos = array();
                array_push($objetos,$cortes[$i]);
              }
           }
         }

         return $listados;
    }
    public function lista_Corte($corte,$lista)
    {
      $cortes = DB::table('cortes')
                         ->join('solicitudes','cortes.solicitud_id','=','solicitudes.id')
                         ->join('users','solicitudes.user_id','=','users.id')
                         ->join('procedencias','users.procedencia_id','=','procedencias.id')
                         ->select('cortes.listado_corte','cortes.listado_id',
                                  'solicitudes.cuenta','solicitudes.nombre','solicitudes.user_id',
                                  'procedencias.procedencia','solicitudes.created_at')
                         ->where('cortes.listado_corte',$corte)
                         ->where('cortes.listado_id',$lista)
                         ->orderBy('solicitudes.cuenta','ASC')
                         ->GET()->toArray();
      return $cortes;
    }
    public function pdf()
    {
      dd('Hola');
    }

}
