<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;




use App\Models\{Web_Service, Alumno};
use App\Http\Controllers\Admin\WSController;
use App\Http\Traits\Consultas\TitulosFechas;

use Carbon\Carbon;
use Session;
use DB;

class LoginController extends Controller
{
    use AuthenticatesUsers, TitulosFechas;

    // protected $redirectTo = '/registroTitulos/contactos/hola';
    // protected $loginView = 'AutTransInfo.login';
    // protected $guard = 'students';

    public function __construct()
    {
      $this->middleware('web');
    }
    public function username(){
        return 'num_cta';
    }
    protected $guard = 'alumno';

    public function showLoginForm()
    {
      // Verificamos si hay sesión activa
     if (Auth::check())
     {
          // Si tenemos sesión activa mostrará la página de inicio
          return redirect()->to('/alumno/login');
     }
     // Si no hay sesión activa mostramos el formulario
     return view('alumno.login');
    }

    public function logout(Request $request)
    {
      $this->guard('alumno')->logout();
      $request->session()->flush();
      $request->session()->regenerate();
      return redirect('alumnos/login');
    }
  //   protected function guard()
  // {
  //     return Auth::guard('web');
  // }

  public function login()
  {
     $credentials = $this->validate(request(),[
        'num_cta' => 'required|numeric|digits:9',
        'password' => 'required|numeric|digits:8',
      ],[
        'num_cta.required' => 'Debes proporcionar tu número de cuenta',
        'num_cta.numeric' => 'El número de cuenta son solo dígitos',
        'num_cta.digits'  => 'Deben ser 9 dígitos',
        'password.required' => 'Debes proporcionar tu contraseña',
        'password.numeric' => 'Deben ser solo números (ddmmaaaa)',
        'password.digits'  => 'Debe tener el formato ddmmaaaa'
     ]);
     $cuenta = substr($_POST['num_cta'], 0, 8);
     $verif = substr($_POST['num_cta'], 8, 1);
     $query = "SELECT * FROM Datos ";
     $query .= "WHERE dat_ncta = '".$cuenta."' ";
     $query .= "AND dat_dig_ver = '".$verif."' ";
     $info = DB::connection('sybase')->select($query);
     $fechaCaptura = $_POST['password'];
     if(!empty($info))
     {
        $fecha_nac = Carbon::parse($info[0]->dat_fec_nac)->format('dmY');
        if($fecha_nac == $fechaCaptura)
        {
           if($this->studentExist($credentials['num_cta']))
           {
              Auth::guard('alumno')->attempt($credentials);
           }
           else {
              // dd("no ingrese");
              $ws_SIAE = Web_Service::find(2);
              $identidad = new WSController();
              $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, $cuenta.$verif, $ws_SIAE->key);
              // dd($identidad, utf8_encode($identidad->apellido1), utf8_encode($identidad->apellido2), $identidad->nombres);
              $valida = (array)$identidad;
              if($valida['curp-validada'] == 0)
              {
                 $nombres = $this->separaNombre($info[count($info)-1]->dat_nombre);
                 $fechaNacDat = substr($info[count($info)-1]->dat_fec_nac, 0, 10);
                 $fechaNacDat = Carbon::parse($fechaNacDat)->format('d/m/Y');
                 $this->createUserLogin($info[count($info)-1]->dat_ncta.$info[count($info)-1]->dat_dig_ver, $fechaCaptura, $nombres['apellido1'], $nombres['apellido2'], $nombres['nombre'], $info[count($info)-1]->dat_curp, null);
              }
              else {
                 $this->createUserLogin($identidad->cuenta, $fechaCaptura, utf8_encode($identidad->apellido1), utf8_encode($identidad->apellido2), utf8_encode($identidad->nombres), $identidad->curp, $identidad->correo1);
              }
              Auth::guard('alumno')->attempt($credentials);
           }
           return redirect()->intended('alumnos/ati');
        }
     }
     else{
        $ws_SIAE = Web_Service::find(2);
        $identidad = new WSController();
        $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, $cuenta.$verif, $ws_SIAE->key);
        // dd(utf8_encode($identidad));
        // dd(count($identidad));

        if(isset($identidad->cuenta))
        {

           $fecha_nac = Carbon::createFromFormat('d/m/Y', $identidad->nacimiento)->toDateTimeString();
           $fecha_nac = Carbon::parse($fecha_nac)->format('dmY');
           // dd("entre");
           if ($fecha_nac == $fechaCaptura) {
              if($this->studentExist($credentials['num_cta']))
              {
                 Auth::guard('alumno')->attempt($credentials);
                 return redirect()->intended('alumnos/ati');
              }
              else {
                 $this->createUserLogin($identidad->cuenta, $fechaCaptura, $identidad->apellido1, $identidad->apellido2, $identidad->nombres, $identidad->curp, $identidad->correo1,$identidad->nacimiento);
                 Auth::guard('alumno')->attempt($credentials);
                 return redirect()->intended('alumnos/ati');
              }
           }
           else {
              Session::flash('message', 'Estas credenciales no coinciden con nuestros registros.');
              return redirect()->route('alumno.login');
           }

        }
        else {
           Session::flash('message', 'No existen datos del alumno en nuestros registros.');
           return redirect()->route('alumno.login');
        }
     }
     Session::flash('message', 'Estas credenciales no coinciden con nuestros registros.');
     return redirect()->route('alumno.login');
  }

   public function studentExist($num_cta)
   {
      $exist=DB::connection('condoc_eti')->table('alumnos')->select('num_cta')->where('num_cta', $num_cta)->get();
      if($exist->isNotEmpty())
         return true;
      return false;
   }

   public function separaNombre($nombreC)
   {
      // dd($nombreC);
      $nombres = explode('*', $nombreC);
      // $resultado = array();
      $resultado['nombre'] = $resultado['apellido1'] = $resultado['apellido2'] = "";
      switch (count($nombres)) {
         case '1':
            $resultado['nombre'] = $nombres[0];
            break;
         case '2':
            $resultado['nombre'] = $nombres[1];
            $resultado['apellido1'] = $nombres[0];
            break;
         case '3':
            $resultado['nombre'] = $nombres[2];
            $resultado['apellido1'] = $nombres[0];
            $resultado['apellido2'] = $nombres[1];
      }
      return $resultado;
   }


}
