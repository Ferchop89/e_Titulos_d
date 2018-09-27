<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
Use App\Models\Alumno;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use App\Rules\curpValido;

use App\Models\Web_Service;
use App\Http\Controllers\Admin\WSController;

class AutorizacionController extends Controller
{
   public function showATI()
    {
      if(!Auth::guard('alumno')->check())
      {
         return redirect()->route('alumno.login');
      }
      $hoy = Carbon::now()->format("d/m/Y");
      $day = substr($hoy, 0, 2);
      $month = substr($hoy, 3, 2);
      $year = substr($hoy, 6, 4);
      $f = Carbon::create($year, $month, $day);
      $day = $f->formatLocalized('%d');
      $month = $f->formatLocalized('%B');
      $year = $f->formatLocalized('%Y');
      $fecha = (object)['day' => $day, 'month' => $month, 'year' => $year];
      $alumno = Auth::guard('alumno')->user();
      return view('/autorizacion_t_info')->with(compact('fecha', 'alumno'));
    }

    public function postATI(Request $request){
      $request->validate([
          'nombres' => 'required',
          'apellido1' => 'required',
          'apellido2' => 'required',
          'curp' => ['required', 'min:18', 'max:18', new curpValido],
          // 'num_tel' => 'numeric|min:10',
          'num_cel' => 'required|numeric|min:10',
          'correo' => 'required|email',
          'acepto' => 'required',
          'plantel' => 'required'
          ],[
           'nombres.required' => 'Debes proporcionar tu(s) nombre(s)',
           'apellido1.required' => 'Debes proporcionar tu apellido paterno',
           'apellido2.required' => 'Debes proporcionar tu apellido materno',
           'curp.required' => 'Debes proporcionar tu CURP',
           'curp.min' => 'El CURP debe ser de 18 caracteres.',
           'curp.max' => 'El CURP debe ser de 18 caracteres.',
           'curp.regex' => 'El formato de CURP es incorrecto',
           // 'num_tel.required' => 'Debes proporcionar tu número de teléfono fijo',
           // 'num_tel.numeric' => 'El número de teléfono fijo deben ser solo dígitos',
           // 'num_tel.min' => 'El número de teléfono fijo al menos se forma por 10 dígitos',
           'num_cel.required' => 'Debes proporcionar tu número de teléfono celular',
           'num_cel.numeric' => 'El número de teléfono celular deben ser solo dígitos',
           'num_cel.min' => 'El número de teléfono celular al menos se forma por 10 dígitos',
           'correo.required' => 'Debes proporcionar tu correo electrónico',
           'correo.email' => 'El correo electrónico no tiene el formato correcto',
           'acepto.required' => 'Debes aceptar lo notificado',
           'plantel' => 'El plantel es obligatorio'
      ]);
      $alumno = Auth::guard('alumno')->user();
      $num_cta = $alumno->num_cta;
      $num_cta = str_pad($num_cta, 9, "0", STR_PAD_LEFT);
      $consulta = Alumno::select()->where('num_cta', $num_cta)->first();
      if($consulta != null){
         if($_POST['acepto'] == "on")
         {
            $aceptacion = 1;
         }
         else{
            $aceptacion = 0;
         }
         $consulta->nombres = strtoupper($_POST['nombres']);
         $consulta->apellido1 = strtoupper($_POST['apellido1']);
         $consulta->apellido2 = strtoupper($_POST['apellido2']);
         $consulta->curp = strtoupper($_POST['curp']);
         $consulta->tel_fijo = $_POST['num_tel'];
         $consulta->tel_celular = $_POST['num_cel'];
         $consulta->correo = $_POST['correo'];
         $consulta->autoriza = $aceptacion;
         $consulta->num_cta = $num_cta;
         $consulta->update();
      }
      else {
         if($_POST['acepto'] == "on"){
            $aceptacion = 1;
         }
         else
         {
            $aceptacion = 0;
         }
         $user = new Alumno();
         $user->nombres = strtoupper($_POST['nombres']);
         $user->apellido1 = strtoupper($_POST['apellido1']);
         $user->apellido2 = strtoupper($_POST['apellido2']);
         $user->curp = strtoupper($_POST['curp']);
         $user->tel_fijo = $_POST['num_tel'];
         $user->tel_celular = $_POST['num_cel'];
         $user->correo = $_POST['correo'];
         $user->autoriza = $aceptacion;
         $user->num_cta = $num_cta;
         $user->save();
      }
      return redirect()->route('imprimePDF_ATI', ['num_cta' => $num_cta, 'plantel' => $_POST['plantel']]);
    }
    //Se obtiene la información necesaria para la creación del PDF
    public function PdfAlumno(){
      $plantel = request()->plantel;
      $num_cta = request()->num_cta;
      $alumno = Alumno::select()->where('num_cta', $num_cta)->first();
      $consulta = $alumno->created_at;
      $day = $consulta->format('d');
      $month = $consulta->format('m');
      $year = $consulta->format('Y');
      $fecha = Carbon::create($year, $month, $day);
      $day = $fecha->formatLocalized('%d');
      $month = $fecha->formatLocalized('%B');
      $year = $fecha->formatLocalized('%Y');
      //Creamos el contenido del documento
      $vista = $this->documentoPDF($alumno, $day, $month, $year, $plantel);
      // return view("consultas.listasPDF", compact('vista'));
      $titulo = "Impresión Aceptación de Transferencia de Información";
      $view = \View::make('consultas.autorizacionPDF', compact('vista', 'titulo'))->render();
      $pdf = \App::make('dompdf.wrapper');
      $pdf->loadHTML($view);
      // return $pdf->stream('ATI_'.$num_cta.'.pdf');
      return $pdf->download('ATI_'.$num_cta.'.pdf');
    }
    public function documentoPDF_mitad($alumno, $day, $month, $year, $entrega, $plantel){
      $composite = "";
      $composite .= "<div class='container pdf_c'>";
      // $composite .= "<div class='test'>Impresión de prueba</div>";
      $composite .= "<div class='ati_pdf'>";
      $composite .= "<table class='tabla'>";
      $composite .= "<tr>";
      $composite .= "<td><div align='left'><img src='images/logo_unam.jpg' height='80' width='65'></div></td>";
      $composite .= "<td><div class='head' align='center'><p>Universidad Nacional Autónoma de México</p>";
      $composite .= "<p>Secretaría General</p>";
      $composite .= "<p>Dirección General de Administración Escolar</p></div></td>";
      $composite .= "<td><div align='right'><img src='images/escudo_dgae_bw.png' height='70' width='90'></div></td>";
      $composite .= "</tr>";
      $composite .= "</table>";
      $composite .= "<hr class='bloque'/>";
      $composite .= "<div class='dup'><p align='center' style='font-size:14.5px;'><b>AUTORIZACIÓN DE TRANSFERENCIA DE INFORMACIÓN</b></p>";
      $composite .= "<p align='center' style='font-size:14.5px;'><b>A LA DIRECCIÓN GENERAL DE PROFESIONES DE LA SECRETARÍA DE EDUCACIÓN PÚBLICA</b></p><div>";
      $composite .= "<br><p align='right'> Ciudad Universitaria, Cd. Mx., a <u>".$day."</u> de <u>".$month."</u> de <u>".$year."</u></p><br>";
      $composite .=	"<p align='justify'>";
      $composite .= "<div><b>Dirección General de Administración Escolar</b></div>";
      $composite .= "<div><b>Universidad Nacional Autónoma de México</b></div>";
      $composite .= "<div><b>P r e s e n t e.</b></div>";
      $composite .= "</p><br>";
      $composite .= "<p align='justify'>Por medio edio de la presente manifiesto que el área de Servicios Escolares de la (del) Facultad, Escuela, Centro, Instituto o
                      Programa de Posgrado <u>".strtoupper($plantel)."</u> me informó que la Dirección General de Administración Escolar de la UNAM (DGAE-UNAM) debe transferir la información
                      de mis datos académicos y personales como egresado(a) de esta Universidad Nacional, a la Dirección General de Profesiones de la Secretaría de Educación
                      Pública (DGP-SEP), para que yo pueda realizar el trámite de registro de título o grado para la obtención de cédula profesional ante la citada dependencia
                      gubernamental.</p>";
      $composite .= "<br><p align='justify'>Para ello, se me solicita actualizar los siguientes datos personales y manifiesto, <b>bajo protesta de decir verdad</b>, que
                     son verídicos y fehacientes:</p><br>";
      $composite .= "<div class='info_alumno'><p align='justify'><div><b>Nombre(s): </b> <u>".$alumno->nombres."</u></div><br>";
      $composite .= "<div><table class='tabla_i'><tr><td><b>Primer Apellido: </b><u>".$alumno->apellido1."</u></td>";
      $composite .= "<td><b>Segundo Apellido: </b><u>".$alumno->apellido2."</u></td></tr></table></div><br>";
      $composite .= "<div><b>CURP (18 caracteres): </b> <u>".$alumno->curp."</u></div><br>";
      $composite .= "<div><table class='tabla_i'><tr><td><b>Núm. telefónico fijo: </b><u>".$alumno->tel_fijo."</u></td>";
      $composite .= "<td> <b>Núm. telefónico celular: </b><u>".$alumno->tel_celular."</u></td></tr></table></div><br>";
      $composite .= "<div><b>Correo electrónico</b>(donde recibirá información del trámite, incluido el número de cédula profesional):
              <u>".$alumno->correo."</u></div></p></div>";
      $composite .= "<br><p align='justify'>Por lo anterior descrito, acepto y autorizo<span class='super'>1</span> que la DGAE-UNAM, en cumplimiento a lo establecido
                      en el Decreto por el que se reforman y derogan diversas disposiciones del Reglamento de la Ley Reglamentaria del Artículo 5°
                      Constitucional,relativo al ejercicio de las profesiones en el Distrito Federal, publicado en el Diario Oficial de la Federación
                      el 5 de abril de 2018 <span class='super'>2</span>, realice la transferencia electrónica de mis datos personales y académicos (que hasta hoy se
                      mantienen en custodia de la DGAE-UNAM) a la DGP-SEP y que, una vez actualizados, formarán parte de la base de datos de dicha
                      dependencia gubernamental. Lo anterior, primero para que cuando la DGAE-UNAM lo requiera, me identifique, ubique,
                      comunique, contacte y envíe información por cualquier medio posible, y en segundo término para que la DGP-SEP
                      en el momento que yo lo requiera o decida, acepte mi solicitud del trámite de registro de Título o Grado y me emita la
                      cédula profesional correspondiente.</p>";
      $composite .= "</div>";
      $composite .= "<br><br><br><div class='line'/></br>";
      $composite .= "<div><div align='center'>NOMBRE Y FIRMA DEL ALUMNO</div>";
      $composite .= "<hr class='bloque'/>";
      $composite .= "<div class='sub' style='font-size:11px;' align='left'><b>1</b> Artículo 8° del Reglamento de Transparencia,
                      Acceso a la Información Pública y Protección de Datos Personales para la
                      Universidad Nacional Autónoma de México (Consulte: <a>http://www.abogadogeneral.unam.mx/legislacion/abogen/documento.html?doc_id=66</a>).";
      $composite .= "<div class='sub' style='font-size:11px;' align='left'><b>2</b> Consulte: <a>http://www.dof.gob.mx/nota_detalle.php?codigo=5518146&fecha=05/04/2018</a></div>";
      $composite .= "<div class='uni_na'><img src='images/unam_universidad.png' height='70' width='90'></div>";
      $composite .= "<br><div align='left'><b>".$entrega."</b></div>";
      $composite .= "</div>";

      return $composite;
    }
    public function documentoPDF($alumno, $day, $month, $year, $plantel){
      $compositef = "";
      $compositef .= $this->documentoPDF_mitad($alumno, $day, $month, $year, "EGRESADO", $plantel);
      $compositef .= $this->documentoPDF_mitad($alumno, $day, $month, $year, "EXPEDIENTE DGAE", $plantel);
      return $compositef;
    }
}
