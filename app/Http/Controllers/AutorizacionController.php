<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
Use App\Models\AutTransInfo;
use Carbon\Carbon;
use DateTime;

class AutorizacionController extends Controller
{
   public function showATI()
    {
      $hoy = Carbon::now()->format("d/m/Y");
      $day = substr($hoy, 0, 2);
      $month = substr($hoy, 3, 2);
      $year = substr($hoy, 6, 4);
      $fecha = Carbon::create($year, $month, $day);
      $day = $fecha->formatLocalized('%d');
      $month = $fecha->formatLocalized('%B');
      $year = $fecha->formatLocalized('%Y');
      return view('/autorizacion_t_info')->with(compact('day', 'month', 'year'));
    }
    public function postATI(Request $request){
      $request->validate([
          'nombre' => 'required',
          'curp' => 'required|min:18|max:18|regex:/^[A-Z]{4}[0-9]{2}[0-1][0-9][0-9]{2}[M,H][A-Z]{5}[0-9]{2}$/',
          'num_tel' => 'required|numeric',
          'num_cel' => 'required|numeric|digits:10',
          'correo' => 'required|email',
          'acepto' => 'required'
          ],[
           'nombre.required' => 'Debes proporcionar tu nombre completo',
           'curp.required' => 'Debes proporcionar tu CURP',
           'curp.min' => 'El CURP debe ser de 18 caracteres.',
           'curp.max' => 'El CURP debe ser de 18 caracteres.',
           'curp.regex' => 'El formato de CURP es incorrecto',
           'num_tel.required' => 'Debes proporcionar tu número de teléfono fijo',
           'num_tel.numeric' => 'El número de teléfono fijo deben ser solo dígitos',
           'num_cel.required' => 'Debes proporcionar tu número de teléfono celular',
           'num_cel.numeric' => 'El número de teléfono celular deben ser solo dígitos',
           'num_cel.digits' => 'El número de teléfono celular deben ser de 10 dígitos',
           'correo.required' => 'Debes proporcionar tu correo electrónico',
           'correo.email' => 'El correo electrónico no tiene el formato correcto',
           'acepto.required' => 'Debes aceptar lo notificado'
      ]);
      $num_cta = '312207139'; // *** El #cuenta debió ser previamente proporcionado ***

      $consulta = AutTransInfo::select()->where('num_cta', $num_cta)->first();
      if($consulta != null){
         $consulta->nombre_completo = $_POST['nombre'];
         $consulta->curp = $_POST['curp'];
         $consulta->tel_fijo = $_POST['num_tel'];
         $consulta->tel_celular = $_POST['num_cel'];
         $consulta->correo = $_POST['correo'];
         $consulta->autoriza = $_POST['acepto']; //on
         $consulta->num_cta = '312207139';
         $consulta->update();
      }
      else {
         $user = new AutTransInfo();
         $user->nombre_completo = $_POST['nombre'];
         $user->curp = $_POST['curp'];
         $user->tel_fijo = $_POST['num_tel'];
         $user->tel_celular = $_POST['num_cel'];
         $user->correo = $_POST['correo'];
         $user->autoriza = $_POST['acepto']; //on
         $user->num_cta = '312207139';
         $user->save();
      }
      return redirect()->route('imprimePDF_ATI', compact('num_cta'));
    }
    //Se obtiene la información necesaria para la creación del PDF
    public function PdfAutTransInfo(){
      $num_cta = $_GET['num_cta'];
      $alumno = AutTransInfo::select()->where('num_cta', $num_cta)->first();
      $consulta = $alumno->created_at;
      $day = $consulta->format('d');
      $month = $consulta->format('m');
      $year = $consulta->format('Y');
      // dd($day, $consulta->format('d'));
      $fecha = Carbon::create($year, $month, $day);
      $day = $fecha->formatLocalized('%d');
      $month = $fecha->formatLocalized('%B');
      $year = $fecha->formatLocalized('%Y');
      //Creamos el contenido del documento
      $vista = $this->documentoPDF($alumno, $day, $month, $year);
      //return view("consultas.listasPDF", compact('vista'));
      $titulo = "Impresión Aceptación de Transferencia de Información";
      $view = \View::make('consultas.autorizacionPDF', compact('vista', 'titulo'))->render();
      $pdf = \App::make('dompdf.wrapper');
      $pdf->loadHTML($view);
      return $pdf->stream('ATI_'.$num_cta.'.pdf');
    }
    public function documentoPDF_mitad($alumno, $day, $month, $year, $entrega){
      $composite = "";
      $composite .= "<div class='test'>Impresión de prueba</div>";
      $composite .= "<div class='container' class='cont_aut'>";
      $composite .= "<div class='ati_pdf'>";
      $composite .= "<h1 align='center'><b>AUTORIZACIÓN DE TRANSFERENCIA DE INFORMACIÓN</b></h1><br>";
      $composite .= "<p> Ciudad Universitaria, Cd. Mx., a <u>".$day."</u> de <u>".$month."</u> de <u>".$year."</u></p><br>";
      $composite .=	"<p align='justify'>";
      $composite .= "<div>Director de Certificación y Control Documental,</div>";
      $composite .= "<div>D.G.A.E.</div>";
      $composite .= "<div>P r e s e n t e.</div>";
      $composite .= "</p><br>";
      $composite .= "<p align='justify'>Por medio de la presente manifiesto que se me ha informado de que la Dirección de Certificación y Control
      		Documental de la DGAE-UNAM, debe de enviar información de mis datos académicos y profesionales como
      		egresado(a), a la Dirección General de Profesiones de la Secretaría de Educación Pública, para que yo pueda
      		en su oportunidad realizar el trámite de registro de título y obtención de cédula profesional ante la citada
      		dependencia y que además debo actualizar mis datos personales siguientes: </p><br>";
      $composite .= "<p align='justify'><div>Nombre completo: <u>".$alumno->nombre_completo."</u></div>";
      $composite .= "<div>CURP: <u>".$alumno->curp."</u></div>";
      $composite .= "<div>Núm. telefónico fijo: <u>".$alumno->tel_fijo."</u></div>";
      $composite .= "<div> Núm. telefónico celular: <u>".$alumno->tel_celular."</u></div>";
      $composite .= "<div><b>Correo electrónico</b>(a donde será enviada cualquier información del trámite, incluido el número de cédula profesional):
              <u>".$alumno->correo."</u></div></p><br>";
      $composite .= "<p align='justify'>De acuerdo con el artículo octavo del Reglamento de Transparencia, acceso a la información pública y
      		protección de datos personales para la Univerdidad Autponoma de México, se considera como información
      		confidencial los datos personales de todos y cada uno de los egresados que deseen realizar el trámite de
      	 	registro y otención de cédula profesional.</p><br>";
      $composite .= "<p align='justify'>Por lo anterior acepto y autorizo que la DGAE-UNAM utilice de forma automatizada mis datos personales y
      		académicos, los cuales formarán parte de la base de datos de la misma dependencia con la finalidad de usarlos
      		en forma enunciativa más no limitativa para que me identifiquen, ubiquen, comuniquen, contacten, y envíen
      		información por cualquier medio posible además de transferirlos a la Dirección General de Profesiones de la
      	 	Secretaría de Educación Pública, para los fines antes señalados.</p>";
      $composite .= "<div class='line'></div>";
      $composite .= "<div> <span><div align='left'>Firma del (la) interesado(a)<div></span>";
      $composite .= "<span><div align='right'><b>".$entrega."</b></div><span></div>";
      $composite .= "</div>";
      $composite .= "</div>";
      return $composite;
    }
    public function documentoPDF($alumno, $day, $month, $year){
      $composite = "";
      $composite .= $this->documentoPDF_mitad($alumno, $day, $month, $year, "EXPEDIENTE");
      $composite .= "<hr>";
      $composite .= $this->documentoPDF_mitad($alumno, $day, $month, $year, "EGRESADO");
      return $composite;
    }
}
