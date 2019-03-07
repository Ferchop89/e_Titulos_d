<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;

use App\Models\{Web_Service, Alumno, InfoExtra};
use App\Http\Controllers\Admin\WSController;
use App\Http\Traits\Consultas\TitulosFechas;

use Carbon\Carbon;
use Session;
use DB;

class LoginController extends Controller
{
    use AuthenticatesUsers, TitulosFechas;

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

  public function oauth_SIAE($cta, $nip){
    $key = "9c1335b2f05e6e6075c7f25d064edf9dfc4c7222"; // llave valida
    error_reporting(E_ALL);

    $postData = array(
                     "ncta" => $cta,
                     "nip" => $nip,
                     "key"  => $key
                     );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, "https://tramites.dgae.unam.mx/ws/soap/loginws.php");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    ob_start();
    $data = curl_exec($ch);
    ob_end_clean();
    curl_close($ch);
    $json = json_decode($data);

    return $json;
  }

   public function login(){
      $credentials = $this->validate(request(),[
        'num_cta' => 'required|numeric|digits:9',
        'password' => 'required',
      ],[
        'num_cta.required' => 'Debes proporcionar tu número de cuenta',
        'num_cta.numeric' => 'El número de cuenta son solo dígitos',
        'num_cta.digits'  => 'Deben ser 9 dígitos',
        'password.required' => 'Debes proporcionar tu contraseña',
      ]);
      $cuenta = substr($_POST['num_cta'], 0, 8);
      $verif = substr($_POST['num_cta'], 8, 1);
      $ncuenta = $_POST['num_cta'];
      $fechaCaptura = $_POST['password'];

      $oauth = $this->oauth_SIAE($ncuenta, $fechaCaptura);
      if($oauth->mensaje == "validado"){
      //Verificamos que exista en el sistema
         if($this->studentExist($credentials['num_cta'])){
            $this->changePass($ncuenta, $fechaCaptura);
            if(Auth::guard('alumno')->attempt($credentials))
            {
               return redirect()->intended('alumnos/ati');
            }
         }
         else{
            $info = $this->validaCondocDatos($cuenta, $verif);
            if(!empty($info)){
               $nombres = $this->separaNombre($info->dat_nombre);
               $user = $this->createUserLogin($info->dat_ncta.$info->dat_dig_ver, $fechaCaptura, $nombres['apellido1'], $nombres['apellido2'], $nombres['nombre'], $info->dat_curp, null);
               //Añadimos los datos necesarios para la nueva información
               $info_dl = $this->createInfo($info->dat_ncta.$info->dat_dig_ver, NULL);
               if(Hash::check($fechaCaptura, $user->password)){ //Verificamos que la contraseña sea igual a la registrada
                 Auth::guard('alumno')->attempt($credentials);
                 return redirect()->intended('alumnos/ati');
               }
               else{ //Si no son iguales, notificamos
                 Session::flash('message', 'Estas credenciales no coinciden con nuestros registros.');
                 return redirect()->route('alumno.login');
               }
            }
            $ws_SIAE = Web_Service::find(2);
            $identidad = new WSController();
            $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, $cuenta.$verif, $ws_SIAE->key);
            if(isset($identidad) && (isset($identidad->nacimiento) && $identidad->nacimiento != "//")){ //Verificamos que exista el alumno en SIAE
               //Verificamos que coincidan los datos con SIAE
               $user = $this->createUserLogin($identidad->cuenta, $fechaCaptura, $identidad->apellido1, $identidad->apellido2, $identidad->nombres, $identidad->curp, $identidad->correo1);
               //Añadimos los datos necesarios para la nueva información
               if($identidad->codigo-postal != NULL){
                 //dd("codigo_postal");
                 $info_dl = $this->createInfo($info->dat_ncta.$info->dat_dig_ver, $identidad->codigo-postal);
                 //con número de cuenta y codigo_postal
               }else{
                 //dd("sin_codigo_postal");
                 $info_dl = $this->createInfo($info->dat_ncta.$info->dat_dig_ver, NULL);
               }
               if(Hash::check($fechaCaptura, $user->password)){ //Verificamos que la contraseña sea igual a la registrada
                  Auth::guard('alumno')->attempt($credentials);
                  return redirect()->intended('alumnos/ati');
               }
               else{ //Si no son iguales, notificamos
                  Session::flash('message', 'Estas credenciales no coinciden con nuestros registros.');
                  return redirect()->route('alumno.login');
               }
            }
         }
      }
      else{
         $ws_DGIRE = new WSController();
         $ws_DGIRE = $ws_DGIRE->ws_DGIRE2($cuenta.$verif);
         //Verificamos si DGIRE tiene información del alumno
         if(isset($ws_DGIRE) && (isset($ws_DGIRE->fechaNacimiento) && $ws_DGIRE->fechaNacimiento != "")){
            $fecha_nac = Carbon::createFromFormat('d/m/Y', $ws_DGIRE->fechaNacimiento)->toDateTimeString();
            $fecha_nac = Carbon::parse($fecha_nac)->format('dmY');
            //Verificamos que su password (fecha de nacimiento) sea correcta
            if ($fecha_nac == $fechaCaptura){
               $this->createUserLogin($ws_DGIRE->numeroCuenta, $fechaCaptura, $ws_DGIRE->apellidoPaterno, $ws_DGIRE->apellidoMaterno, $ws_DGIRE->nombre, $ws_DGIRE->curp, " ",$ws_DGIRE->fechaNacimiento);
               //Añadimos los datos necesarios para la nueva información
               if($identidad->codigo-postal != NULL){
                 //dd("codigo_postal");
                 $info_dl = $this->createInfo($info->dat_ncta.$info->dat_dig_ver, $identidad->codigo-postal);
                 //con número de cuenta y codigo_postal
               }else{
                 //dd("sin_codigo_postal");
                 $info_dl = $this->createInfo($info->dat_ncta.$info->dat_dig_ver, NULL);
               }
               Auth::guard('alumno')->attempt($credentials);
               return redirect()->intended('alumnos/ati');
            }else{ //Si no coinciden la contraseña y lo proporcionado
               Session::flash('message', 'Estas credenciales no coinciden con nuestros registros.');
               return redirect()->route('alumno.login');
            }
         }
         Session::flash('message', 'No existen datos del alumno en nuestros registros.');
         return redirect()->route('alumno.login');
      }
   }

   public function studentExist($num_cta){
      $exist=DB::connection('condoc_eti')
         ->table('alumnos')
         ->select('num_cta')
         ->where('num_cta', $num_cta)
         ->get();
      if($exist->isNotEmpty())
         return true;
      return false;
   }

   public function separaNombre($nombreC){
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

   public function validaCondocDatos($cuenta, $verif){
      $info = DB::connection('sybase')
         ->table('Datos')
         ->where('dat_ncta', $cuenta)
         ->where('dat_dig_ver', $verif)
         ->get()
         ->last();
      return $info;
   }
   public function changePass($num_cta, $pass){
      $user = DB::connection('condoc_eti')
         ->table('alumnos')
         ->where('num_cta', $num_cta)
         ->update(['password' => bcrypt($pass)]);
   }
   public function createInfo($num_cta, $cp){
     $info_dl = new InfoExtra();
     $info_dl->num_cta = $num_cta;
     $info_dl->codigo_postal = $cp;
     $info_dl->save();
     return $info_dl;
   }
}
