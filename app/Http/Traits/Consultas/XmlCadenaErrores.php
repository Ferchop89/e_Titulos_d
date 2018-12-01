<?php

namespace App\Http\Traits\Consultas;

use DB;
use Illuminate\Http\Request;
use Spatie\ArrayToXml\ArrayToXml;
use \FluidXml\FluidXml;
use App\Models\Estudio;
use App\Models\Entidad;
use App\Models\Carrera;
use App\Models\SolicitudSep;
use App\Models\Modo;
use Carbon\Carbon;

use App\Models\Web_Service;
use App\Http\Controllers\Admin\WSController;


trait XmlCadenaErrores {

   public function integraConsulta($cuenta, $digito, $carrera)
   {
      // Integra toda la informacion que previamente ha sido segmentada por temas correspondientes a cada nodos
      // genera una arreglo con 3 arreglos, datos: arreglo con todos los items a enviar a sep; errores: arreglo
      // de errores en los items; y paridad: arreglos llaves (como en el arreglos datos) con valores unam.

      $errores = $items = $resultado = $paridad = array();
      $fuente = '';
      // Consulta por porOmision Integra los Items xml que no requieren consulta y son valores predeterminados
      $consulta = $this->porOmision($cuenta,$digito,$carrera);
      $items = array_merge($items,$consulta);
      // Primer consulta
      $consulta = $this->carrerasProcedencia($cuenta,$digito,$carrera);
      if (isset($consulta['errores'])!=null) {
         $errores = array_merge($errores,$consulta['errores']);
         // Se elimina de la consulta el item errores porque ya se encuentra en el arreglo $erroers
         unset($consulta['errores']);
      }
      if (isset($consulta['paridad'])!=null) {
         $paridad  = array_merge($paridad,$consulta['paridad']);
         // Se elimina el item "paridad" porque ya swe encuentra en el arreglo $paridad;
         unset($consulta['paridad']);
      }
      $items = array_merge($items,$consulta);
      // Segunda consulta de Datos
      $consulta = $this->titulosEscprocedencia($cuenta,$carrera);
      if (isset($consulta['errores'])!=null) {
         $errores = array_merge($errores,$consulta['errores']);
         unset($consulta['errores']);
      }
      if (isset($consulta['paridad'])!=null) {
         $paridad  = array_merge($paridad,$consulta['paridad']);
         // Se elimina el item "paridad" porque ya swe encuentra en el arreglo $paridad;
         unset($consulta['paridad']);
      }
      $items = array_merge($items,$consulta);
      // Tecer consulta de Datos
      $nivel = $items['atrib_nivelProf']; // Proviene de titulosProcedencia
      $consulta = $this->antecedente($cuenta,$nivel);
      if (isset($consulta['errores'])!=null) {
         $errores = array_merge($errores,$consulta['errores']);
         unset($consulta['errores']);
      }
      if (isset($consulta['paridad'])!=null) {
         $paridad  = array_merge($paridad,$consulta['paridad']);
         // Se elimina el item "paridad" porque ya swe encuentra en el arreglo $paridad;
         unset($consulta['paridad']);
      }
      $items = array_merge($items,$consulta);
      unset($items['atrib_nivelProf']); // Eliminamos el nivel. No se ocupa en el documento final.
      // Cuarta consultaDatos
      $consulta = $this->titulosExamenes($cuenta,$carrera);
      if (isset($consulta['errores'])!=null) {
         $errores = array_merge($errores,$consulta['errores']);
         unset($consulta['errores']);
      }
      if (isset($consulta['paridad'])!=null) {
         $paridad  = array_merge($paridad,$consulta['paridad']);
         // Se elimina el item "paridad" porque ya swe encuentra en el arreglo $paridad;
         unset($consulta['paridad']);
      }
      $items = array_merge($items,$consulta);
      // Quinta consultaDatos
      $consulta = $this->titulosDatos($cuenta,$digito,$carrera);
      if (isset($consulta['errores'])!=null) {
         $errores = array_merge($errores,$consulta['errores']);
         unset($consulta['errores']);
      }
      if (isset($consulta['fuente'])!=null) {
         $fuente = $consulta['fuente'];
         unset($consulta['fuente']);
      }
      $items = array_merge($items,$consulta);

      // Ordenamos los items. Todos tienen un sub item (ejem: __05__) y como se agregaron
      // los items "por omision" que vienen desordenados, es necesario reenumerar para tenerlos consecutivos.
      ksort($items);

      // Si no existen los errores, el arreglo de errores
      if ($errores==[]) {
         $errores['sin errores'] = 'Sin errores';
      }

      // Integramos errores y Datos
      $resultado[0] = $items;
      $resultado[1] = $errores;
      $resultado[2] = $paridad;
      $resultado[3] = $fuente;
      return $resultado;
   }
   public function nombreCarrera($carrera)
   {
      // consulta de la clave UNAM y nombre UNAM
      $query  = 'SELECT ';
      $query .= 'carrp_cve      AS cveCarrera, ';
      $query .= 'carrp_nombre   AS nombreCarrera ';
      $query .= 'from Carrprog ';
      $query .= "where carrp_cve = '".$carrera."'";
      $nombreCarrera  = (array)DB::connection('sybase')->select($query);
      return $nombreCarrera;
   }
   public function carrerasProcedencia($cuenta,$digito,$carrera)
   {
      // procedimiento de asignacion de clave sep y nombre de carrera para la paridad de informacion
      // consulta de la clave SEP
      $query1  = 'SELECT ';
      $query1 .= "ori_cve_profesiones AS _09_cveCarrera, ";
      $query1 .= "carrera             AS _10_nombreCarrera ";
      $query1 .= 'from Datos ';
      $query1 .= 'join Orientaciones on ori_plancarr = dat_car_actual and dat_orientacion = ori_orienta ';
      $query1 .= 'join Carreras_Profesiones on convert(int,ori_cve_profesiones) = clave_carrera ';
      $query1 .= "where ";
      $query1 .= "dat_ncta = '".$cuenta."' and ";
      $query1 .= "dat_dig_ver = '".$digito."' and ";
      $query1 .= "dat_car_actual = '".$carrera."' ";
      // consulta de la clave UNAM y nombre UNAM
      $query2  = 'SELECT ';
      $query2 .= 'carrp_cve      AS _09_cveCarrera, ';
      $query2 .= 'carrp_nombre   AS _10_nombreCarrera ';
      $query2 .= 'from Carrprog ';
      $query2 .= "where carrp_cve = '".$carrera."'";
      // inicialización de arreglos.
      $errores = $datos = $paridad = array(); // errores y faltantes
      // Buscamos el nombre y carrera SEP
      $sep  = (array)DB::connection('sybase')->select($query1); // Carrera SEP
      $unam = (array)DB::connection('sybase')->select($query2); // Carrera UNAM
      // Busqueda de la clave SEP
      if ($sep!=[]) {
         //  Encontro clave y nombre SEP para la clave local ($cuenta y $carrera)
         $resultado = (array)$sep[0];
      } else {
         // No existe nombre de carrera ni clave
         // Realizamos una nueva busqueda en un archivo temporal de carreras que nos proporciona Eduardo Miranda
         $carrerasLalo = [ '00452'=>'611310','00923'=>'431302','01157'=>'511301','01210'=>'411305','09121'=>'128304','10646'=>'621301',
                           '11622'=>'301303','10647'=>'621311','20227'=>'771302','40723'=>'612301','41143'=>'521301','51922'=>'231301',
                           '61021'=>'211327','61221'=>'411350','61222'=>'411351','61421'=>'452305','61422'=>'452304','61423'=>'452306',
                           '61424'=>'452303','66621'=>'342319','09021'=>'563313','11136'=>'520396','01214'=>'411350','00253'=>'734369',
                           '61223'=>'411352','00640'=>'621311','00248'=>'771327','00641'=>'621301','01334'=>'720353',
                           '01057' => '203312', 	'50922' => '431302', 	'01053' => '245301', 	'01054' => '221351', 	'01055' => '271301', 	'40437' => '611303',
                           '01056' => '203310', 	'01336' => '720355', 	'00642' => '611313', 	'01061' => '623306', 	'00445' => '611305', 	'00637' => '621311',
                           '00638' => '621301', 	'01912' => '231301', 	'0123470' => '410731', 	'1003171' => '431798', 	'0093103' => '431715', 	'1003165' => '633722',
                           '0163100' => '311745', 	'0123429' => '419733', 	'0063200' => '612741', 	'0113157' => '511771', 	'3003020' => '421701', 	'0083077' => '622746',
                           '0073143' => '612746', 	'0083224' => '622737', 	'0013180' => '621741', 	'0143002' => '421761', 	'0093109' => '431788', 	'0123447' => '419716',
                           '0093112' => '431742', 	'0093110' => '431755', 	'2000344' => '621766', 	'0113072' => '511730', 	'0093107' => '431731', 	'0113156' => '511702',
                           '0123443' => '414766', 	'0113158' => '511724', 	'3003021' => '421706', 	'0083220' => '622745', 	'0083164' => '613712', 	'0043191' => '611768',
                           '0143070' => '421761', 	'0083218' => '613712', 	'0113159' => '511719', 	'0093113' => '431768', 	'2000346' => '511702', 	'0063196' => '621759',
                           '2000341' => '514745', 	'0063085' => '612741', 	'0083222' => '622746', 	'0803116' => '111705', 	'0043192' => '611706', 	'0043193' => '661701',
                           '0143167' => '406701', 	'0083226' => '622739', 	'0113161' => '566708', 	'0143166' => '421761', 	'0093190' => '430704', 	'0113155' => '511727',
                           '0093189' => '457702', 	'0010346' => '612722', 	'0093108' => '431753', 	'0123467' => '410787', 	'0123406' => '410715', 	'0113154' => '511730',
                           '0123448' => '410785', 	'0013179' => '559702', 	'0093104' => '410778', 	'0010347' => '621741', 	'0073133' => '612732', 	'0104161' => '271501',
                           '0724087' => '122539', 	'2004146' => '612501', 	'0044115' => '601514', 	'0064073' => '607502', 	'0964087' => '122539', 	'0124096' => '401505',
                           '3004085' => '121502', 	'0064189' => '607502', 	'1004169' => '311556', 	'0044113' => '601528', 	'0744087' => '122539', 	'0674194' => '341501',
                           '3004178' => '241584', 	'0664109' => '515530', 	'2004042' => '511533', 	'0794100' => '622502', 	'4004025' => '511530', 	'0594153' => '121579',
                           '0064193' => '611520', 	'0754101' => '510501', 	'0124085' => '121502', 	'0074146' => '612501', 	'4004146' => '612501', 	'0164169' => '311556',
                           '4004149' => '245508', 	'0924078' => '104501', 	'0694172' => '642573', 	'0194173' => '231501', 	'0744085' => '121502', 	'0054154' => '515507',
                           '0024137' => '711504', 	'0164170' => '311578', 	'0974085' => '121502', 	'2004117' => '221538', 	'0104081' => '211502', 	'0104149' => '245508',
                           '0064190' => '621560', 	'0754103' => '514501', 	'0044116' => '611584', 	'0134130' => '721511', 	'0024125' => '714502', 	'0104089' => '623507',
                           '0104110' => '221502', 	'0514087' => '122539', 	'0044114' => '611562', 	'0104145' => '261501', 	'0904104' => '514516', 	'6214171' => '613501',
                           '0034085' => '121502', 	'2004149' => '245508', 	'0104195' => '261505', 	'0104144' => '611545', 	'0054109' => '515530', 	'0134135' => '721516',
                           '0805090' => '521605', 	'0075146' => '612601', 	'0015092' => '511609', 	'3005088' => '231601', 	'0105145' => '261601', 	'0105110' => '203601',
                           '0125093' => '411601', 	'0755103' => '505605', 	'0055112' => '515602', 	'0725087' => '122605', 	'0695143' => '121613', 	'0045117' => '611602',
                           '0675121' => '341601', 	'0125143' => '121613', 	'0165071' => '311618', 	'0715143' => '121613', 	'0085123' => '622601', 	'5005088' => '231601',
                           '0745087' => '122605', 	'0975085' => '121602', 	'0055109' => '515601', 	'0105148' => '761601',
                             '70321' => '120309',   '70322' => '120310',   '71024' => '273301',   '20412' => '611302', '0104120' => '221508', '0064192' => '213544',
                           '0134133' => '721526', '0014198' => '511530', '0695160' => '121602', '0145095' => '421604', '0015111' => '511611', '0795123' => '622601',
                           '0105089' => '623601', '1005071' => '311618', '0105091' => '245601', '0655085' => '121602', '0035085' => '121602', '0125085' => '121602',
                           '0695085' => '121602', '3005085' => '121602'];
         if (array_key_exists($carrera,$carrerasLalo)) {
            // la carrera existe en el arreglo de Lalo. Entonces buscamos en la tabla de carreras el nombre de la misma.
            $nombreSep = Carrera::where('CVE_SEP',$carrerasLalo[$carrera])->pluck('CARRERA');
            if (isset($nombreSep[0])) {
               $resultado['_09_cveCarrera'] = $carrerasLalo[$carrera];
               $resultado['_10_nombreCarrera'] = $nombreSep[0];
               // $resultado['_10_nombreCarrera'] = 'Nombre Eduardo';
            } else {
               // No se encuentra la clave Sep
               $errores['_09_cveCarrera'] = 'Sin clave SEP';
               // Agregamos al arreglo de resultados la lleve "errores" que contiene un key-value de errores
               $resultado['errores'] = $errores;
               $resultado['_09_cveCarrera'] = '----';
               $resultado['_10_nombreCarrera'] = '----';
            }
         } else {
            $errores['_09_cveCarrera'] = 'Sin clave SEP';
            // Agregamos al arreglo de resultados la lleve "errores" que contiene un key-value de errores
            $resultado['errores'] = $errores;
            $resultado['_09_cveCarrera'] = '----';
            $resultado['_10_nombreCarrera'] = '----';

         }
      }
      // Busqueda de la clave UNAM
      if ($unam!=[]) {
         //  Encontro clave y nombre UNAM para clave SEP de la carrera
         $paridad = (array)$unam[0];
      } else {
         // No existe nombre de carrera ni clave
         $paridad['_09_cveCarrera'] = '----';
         $paridad['_10_nombreCarrera'] = '----';
      }
      // Agregamos al arreglos de resultados la llave "paridad" que contiene las llaves de paridad UNAM
      $resultado['paridad'] = $paridad;
      return $resultado;
   }
   public function fechas1136($fecha, $tipoFecha, $item)
   {
      // Evaluacion de fechas según el campo escpro_tipo_fec: 1=ddmmaaaa; 2=mmaaaa; 3=aaaaaaaa.
      // item (11 o 36):  2 (escuela que gradua) o 36 (escuela antecendente al grado)
      $datos = $errores = $paridad =  array();
      switch ($tipoFecha) {
         case '1': // fechas tipo ddmmaaaa suponemos fecha de terminación
            $fechaX = substr($fecha,4,4).'-'.substr($fecha,2,2).'-'.substr($fecha,0,2) ;
            if ($item=='11') { // item 11 duracion de la carrera. item 11 (inicio) y 12 (fin obligatorio).
               $datos['_11_fechaInicio']      = '----';
               if ( !strtotime($fechaX) || strlen($fecha)!=8 ) {
                  $datos['_12_fechaTerminacion'] = $fecha;
                  $errores['_12_fechaTerminacion'] = 'periodo irregular (ddmmaaaa)';
               } else {
                  $datos['_12_fechaTerminaciantecedenteon'] = $fechaX;
                  $paridad['_12_fechaTerminacion'] = $fecha.'. tipo(ddmmaaaa)';
               }
            } elseif($item=='36') { // item 36 Solo es obligatorio la fecha de terminacion
               $datos['_36_fechaInicio']      = '----';
               if ( !strtotime($fechaX) || strlen($fecha)!=8 ) { // La fecha no es valida
                  $datos['_37_fechaTerminacion'] = $fecha;
                  $errores['_37_fechaTerminacion'] = 'periodo irregular (ddmmaaaa)';
               } else {
                  $datos['_37_fechaTerminacion'] = $fechaX;
                  $paridad['_37_fechaTerminacion'] = $fecha.'. tipo(ddmmaaaa)';
               }
            }
            break;
         case '2': // fechas tipo mmaaaa
            $fechaX = substr($fecha,2,4).'-'.substr($fecha,0,2).'-01';
            if ($item=='11') { // item 11 duracion de la carrera. item 11 (inicio) y 12 (fin obligatorio).
               $datos['_11_fechaInicio']      = '----';
               if (!strtotime($fechaX) || strlen($fecha)!=6) {
                  $datos['_12_fechaTerminacion'] = $fecha;
                  $errores['_12_fechaTerminacion'] = 'periodo irregular (mmaaaa)';
               } else {
                  // Fecha validad, colocamos la paridad para comparar los resultados
                  $datos['_12_fechaTerminacion'] = $fechaX;
                  $paridad['_12_fechaTerminacion'] = $fecha.'. tipo(mmaaaa)';
               }
            } elseif($item=='36') { // item 36 Solo es obligatorio la fecha de terminacion.
               $datos['_36_fechaInicio'] = '----';
               if (!strtotime($fechaX) || strlen($fecha)!=6) { // La fecha no es valida
                  $datos['_37_fechaTerminacion'] = $fecha;
                  $errores['_37_fechaTerminacion'] = 'periodo irregular tipo(mmaaaa)';
               } else {
                  // Fecha validad, colocamos la paridad para comparar los resultados
                  $datos['_37_fechaTerminacion'] = $fechaX;
                  $paridad['_37_fechaTerminacion'] = $fecha.'. tipo(mmaaaa)';
               }
            }
            break;
         case '3': // fechas tipo aaaaaaaa (dos periodos consecutivos)
            // Si los periodos son iguales (ejemño inicio 2016; final 2016)
            $fecha1 = substr($fecha,0,4); $fecha2 = substr($fecha,4,4);
            if ($fecha1 == $fecha2) {
               // inicio y final son iguales. (enero-diciembre)
               $fecha1 = substr($fecha,0,4).'-01-01'; $fecha2 = substr($fecha,4,4).'-12-01';
            } else {
               // inicio del periodo es diferente al final periodo (enero-enero)
               $fecha1 = substr($fecha,0,4).'-01-01'; $fecha2 = substr($fecha,4,4).'-01-01';
            }
            // verificamos la calidad de la información

            if ($item=='11') { // Periodo de estudios
               if ( !strtotime($fecha1) || !strtotime($fecha2) || $fecha1>$fecha2 || strlen($fecha)!=8 ) {
                  $datos['_11_fechaInicio']      = '----';
                  $datos['_12_fechaTerminacion'] = $fecha;
                  $errores['_12_fechaTerminacion'] = 'periodo irregular tipo(aaaaAAAA)';
               } else {
                  // Fechas correctas
                  $datos['_11_fechaInicio']      = $fecha1;
                  $datos['_12_fechaTerminacion'] = $fecha2;
                  $paridad['_12_fechaTerminacion'] = $fecha.'. tipo(aaaaAAAA)';
               }
            } elseif ($item=='36'){ // Periodo antecedente
               if ( !strtotime($fecha1) || !strtotime($fecha2) || $fecha1>$fecha2 || strlen($fecha)!=8 ) {
                  $datos['_36_fechaInicio']      = '----';
                  $datos['_37_fechaTerminacion'] = $fecha;
                  $errores['_37_fechaTerminacion'] = 'periodo irregular tipo(aaaaAAAA)';
               } else {
                  // Fechas correctas
                  $datos['_36_fechaInicio']      = $fecha1;
                  $datos['_37_fechaTerminacion'] = $fecha2;
                  $paridad['_37_fechaTerminacion'] = $fecha.'. tipo(aaaaAAAA)';
               }
            }
            break;
         default:
            // No se tiene ninguna de los tipos de fecha esperadas (1,2,3)
            if ($item=='11') {
               if ($fecha!='') {
                  $datos['_11_fechaInicio']      = '----';
                  $datos['_12_fechaTerminacion'] = $fecha;
                  $errores['_12_fechaTerminacion'] = 'Tipo de fecha irregular: '.$tipoFecha;
               } else {
                  $datos['_11_fechaInicio']      = '----';
                  $datos['_12_fechaTerminacion'] = '----';
                  $errores['_12_fechaTerminacion'] = 'Fecha inexistente';
               }

            } elseif ($item=='36') {
               if ($fecha!='') {
                  $datos['_36_fechaInicio']      = '----';
                  $datos['_37_fechaTerminacion'] = $fecha;
                  $errores['_37_fechaTerminacion'] = 'Tipo de fecha irregular: '.$tipoFecha;
               } else {
                  $datos['_36_fechaInicio']      = '----';
                  $datos['_37_fechaTerminacion'] = '----';
                  $errores['_37_fechaTerminacion'] = 'Fecha inexistente';
               }
            }
            break;
      }
      if ($errores!=[]) {  // Existen errores
         $datos['errores'] = $errores;
      }
      if ($paridad!=[]) {
         $datos['paridad'] = $paridad;
      }
      return $datos;
   }
   public function titulosEscprocedencia($cuenta,$carrera)
   {
      // Periodo en que se cursa la carrera. Las fechas de deducen de un periodo (ejem: 20092011)
      // y se extrae el nivel para buscar posteriormente la escuela anterior (antecedente) en el
      // nivel anterior
      $query = 'SELECT ';
      $query .= "escpro_fec_exp     AS carrera_fechas, ";
      $query .= "escpro_tipo_fec    AS fecha_tipo, ";
      $query .= "escpro_nivel_pro   AS atrib_nivelProf ";
      $query .= "FROM Titulos ";
      $query .= "JOIN Escprocedencia ON escpro_ncta = tit_ncta AND escpro_plancarr_act = tit_plancarr ";
      $query .= "where escpro_ncta = '".$cuenta."' and tit_plancarr='".$carrera."'";
      $info = (array)DB::connection('sybase')
                     ->select($query);

      $datos = $resultado = array();
      if ($info==[]) {// $query .= "AND tit_nivel != '07'";
            $datos['_11_fechaInicio'] = '----';
            $datos['_12_fechaTerminacion'] = '----';
            $datos['atrib_nivelProf'] =  '--';
            $errores['_11_fechaInicio'] = 'periodo de estudios inexistente';
            $errores['_EscuelaProcedencia'] = 'procedencia sin clave de nivel';
      } else {
         // Existen el periode estudio y en nivle profesionl
         $datos = (array)$info[0];
         // test
         $resultado = $this->fechas1136($datos['carrera_fechas'],$datos['fecha_tipo'],'11');
         // Agregamos el nivel de la carrera_Attr
         $resultado['atrib_nivelProf'] = $datos['atrib_nivelProf'];
      }
      return $resultado;
   }

   public function titulosDatosAti($cuenta,$digito,$carrera)
   {
      // El nombre viene conbinado, se consulta, se divide en nombre y apellidos; y se omite del arreglo
      $query1 = 'SELECT DISTINCT ';
      $query1 .= "dat_nombre AS _17_nombre ";
      $query1 .= "FROM Titulos ";
      $query1 .= "JOIN Datos ON dat_ncta = tit_ncta  AND dat_car_actual = tit_plancarr AND dat_nivel = tit_nivel ";
      $query1 .= "where dat_ncta = '".$cuenta."' and dat_dig_ver = '".$digito."' and tit_plancarr='".$carrera."'";

      $query2  = 'SELECT DISTINCT ';
      $query2 .= 'apellido1,apellido2,nombres, ';
      $query2 .= 'curp AS _16_curp, ';
      $query2 .= 'correo AS _20_correoElectronico, ';
      $query2 .= 'autoriza ' ;
      $query2 .= 'FROM alumnos ';
      // $query2 .= "where num_cta = '".$cuenta.$digito."'";
      $query2 .= "where num_cta = '".$cuenta.$digito."' and ";
      $query2 .= "autoriza = 1 ";

      // Traemos los datos desde la bdd que los alumnos actualizan y del condoc
      $info_sybase = (array)DB::connection('sybase')->select($query1);
      $info_mysql = DB::connection('condoc_ati')->select($query2);

      $datos = $errores = array();
      // Consultamos si el usuario y actualizo los datos
      if ($info_mysql==[]) {
         // El usuario aun no ha actualizado los datos
         // entonces los buscamos en el Web_Service
         // NO se encontro la informacion en el WS
         $datos['_16_curp']              = '----';
         $datos['_17_nombre']            = '----';
         $datos['_18_primerApellido']    = '----';
         $datos['_19_segundoApellido']   = '----';
         $datos['_20_correoElectronico'] = '----';
         // La actulizacion es via ati_pdf
         $datos['fuente'] = '0'; // No lo encontro ni en Ati
         // En un solo campo se coloca el error para los campos 16, 17, 18, 19, 20
         $errores['_16_curp'] = 'Sin autorización';
      } else
      {
         // El usuario si ha registrado los datos
         $datos['_16_curp']               = $info_mysql[0]->_16_curp;
         $datos['_17_nombre']             = $info_mysql[0]->nombres;
         $datos['_18_primerApellido']     = $info_mysql[0]->apellido1;
         $datos['_19_segundoApellido']    = $info_mysql[0]->apellido2;
         $datos['_20_correoElectronico']  = $info_mysql[0]->_20_correoElectronico;
         // La actulizacion es via ati
         $datos['fuente'] = '1'; // ati
      }
      if ($errores!=[]) {
         $datos['errores'] = $errores;
      }
      return $datos;
   }
   public function titulosDatosWs($cuenta,$digito,$carrera)
   {
      // El nombre viene conbinado, se consulta, se divide en nombre y apellidos; y se omite del arreglo
      $query1 = 'SELECT DISTINCT ';
      $query1 .= "dat_nombre AS _17_nombre ";
      $query1 .= "FROM Titulos ";
      $query1 .= "JOIN Datos ON dat_ncta = tit_ncta  AND dat_car_actual = tit_plancarr AND dat_nivel = tit_nivel ";
      $query1 .= "where dat_ncta = '".$cuenta."' and dat_dig_ver = '".$digito."' and tit_plancarr='".$carrera."'";

      $query2  = 'SELECT DISTINCT ';
      $query2 .= 'apellido1,apellido2,nombres, ';
      $query2 .= 'curp AS _16_curp, ';
      $query2 .= 'correo AS _20_correoElectronico, ';
      $query2 .= 'autoriza ' ;
      $query2 .= 'FROM alumnos ';
      // $query2 .= "where num_cta = '".$cuenta.$digito."'";
      $query2 .= "where num_cta = '".$cuenta.$digito."' and ";
      $query2 .= "autoriza = 1 ";

      // Traemos los datos desde la bdd que los alumnos actualizan y del condoc
      $info_sybase = (array)DB::connection('sybase')->select($query1);
      $info_mysql = DB::connection('condoc_ati')->select($query2);

      $datos = $errores = array();
      // Consultamos si el usuario y actualizo los datos
      if ($info_mysql==[]) {
         // El usuario aun no ha actualizado los datos
         // entonces los buscamos en el Web_Service
         $ws_SIAE = Web_Service::find(2);
         $identidad = new WSController();
         $identidad = $identidad->ws_SIAE($ws_SIAE->nombre, $cuenta.$digito, $ws_SIAE->key);

               if (!empty($identidad)) {
                  if (isset($identidad->curp)) {
                     if ($identidad->curp!='') {
                        $datos['_16_curp'] = $identidad->curp;
                     } else {
                        $datos['_16_curp'] = '----';
                        $errores['_16_curp'] = 'Sin informacion en WS';
                     }
                  }else {
                     $datos['_16_curp'] = '----';
                     $errores['_16_curp'] = 'Sin informacion en WS';
                  }
                  if (isset($identidad->correo1)) {
                     if ($identidad->correo1!='') {
                        $datos['_20_correoElectronico'] =  $identidad->correo1;
                     } else {
                        $datos['_20_correoElectronico'] = '----';
                        $errores['_16_curp'] = 'Sin informacion en WS';
                     }
                  } else {
                     $datos['_20_correoElectronico'] = '----';
                     $errores['_16_curp'] = 'Sin informacion en WS';
                  }
                  if (isset($identidad->nombres)) {
                     $datos['_17_nombre'] =  utf8_encode($identidad->nombres);
                  } else {
                     $errores['_16_curp'] = 'Sin informacion en WS';
                     $datos['_17_nombre'] = '----';
                  }
                  if (isset($identidad->apellido1)) {
                     $datos['_18_primerApellido'] =  utf8_encode($identidad->apellido1);
                  } else {
                     $errores['_16_curp'] = 'Sin informacion en WS';
                     $datos['_18_primerApellido']  = '----';
                  }
                  if (isset($identidad->apellido2)) {
                     $datos['_19_segundoApellido'] =  utf8_encode($identidad->apellido2);
                  } else {
                     $errores['_16_curp'] = 'Sin informacion en WS';
                     $datos['_19_segundoApellido'] = '----';
                  }
                  // La actualizacion es via ati_pdf
                  $datos['fuente'] = '2'; // WS
               } else {
                  // NO se encontro la informacion en el WS
                  $datos['_16_curp'] = '----';
                  $datos['_17_nombre']          = '----';
                  $datos['_18_primerApellido']  = '----';
                  $datos['_19_segundoApellido'] = '----';
                  $datos['_20_correoElectronico'] = '----';
                  // La actulizacion es via ati_pdf
                  $datos['fuente'] = '0'; // No lo encontro ni en Ati ni en WS
                  // En un solo campo se coloca el error para los campos 16, 17, 18, 19, 20
                  $errores['_16_curp'] = 'Sin autorización';
               }

      } else
      {
         // El usuario si ha registrado los datos
         $datos['_16_curp']               = $info_mysql[0]->_16_curp;
         $datos['_17_nombre']             = $info_mysql[0]->nombres;
         $datos['_18_primerApellido']     = $info_mysql[0]->apellido1;
         $datos['_19_segundoApellido']    = $info_mysql[0]->apellido2;
         $datos['_20_correoElectronico']  = $info_mysql[0]->_20_correoElectronico;
         // La actulizacion es via ati
         $datos['fuente'] = '1'; // ati
      }
      if ($errores!=[]) {
         $datos['errores'] = $errores;
      }
      return $datos;
   }

   public function titulosDatos($cuenta,$digito,$carrera)
   {
      // El nombre viene conbinado, se consulta, se divide en nombre y apellidos; y se omite del arreglo
      $query1 = 'SELECT DISTINCT ';
      $query1 .= "dat_nombre AS _17_nombre ";
      $query1 .= "FROM Titulos ";
      $query1 .= "JOIN Datos ON dat_ncta = tit_ncta  AND dat_car_actual = tit_plancarr AND dat_nivel = tit_nivel ";
      $query1 .= "where dat_ncta = '".$cuenta."' and dat_dig_ver = '".$digito."' and tit_plancarr='".$carrera."'";

      $query2  = 'SELECT DISTINCT ';
      $query2 .= 'apellido1,apellido2,nombres, ';
      $query2 .= 'curp AS _16_curp, ';
      $query2 .= 'correo AS _20_correoElectronico, ';
      $query2 .= 'autoriza ' ;
      $query2 .= 'FROM alumnos ';
      $query2 .= "where num_cta = '".$cuenta.$digito."' and ";
      $query2 .= "autoriza = 1 ";

      // Traemos los datos desde la bdd que los alumnos actualizan y del condoc
      $info_sybase = (array)DB::connection('sybase')->select($query1);
      $info_mysql = DB::connection('condoc_ati')->select($query2);

      $datos = $errores = array();
      // Consultamos si el usuario y actualizo los datos
      if ($info_mysql==[]) {
         // El usuario aun no ha actualizado los datos
         $datos['_16_curp'] = '----';
         $datos['_17_nombre']          = '----';
         $datos['_18_primerApellido']  = '----';
         $datos['_19_segundoApellido'] = '----';
         $datos['_20_correoElectronico'] = '----';
         // La actulizacion es via ati_pdf
         $datos['fuente'] = '0'; // No lo encontro en WS
         // En un solo campo se coloca el error para los campos 16, 17, 18, 19, 20
         $errores['_16_curp'] = 'Sin autorización alumno';
      } else
      {
         // El usuario si ha registrado los datos
         $datos['_16_curp']               = $info_mysql[0]->_16_curp;
         $datos['_17_nombre']             = $info_mysql[0]->nombres;
         $datos['_18_primerApellido']     = $info_mysql[0]->apellido1;
         $datos['_19_segundoApellido']    = $info_mysql[0]->apellido2;
         $datos['_20_correoElectronico']  = $info_mysql[0]->_20_correoElectronico;
         // La actulizacion es via ati
         $datos['fuente'] = '1'; // ati
      }
      if ($errores!=[]) {
         $datos['errores'] = $errores;
      }
      return $datos;
   }

   public function titulosExamenes($cuenta, $carrera)
   {
      $query = 'SELECT  ';
      $query .= "tit_fec_emision_tit AS _21_fechaExpedicion, ";
      $query .= "exa_tipo_examen AS _22_idModalidadTitulacion, ";
      $query .= "exa_fecha_examen_prof AS _24_fechaExamenProfesional, ";
      $query .= "exa_ini_ssoc AS exp_InicioSs, ";
      $query .= "exa_fin_ssoc AS exp_FinSs ";
      $query .= "FROM Titulos ";
      $query .= "JOIN Examenes ON exa_ncta = tit_ncta and exa_plancarr = tit_plancarr ";
      $query .= "where tit_ncta = '".$cuenta."' and tit_plancarr='".$carrera."'";

      $info = (array)DB::connection('sybase')
                     ->select($query);

      $datos = $errores = $paridad = array();
      if ($info==[]) {
         // La consulta no arrojo resultados, los campos para el XML se colocan vacios
         $datos['_21_fechaExpedicion'] = '----';
         $datos['_22_idModalidadTitulacion'] = '----';
         $datos['_23_ModalidadTitulacion'] = '----';
         $datos['_24_fechaExamenProfesional'] = '----';
         $datos['_25_fechaExencionExamenProfesional'] = '----';
         $datos['_26_cumplioServicioSocial'] = '----';
         // Se agregan errores correspondientes a los faltantes
         $errores['_21_fechaExpedicion'] = 'falta fecha de expedición de examen profesional';
         $errores['_22_idModalidadTitulacion'] = 'clave modalidad de titulación';
         $errores['_23_ModalidadTitulacion'] = 'modalidad de titulación inexistente';
         $errores['_24_fechaExamenProfesional'] = 'falta fecha de examen profesional';
         $errores['_25_fechaExencionExamenProf'] = 'falta fecha de exencion de examen profesional';
         $errores['_26_cumplioServicioSocial'] = 'falta periodo de Servicio Social';
      } else {
         // Validamos la existencia y el tipo y valor de todos los campos
         // pasamos los objetos a un arreglo.
         $datos = (array)$info[0];
         // Sustituimos en el arreglo la fecha de expedición del título por una fecha corta (sin hora-min-seg)
         $datos['_21_fechaExpedicion'] = substr($datos['_21_fechaExpedicion'],0,10);
         // validamos la fecha de expedición del Título. si existen errores, los agregamos.
         if (Carbon::createFromFormat('Y-m-d', substr($datos['_21_fechaExpedicion'],0,10))==false) {
            $errores['_21_fechaExpedicion'] = 'fecha de expedición de título inválida';
         }
         // preguntamos si existe la modalida de titulación en la tabla mapeo.
         $modo = Modo::where('cat_subcve',$datos['_22_idModalidadTitulacion'])->first();
         if ($modo==[]) {
            // Ya existe $datos['_22_idModalidadTitulacion'], pero no la ModalidadTitulacion
            $datos['_22_idModalidadTitulacion'] =  '----';
            $datos['_23_modalidadTitulacion']   =  '----';
            // Agregamos el mensaje de error a la llave _22_idModalidadTitulacion
            $errores['_22_idModalidadTitulacion'] = 'modalidad de titulación inexistente';

         } else {
            // Modalidad existencia si existe en catalogo.
            $datos['_22_idModalidadTitulacion']    = $modo->ID_MODALIDAD_TITULACION;
            $datos['_23_modalidadTitulacion']      = $modo->MODALIDAD_TITULACION;
            // agregamos los datos de paridad que aparecen en la columna de solicitudes.
            $paridad['_22_idModalidadTitulacion']  = $modo->cat_subcve;
            $paridad['_23_modalidadTitulacion']    = $modo->cat_nombre;
         }
         // validamos la fecha de examen profesional.
         // $fecha = strtotime($datos['_24_fechaExamenProfesional']);
         $fecha = Carbon::parse($datos['_24_fechaExamenProfesional'])->format('Y-m-d');
         if (!$fecha) {
            $errores['_24_fechaExamenProfesional'] = 'fecha de examen profesional inválida';
            $errores['_25_fechaExencionExamenProfesional'] = 'fecha de exensión de examen profesional inválida';
         } else {
            $datos['_24_fechaExamenProfesional'] = $fecha;
            // La fecha del examen profesional es la misma que la fecha de exencion (24 y 24)
            $datos['_25_fechaExencionExamenProfesional'] = $fecha;
         }
         // validamos fechas de servicio social.
         $fecha1 = Carbon::parse($datos['exp_InicioSs'])->format('Y/m/d');
         $fecha2 = Carbon::parse($datos['exp_FinSs'])->format('Y/m/d');
         // Retiramos del arreglo las fechas de Servicios social que ya no se van a ocupar.
         unset($datos['exp_InicioSs']); unset($datos['exp_FinSs']);
         // Se trata de fechas y además la final no es menor que la inicial
         // Evaluamos las fechas para inconsistencias o bien validar el Servicio solcial.
         if (!$fecha1 || !$fecha2 || !$fecha2>=$fecha1) {
            // Fecha de inicio o termino de servicio social no valida o invasion de fechas
            $datos['_26_cumplioServicioSocial'] = '---';
            $errores['_26_cumplioServicioSocial'] = 'periodo irregular de servicio social';
         } else {
            // El periodo del servicio social es válido.
            $datos['_26_cumplioServicioSocial'] = '1';
         }
      }

      if ($errores!=[]) {
         $datos['errores'] = $errores;
      }

      if ($paridad!=[]) {
         $datos['paridad'] = $paridad;
      }

      return $datos;
   }

   public function antecedente($cuenta,$nivel)
   {
      // ante periodo se divide en 2 fechas y se omite de la salida
      $query = "Select distinct catproc_nombre as _31_institucionProcedencia, ";
      $query .= "escpro_cveproc as clave_escuela_previa, ";
      $query .= "escpro_nivel_pro as ante_nivel, ";
      $query .= "catproc_ent_fed as _34_idEntidadFederativa, ";
      $query .= "escpro_fec_exp as carrera_fechas, ";
      $query .= "escpro_tipo_fec as fecha_tipo ";
      $query .= "from Escprocedencia ";
      $query .= "join Catprocedencia on catproc_cve = escpro_cveproc ";
      $query .= "join Paisedos on escpro_cveproc = catproc_cve ";
      $query .= "where escpro_ncta = '".$cuenta."' and escpro_nivel_pro < '".$nivel."' "; // $nivel es $info['nivelProf']
      $query .= "and escpro_nivel_pro<>'07' ";
      $query .= "order by escpro_nivel_pro asc";

      $info = (array)DB::connection('sybase')
                     ->select($query);
      // Escuela antecedente.antecedente
      $datos = $errores = $resultado = $paridad = array();
      if ($info!=[]) {
         // Agregamos a $info el ultimo registro de $antece que es el nivel previo inferior al actual.
         $datos = (array)$info[count($info)-1];
         if (!$datos['_31_institucionProcedencia']=='') {
            $resultado['_31_institucionProcedencia'] = $datos['_31_institucionProcedencia'];
         } else {
            $resultado['_31_institucionProcedencia'] = '----';
            $errores['_31_institucionProcedencia'] = 'No se cuenta con escuela de procedencia';
         }
         // Mapeo del idTipoEstudioAntecedente
         $tipoEstudio = Estudio::where('cat_subcve',$datos['ante_nivel'])->first();
         if (!$tipoEstudio==[]) {
            $resultado['_32_idTipoEstudioAntecedente']   = $tipoEstudio->ID_TIPO_ESTUDIO_ANTECEDENTE;
            $resultado['_33_tipoEstudioAntecedente']     = $tipoEstudio->TIPO_ESTUDIO_ANTECEDENTE;
            $paridad['_32_idTipoEstudioAntecedente']     = $tipoEstudio->cat_subcve;
            $paridad['_33_tipoEstudioAntecedente']       = $tipoEstudio->cat_nombre;
         } else {
            $resultado['_32_idTipoEstudioAntecedente']   = '----';
            $resultado['_33_tipoEstudioAntecedente']     = '----';
            $paridad['_32_idTipoEstudioAntecedente']     = '----';
            $paridad['_33_tipoEstudioAntecedente']       = '----';
            $errores['_32_idTipoEstudioAntecedente'] = 'tipo de estudio antecedente inválido';
         }
         // Mapeo de la entidad (Unam y Sep manejan diferentes claves)
         if ($datos['_34_idEntidadFederativa']<'00033') {
               $entidad = Entidad::where('pais_cve',$datos['_34_idEntidadFederativa'])->first();
         } else {
               // Todos las entidades se dejan coantecedentemo "EXTRANJERO"
               $entidad = Entidad::where('pais_cve','00033')->first();
         }

         if ($entidad) {
            $resultado['_34_idEntidadFederativa'] = $entidad->ID_ENTIDAD_FEDERATIVA;
            $resultado['_35_entidadFederativa'] = $entidad->C_NOM_ENT;
         } else {
            $resultado['_34_idEntidadFederativa'] = '----';
            $resultado['_35_entidadFederativa'] = '----';
            $errores['_34_idEntidadFederativa'] = 'sin clave de entidad para estudio antecedente';
         }
         // Valoramos la fecha y el tipo de fecha para las respuestas 36 y 37
         $fechas = $this->fechas1136($datos['carrera_fechas'],$datos['fecha_tipo'],'36');
         // verificamos la existencia del arreglo paridad para incorporarlo al local y elimiarlo de la listados
         if (array_key_exists('paridad',$fechas)) {
            $paridad  = array_merge($paridad,$fechas['paridad']);
            unset($fechas['paridad']);
         }
         // verificamos la existencia del arreglo errores para incoporarlo al local y eliminarlo del listado
         if (array_key_exists('errores',$fechas)) {
            $errores  = array_merge($errores,$fechas['errores']);
            unset($fechas['errores']);
         }
         // El arreglo $fechas solo queda con los datos de los items 36 y 37 por lo que lo mezcamos con el arrego $resultado
         $resultado = array_merge($resultado,$fechas);
      }
      else {
         // la consulta no arrojo resultados
         $resultado['_31_institucionProcedencia'] = '----';
         $resultado['_32_idTipoEstudioAntecedente'] = '----';
         $resultado['_33_tipoEstudioAntecedente'] = '----';
         $resultado['_35_entidadFederativa'] = '----';
         $resultado['_36_fechaInicio'] = '----';
         $resultado['_37_fechaTerminacion'] = '----';
         $errores['_31_institucionProcedencia'] = 'procedencia sin datos';
      }
      // agregamos los errores.
      if ($errores!=[]) {
         $resultado['errores'] = $errores;
      }

      // Agregamos al arreglos de resultados la llave "paridad" que contiene las llaves de paridad UNAM
      if ($paridad!=[]) {
         $resultado['paridad'] = $paridad;
      }
      return $resultado;
   }
   public function titulos($cuenta,$carrera)
   {
      $query = 'SELECT ';
      $query .= "tit_fec_emision_tit AS exp_fechaExpedicion, ";
      $query .= "FROM Titulos ";
      $query .= "where dat_ncta = '".$cuenta."' and tit_plancarr='".$carrera."'";

      $info = (array)DB::connection('sybase')
                     ->select($query)[0];
      return $info;
   }
   public function porOmision()
   {
      // Arreglo de valores por omiRectorsión.
      $datos = array();
      $datos['_01_version'] = '1.0';
      $datos['_07_cveInstitucion'] = '090001';
      $datos['_08_nombreInstitucion'] = 'UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO';
      $datos['_13_idAutorizacionReconocimiento'] = '8';
      $datos['_14_autorizacionReconocimiento'] = 'DECRETO DE CREACIÓN';
      $datos['_15_numeroRvoe'] = '----';
      $datos['_27_idFundamentoLegalServicioSocial'] = '2';
      $datos['_28_fundamentoLegalServicioSocial'] = 'ART. 55 LRART. 5 CONST';
      $datos['_29_idEntidadFederativa'] = '09';
      $datos['_30_entidadFederativa'] = 'CIUDAD DE MÉXICO';
      $datos['_38_noCedula'] = '----';
      return $datos;
   }

   public function cadenaOriginal($nodos,$tipo)
   {
      // Formacion de la cadena original para cada responsable de firma
      $cadenaOriginal = '||';
      $cadenaOriginal.= $nodos['TituloElectronico']['version'].'|';
      $cadenaOriginal.= $nodos['TituloElectronico']['folioControl'].'|';
      switch ($tipo) {
         case 'Jtit': // firma el Director General
               $cadenaOriginal.= $nodos['FirmaResponsable0']['curp'].'|';
               $cadenaOriginal.= $nodos['FirmaResponsable0']['idCargo'].'|';
               $cadenaOriginal.= $nodos['FirmaResponsable0']['cargo'].'|';
               $cadenaOriginal.= ($nodos['FirmaResponsable0']['abrTitulo']=='----')? '|':
               $nodos['FirmaResponsable0']['abrTitulo'].'|'; // opcional
            break;
         case 'Director': // firma el Director General
               $cadenaOriginal.= $nodos['FirmaResponsable1']['curp'].'|';
               $cadenaOriginal.= $nodos['FirmaResponsable1']['idCargo'].'|';
               $cadenaOriginal.= $nodos['FirmaResponsable1']['cargo'].'|';
               $cadenaOriginal.= ($nodos['FirmaResponsable1']['abrTitulo']=='----')? '|':
               $nodos['FirmaResponsable1']['abrTitulo'].'|'; // opcional
            break;
         case 'SecGral':  // firma el Secretario General
               $cadenaOriginal.= $nodos['FirmaResponsable2']['curp'].'|';
               $cadenaOriginal.= $nodos['FirmaResponsable2']['idCargo'].'|';
               $cadenaOriginal.= $nodos['FirmaResponsable2']['cargo'].'|';
               $cadenaOriginal.= ($nodos['FirmaResponsable2']['abrTitulo']=='----')? '|':
               $nodos['FirmaResponsable2']['abrTitulo'].'|'; // općional
            break;
         case 'Rector': // firma el Rector
               $cadenaOriginal.= $nodos['FirmaResponsable3']['curp'].'|';
               $cadenaOriginal.= $nodos['FirmaResponsable3']['idCargo'].'|';
               $cadenaOriginal.= $nodos['FirmaResponsable3']['cargo'].'|';
               $cadenaOriginal.= ($nodos['FirmaResponsable3']['abrTitulo']=='----')? '|':
               $nodos['FirmaResponsable3']['abrTitulo'].'|';
            break;
      }

      $cadenaOriginal .= $nodos['Institucion']['cveInstitucion'].'|';
      $cadenaOriginal .= $nodos['Institucion']['nombreInstitucion'].'|';

      $cadenaOriginal .= $nodos['Carrera']['cveCarrera'].'|';
      $cadenaOriginal .= $nodos['Carrera']['nombreCarrera'].'|';
      $cadenaOriginal .= ($nodos['Carrera']['fechaInicio']=='----')? '|' :
                         $nodos['Carrera']['fechaInicio'].'|'; // opcional
      $cadenaOriginal .= $nodos['Carrera']['fechaTeminacion'].'|';
      $cadenaOriginal .= $nodos['Carrera']['idAutorizacionReconocimiento'].'|';
      $cadenaOriginal .= $nodos['Carrera']['autorizacionReconocimiento'].'|';
      $cadenaOriginal .= ($nodos['Carrera']['numeroRvoe']=='----')? '|' :
                         $nodos['Carrera']['numeroRvoe'].'|'; // opcional

      $cadenaOriginal .= $nodos['Profesionista']['curp'].'|';
      $cadenaOriginal .= $nodos['Profesionista']['nombre'].'|';
      $cadenaOriginal .= $nodos['Profesionista']['primerApellido'].'|';
      $cadenaOriginal .= ($nodos['Profesionista']['segundoApellido']=='----')? '|' :
                         $nodos['Profesionista']['segundoApellido'].'|'; // opcional
      $cadenaOriginal .= $nodos['Profesionista']['correoElectronico'].'|';

      $cadenaOriginal .= $nodos['Expedicion']['fechaExpedicion'].'|';
      $cadenaOriginal .= $nodos['Expedicion']['idModalidadTitulacion'].'|';
      $cadenaOriginal .= $nodos['Expedicion']['modalidadTitulacion'].'|';
      $cadenaOriginal .= ($nodos['Expedicion']['fechaExamenProfesional']=='----')? '|' :
                         $nodos['Expedicion']['fechaExamenProfesional'].'|'; // opcional
      $cadenaOriginal .= ($nodos['Expedicion']['fechaExencionExamenProfesional']=='----')? '|':
                         $nodos['Expedicion']['fechaExencionExamenProfesional'].'|'; // opcional
      $cadenaOriginal .= $nodos['Expedicion']['cumpioServicioSocial'].'|';
      $cadenaOriginal .= $nodos['Expedicion']['idFundamentoLegalServicioSocial'].'|';
      $cadenaOriginal .= $nodos['Expedicion']['fundamentoLegalServicioSocial'].'|';
      $cadenaOriginal .= $nodos['Expedicion']['inEntidadFederativa'].'|';
      $cadenaOriginal .= $nodos['Expedicion']['entidadFederativa'].'|';

      $cadenaOriginal .= $nodos['Antecedente']['institucionProcedencia'].'|';
      $cadenaOriginal .= $nodos['Antecedente']['idTipoEstudioAntecedente'].'|';
      $cadenaOriginal .= $nodos['Antecedente']['tipoEstudioAntecedente'].'|';
      $cadenaOriginal .= $nodos['Antecedente']['idEntidadFederativa'].'|';
      $cadenaOriginal .= $nodos['Antecedente']['entidadFederativa'].'|';
      $cadenaOriginal .= ($nodos['Antecedente']['fechaInicio']=='----')? '|' :
                         $nodos['Antecedente']['fechaInicio'].'|'; // opcional
      $cadenaOriginal .= $nodos['Antecedente']['fechaTerminacion'].'|';
      $cadenaOriginal .= ($nodos['Antecedente']['noCedula']=='----')? '' :
                         $nodos['Antecedente']['noCedula']; // opcional
      $cadenaOriginal .= '||';

      return $cadenaOriginal;
   }
    // Generación del XML del Titulo Electrónico,
   public function tituloXml($nodos)
   {
      $tituloXML = new FluidXml('TituloElectronico');
      foreach ($nodos['TituloElectronico'] as $key => $value) {
        $tituloXML->setAttribute($key, $value);
      }
      $tituloXML->addChild('firmaResponsables', true)                    // True forza regresar al nodo firmaResponsables
                ->addChild('firmaResponsable', '' ,   $nodos['FirmaResponsable1'])
                ->addChild('firmaResponsable', '' ,   $nodos['FirmaResponsable2'])
                ->addChild('firmaResponsable', '' ,   $nodos['FirmaResponsable3']);
      $tituloXML->addChild('Institucion','',          $nodos['Institucion']);
      $tituloXML->addChild('Carrera',                 $nodos['Carrera']);
      $tituloXML->addChild('Profesionista',           $nodos['Profesionista']);
      $tituloXML->addChild('Expedicion',              $nodos['Expedicion']);
      $tituloXML->addChild('Antecedente',             $nodos['Antecedente']);

      return $tituloXML;
   }
    // Integración de toda información para todos los nodos.
   public function integraNodosUnam($folio,$datos,$cargo)
   {
      // Integra todos los arreglos de atributos en un arreglos general
      $componentes = array();

      // Nodo Titulo Electronico Ejemplo 1.0|2345678|
      $componentes['TituloElectronico'] = $this->tituloElectronico_Attr($folio);

      // Nodo Firma Responsable Ejemplo EICA750212HDFRNL01|3|RECTOR|LIC.|
      switch ($cargo) {
         case 'Jtit':
            $nombre='DIANA';$apellidoPat='GONZALEZ';$apeMat='NIETO';
            // $curp='UIES180831HDFSEP03';$idCarg='9';$cargo='DIRECTOR GENERAL';$titulo='M. EN C.';
            $curp='UIES180831S04';$idCarg='5';$cargo='JEFA DEL DEPARTAMENTO DE TITULOS';$titulo='LIC.';
            // $curp='GOND701217HP2';$idCarg='5';$cargo='JEFA DEL DEPARTAMENTO DE TITULOS';$titulo='LIC.';
            $certR = 'CertificadoResponsable'; $noCertR='874796688606327447';
            $componentes['FirmaResponsable0'] = $this->firmaResp_AttrUnam(
                        $nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo);
            break;
         case 'Director':
            $nombre='IVONNE';$apellidoPat='RAMIREZ';$apeMat='WENCE';
            // $curp='UIES180831HDFSEP03';$idCarg='9';$cargo='DIRECTOR GENERAL';$titulo='M. EN C.';
            $curp='UIES180831S03';$idCarg='9';$cargo='DIRECTOR GENERAL';$titulo='M. EN C.';
            // $curp='RAWI6005073U0';$idCarg='9';$cargo='DIRECTOR GENERAL';$titulo='M. EN C.';
            $certR = 'CertificadoResponsable'; $noCertR='1682280437054458477';
            $componentes['FirmaResponsable1'] = $this->firmaResp_AttrUnam(
                        $nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo);
            break;
         case 'SecGral':
            $nombre='LEONARDO';$apellidoPat='LOMELI';$apeMat='VANEGAS';
            $curp='UIES180831S02';$idCarg='6';$cargo='SECRETARIO GENERAL';$titulo='DR.';
            // $curp='LOVL7004289W7';$idCarg='6';$cargo='SECRETARIO GENERAL';$titulo='DR.';
            $certR = 'CertificadoResponsable'; $noCertR='3121493390511228062';
            $componentes['FirmaResponsable2'] = $this->firmaResp_AttrUnam(
                        $nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo);
            break;
         case 'Rector':
            $nombre='ENRIQUE LUIS';$apellidoPat='GRAUE';$apeMat='WIECHERS';
            $curp='UIES180831S01';$idCarg='3';$cargo='RECTOR';$titulo='DR.';
            // $curp='GAWE510109C14';$idCarg='3';$cargo='RECTOR';$titulo='DR.';
            $certR = 'CertificadoResponsable'; $noCertR='7608878899635960696 ';
            $componentes['FirmaResponsable3'] = $this->firmaResp_AttrUnam(
                        $nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo);
            break;
      }

      // Nodo Institucion. Ejemplo 010033|UNIVERSIDAD TECNOLÓGICA DE AGUASCALIENTES|
      $cveInstitucion=$datos['_07_cveInstitucion'];
      $nombreInstitucion=$datos['_08_nombreInstitucion'];
      $componentes['Institucion'] = $this->institucion_Attr($cveInstitucion,
                                                            $nombreInstitucion);

      // Nodo Carrera. Ejemplo 103339|INGENIERÍA EN NANOTECNOLOGÍA|2004-08-16|2009-06-12|5|ACTA DE SESIÓN|(vacio numeroRvoe)|
      $cveCarrera=$datos['_09_cveCarrera'];
      $nombreCarrera=$datos['_10_nombreCarrera'];
      $fechaInicio=$datos['_11_fechaInicio'];
      $fechaTerminacion=$datos['_12_fechaTerminacion'];
      $idAutorizacionReconocimiento=$datos['_13_idAutorizacionReconocimiento'];
      $autorizacionReconocimiento=$datos['_14_autorizacionReconocimiento'];
      $noRvoe=$datos['_15_numeroRvoe'];
      // $nombreCarrera = 'DOCTORADO EN CIENCIAS (BIOLOGÍA)';
      $componentes['Carrera'] = $this->carrera_Attr($cveCarrera,$nombreCarrera,
                                                    $fechaInicio,$fechaTerminacion,
                                                   $idAutorizacionReconocimiento,
                                                   $autorizacionReconocimiento,$noRvoe);

      // Nodo Profesionista. Ejemplo:  AICA770112HDFRNL01|ANTONIO|ALPIZAR|CASTRO|antonio.alpizar@gmail.com|
      $curp=$datos['_16_curp'];
      $nombre=trim($datos['_17_nombre']);
      $apePat=$datos['_18_primerApellido'];
      $apeMat=$datos['_19_segundoApellido'];
      $correo=$datos['_20_correoElectronico'];
      // $curp='SELIL890909FDFRL10';$nombre = 'LUZ MARIA GRACIELA'; $apePat='Serrano'; $apeMat = 'LIMON'; $correo='biologia@gmail.com';
      $componentes['Profesionista'] = $this->profesionista_Attr($curp,$nombre,
                                                                $apePat,$apeMat,$correo);

      // Nodo Expedicion. 2011-08-10|1|POR TESIS|2010-08-16|(FechaExionExamenProfesional)|1|2|ART. 55 LRART. 5 CONST|09|CIUDAD DE MÉXICO|
      $fechaExpedicion=$datos['_21_fechaExpedicion'];
      $idModalidadTitulacion=$datos['_22_idModalidadTitulacion'];
      $modalidadTitulacion=$datos['_23_modalidadTitulacion'];
      $fechaExamenProfesional=$datos['_24_fechaExamenProfesional'];
      $fechaEExamenProfesional=$datos['_25_fechaExencionExamenProfesional'];
      $cumplioServicioSocial=$datos['_26_cumplioServicioSocial'];
      $idfundamentoSS=$datos['_27_idFundamentoLegalServicioSocial'];
      $fundamentoSS=$datos['_28_fundamentoLegalServicioSocial'];
      $idEntidadFederativa=$datos['_29_idEntidadFederativa'];
      $eFederativa=$datos['_30_entidadFederativa'];
      $componentes['Expedicion'] = $this->expedicion_Attr($fechaExpedicion,
                                          $idModalidadTitulacion,
                                          $modalidadTitulacion,
                                          $fechaExamenProfesional,
                                          $fechaEExamenProfesional,
                                          $cumplioServicioSocial,
                                          $idfundamentoSS,$fundamentoSS,
                                          $idEntidadFederativa,$eFederativa);

      // Nodo Antecendente Ejemplo: C.E.T.I.S. NO. 80|4|BACHILLERATO|09|CIUDAD DE MÉXICO|2000-06-12|2003-08-12|(noCedula)||
      $inst=$datos['_31_institucionProcedencia'];
      $idTipoE=$datos['_32_idTipoEstudioAntecedente'];
      $tipoE=$datos['_33_tipoEstudioAntecedente'];
      $idEntFed=$datos['_34_idEntidadFederativa'];

      $entFed=$datos['_35_entidadFederativa'];
      $fechaI=$datos['_36_fechaInicio'];
      $fechaT=$datos['_37_fechaTerminacion'];
      $noCedula=$datos['_38_noCedula'];
      $componentes['Antecedente'] = $this->antecedente_Attr($inst,$idTipoE,$tipoE,
                                                            $idEntFed,$entFed,
                                                            $fechaI,$fechaT,$noCedula);

      return $componentes;
   }
   public function integraNodosSep($folio,$datos,$sello1,$sello2,$sello3)
   {
      // dd($folio,$datos,$sello1,$sello2,$sello3);
      // Integra todos los arreglos de atributos en un arreglos general
      $componentes = array();

      // Nodo Titulo Electronico Ejemplo 1.0|2345678|
      $componentes['TituloElectronico'] = $this->tituloElectronico_Attr($folio);

      // Nodo responsable 1
      $nombre='IVONNE';$apellidoPat='RAMIREZ';$apeMat='WENCE';
      $curp='RAWI6005073U0';$idCarg='9';$cargo='DIRECTOR GENERAL';$titulo='M. EN C.'; // UIES180831HDFSEP03
      $certR = 'CertificadoResponsable'; $noCertR='1682280437054458477';
      $componentes['FirmaResponsable1'] = $this->firmaResp_AttrSep(
                  $nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo,
                  $sello3,$certR,$noCertR);
      // Nodo responsable2
      $nombre='LEONARDO';$apellidoPat='LOMELI';$apeMat='VANEGAS';
      $curp='LOVL7004289W7';$idCarg='6';$cargo='SECRETARIO GENERAL';$titulo='DR.'; // UIES180831HDFSEP02
      $certR = 'CertificadoResponsable'; $noCertR='3121493390511228062';
      $componentes['FirmaResponsable2'] = $this->firmaResp_AttrSep(
                  $nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo,
                  $sello2,$certR,$noCertR);
      // Nodo responsable3
      $nombre='ENRIQUE LUIS';$apellidoPat='GRAUE';$apeMat='WIECHERS';
      $curp='GAWE510109C14';$idCarg='3';$cargo='RECTOR';$titulo='DR.';  //UIES180831HDFSEP01
      $certR = 'CertificadoResponsable'; $noCertR='7608878899635960696';
      $componentes['FirmaResponsable3'] = $this->firmaResp_AttrSep(
                  $nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo,
                  $sello1,$certR,$noCertR);

      // Nodo Institucion. Ejemplo 010033|UNIVERSIDAD TECNOLÓGICA DE AGUASCALIENTES|
      $cveInstitucion=$datos['_07_cveInstitucion'];
      $nombreInstitucion=$datos['_08_nombreInstitucion'];
      $componentes['Institucion'] = $this->institucion_Attr($cveInstitucion,
                                                            $nombreInstitucion);

      // Nodo Carrera. Ejemplo 103339|INGENIERÍA EN NANOTECNOLOGÍA|2004-08-16|2009-06-12|5|ACTA DE SESIÓN|(vacio numeroRvoe)|
      $cveCarrera=$datos['_09_cveCarrera'];
      $nombreCarrera=$datos['_10_nombreCarrera'];
      $fechaInicio=$datos['_11_fechaInicio'];
      $fechaTerminacion=$datos['_12_fechaTerminacion'];
      $idAutorizacionReconocimiento=$datos['_13_idAutorizacionReconocimiento'];
      $autorizacionReconocimiento=$datos['_14_autorizacionReconocimiento'];
      $noRvoe=$datos['_15_numeroRvoe'];
      // $nombreCarrera = 'DOCTORADO EN CIENCIAS (BIOLOGÍA)';
      $componentes['Carrera'] = $this->carrera_Attr($cveCarrera,$nombreCarrera,
                                                    $fechaInicio,$fechaTerminacion,
                                                   $idAutorizacionReconocimiento,
                                                   $autorizacionReconocimiento,$noRvoe);

      // Nodo Profesionista. Ejemplo:  AICA770112HDFRNL01|ANTONIO|ALPIZAR|CASTRO|antonio.alpizar@gmail.com|
      $curp=$datos['_16_curp'];
      $nombre=trim($datos['_17_nombre']);
      $apePat=$datos['_18_primerApellido'];
      $apeMat=$datos['_19_segundoApellido'];
      $correo=$datos['_20_correoElectronico'];
      // $curp='SELIL890909FDFRL10';$nombre = 'LUZ MARIA GRACIELA'; $apePat='Serrano'; $apeMat = 'LIMON'; $correo='biologia@gmail.com';
      $componentes['Profesionista'] = $this->profesionista_Attr($curp,$nombre,
                                                                $apePat,$apeMat,$correo);

      // Nodo Expedicion. 2011-08-10|1|POR TESIS|2010-08-16|(FechaExionExamenProfesional)|1|2|ART. 55 LRART. 5 CONST|09|CIUDAD DE MÉXICO|
      $fechaExpedicion=$datos['_21_fechaExpedicion'];
      $idModalidadTitulacion=$datos['_22_idModalidadTitulacion'];
      $modalidadTitulacion=$datos['_23_modalidadTitulacion'];
      $fechaExamenProfesional=$datos['_24_fechaExamenProfesional'];
      $fechaEExamenProfesional=$datos['_25_fechaExencionExamenProfesional'];
      $cumplioServicioSocial=$datos['_26_cumplioServicioSocial'];
      $idfundamentoSS=$datos['_27_idFundamentoLegalServicioSocial'];
      $fundamentoSS=$datos['_28_fundamentoLegalServicioSocial'];
      $idEntidadFederativa=$datos['_29_idEntidadFederativa'];
      $eFederativa=$datos['_30_entidadFederativa'];
      $componentes['Expedicion'] = $this->expedicion_Attr($fechaExpedicion,
                                          $idModalidadTitulacion,
                                          $modalidadTitulacion,
                                          $fechaExamenProfesional,
                                          $fechaEExamenProfesional,
                                          $cumplioServicioSocial,
                                          $idfundamentoSS,$fundamentoSS,
                                          $idEntidadFederativa,$eFederativa);

      // Nodo Antecendente Ejemplo: C.E.T.I.S. NO. 80|4|BACHILLERATO|09|CIUDAD DE MÉXICO|2000-06-12|2003-08-12|(noCedula)||
      $inst=$datos['_31_institucionProcedencia'];
      $idTipoE=$datos['_32_idTipoEstudioAntecedente'];
      $tipoE=$datos['_33_tipoEstudioAntecedente'];
      $idEntFed=$datos['_34_idEntidadFederativa'];
      $entFed=$datos['_35_entidadFederativa'];
      $fechaI=$datos['_36_fechaInicio'];
      $fechaT=$datos['_37_fechaTerminacion'];
      $noCedula=$datos['_38_noCedula'];
      $componentes['Antecedente'] = $this->antecedente_Attr($inst,$idTipoE,$tipoE,
                                                            $idEntFed,$entFed,
                                                            $fechaI,$fechaT,$noCedula);

      return $componentes;
   }
   public function loteCadena($fecha,$cargo)
   {
      // obtencion un lote de cadenas orignales firmadas por la Directora, Secretario o RECTOR
      switch ($cargo) {
         case 'Jtit':
            $datos = SolicitudSep::where('status', 2)->
                                where('fecha_lote',$fecha)->get();
            break;
         case 'Director':
            $datos = SolicitudSep::where('status', 3)->
                                where('fecha_lote',$fecha)->get();
            break;
         case 'SecGral':
            $datos = SolicitudSep::where('status', 4)->
                                where('fecha_lote',$fecha)->get();
            break;
         case 'Rector':
            $datos = SolicitudSep::where('status', 5)->
                                where('fecha_lote',$fecha)->get();
            break;
         default:
         $datos = SolicitudSep::where('status', 6)->
                             where('fecha_lote',$fecha)->get();
            break;
      }
      // El folio se forma por la fecha del loteCadena
      $folio = carbon::parse($fecha)->format('Ymdhis');
      $responsable = $cadenaResp = '';
      // Se recorren las cuentas de alumnos que no tienen errores
      foreach ($datos as $datosAlumno) {
         $responsable = $this->integraNodosUnam($folio,unserialize($datosAlumno->datos),$cargo);
         // generamos la cadena por alumno e integramos el lote
         $cadena = $this->cadenaOriginal($responsable,$cargo);
         $cadenaResp = $cadenaResp.'@_@'.$cadena;
      }
      $cadenaResp = substr($cadenaResp, 3,strlen($cadenaResp)-3);
      return $cadenaResp;
   }

   public function tituloElectronico_Attr($folio)
   {
     // Consulta de la Información
     $datos = array();
     $datos['xmlns'] = "https://www.sige.sep.gob.mx/titulos/";
     $datos['xmlns:xsi'] = "http://www.w3.oft/2001/XMLSchema-instace";
     $datos['version'] = '1.0';
     $datos['folioControl'] = $folio;
     $datos['xsi:schemalocation'] = "https//www.siged.sep.gob.mx/titulos/ schema.xsd";
     return $datos;
   }
   public function firmaResp_AttrSep($nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo,$sello,$certR,$noCertR)
   {
     $dato = array();
     $data['nombre'] = $nombre;
     $data['primerApellido'] = $apellidoPat;
     $data['segundoApellido'] = $apeMat;
     $data['curp'] = $curp;
     $data['idCargo'] = $idCarg;
     $data['cargo'] = $cargo;
     $data['abrTitulo'] = $titulo; // Opcional
     $data['sello'] = $sello;
     $data['certificadoResponsable'] = $certR;
     $data['noCertificadoResponsable'] = $noCertR;
     return $data;
   }
   public function firmaResp_AttrUnam($curp,$idCarg,$cargo,$titulo)
   {
     $dato = array();
     $data['curp'] = $curp;
     $data['idCargo'] = $idCarg;
     $data['cargo'] = $cargo;
     $data['abrTitulo'] = $titulo; // Opcional
     return $data;
   }
   public function institucion_Attr($cveInstitucion,$nombreInstitucion)
   {
     $data = array();
     $data['cveInstitucion'] = $cveInstitucion;
     $data['nombreInstitucion'] = $nombreInstitucion;
     return $data;
   }
   public function carrera_Attr($cveCarrera,$nombreCarrera,$fechaInicio,$fechaTerminacion,$idAutorizacionReconocimiento,$autorizacionReconocimiento,$noRvoe)
   {
     $data = array();
     $data['cveCarrera'] = $cveCarrera;
     // $data['nombreCarrera'] = utf8_encode($nombreCarrera);
     $data['nombreCarrera'] = $nombreCarrera;
     $data['fechaInicio'] = $fechaInicio; // Opcional omitir. SI esta vacio, omitir en el XML
     $data['fechaTeminacion'] = $fechaTerminacion;
     $data['idAutorizacionReconocimiento'] = $idAutorizacionReconocimiento;
     $data['autorizacionReconocimiento'] = $autorizacionReconocimiento;
     $data['numeroRvoe'] = $noRvoe; // Opcional omitir. SI esta vacio, omitir en el XML
     return $data;
   }
   public function profesionista_Attr($curp,$nombre,$apePat,$apeMat,$correo)
   {
     // dd('nombre:', $nombre,$apePat,utf8_encode($apePat),$apeMat);
     $data = array();
     $data['curp'] = $curp;
     $data['nombre'] = $nombre;
     $data['primerApellido'] = $apePat;
     $data['segundoApellido'] = $apeMat; // Opcional, omitir en el XML si esta vacio.
     $data['correoElectronico'] = $correo;
     return $data;
   }
   public function expedicion_Attr($fechaExpedicion,$idModalidadTitulacion,$modalidadTitulacion,$fechaExamenProfesional,$fechaEExamenProfesional,
                                   $cumplioServicioSocial,$idfundamentoSS,$fundamentoSS,$idEntidadFederativa,$eFederativa)
   {
     $data = array();
     $data['fechaExpedicion'] = $fechaExpedicion;
     $data['idModalidadTitulacion'] = $idModalidadTitulacion;
     $data['modalidadTitulacion'] = $modalidadTitulacion;
     $data['fechaExamenProfesional'] = $fechaExamenProfesional; // Opcional. Si esta vacio,  omitir en en XML}
     $data['fechaExencionExamenProfesional'] = $fechaEExamenProfesional; // Opcional. Si esta vacio,  omitir en en XML}
     $data['cumpioServicioSocial'] =  $cumplioServicioSocial;
     $data['idFundamentoLegalServicioSocial'] = $idfundamentoSS;
     $data['fundamentoLegalServicioSocial'] = $fundamentoSS;
     $data['inEntidadFederativa'] =  $idEntidadFederativa;
     $data['entidadFederativa'] = $eFederativa;
     return $data;
   }
   public function antecedente_Attr($inst,$idTipoE,$tipoE,$idEntFed,$entFed,$fechaI,$fechaT,$noCedula)
   {
     $data = array();
     $data['institucionProcedencia'] = $inst;
     $data['idTipoEstudioAntecedente'] = $idTipoE;
     $data['tipoEstudioAntecedente'] = $tipoE;
     $data['idEntidadFederativa'] = $idEntFed;
     $data['entidadFederativa'] = $entFed;
     $data['fechaInicio'] = $fechaI; // Opcional, si esta vacio, omitr en el XML
     $data['fechaTerminacion'] = $fechaT;
     $data['noCedula'] = $noCedula; // Opcional, si esta vacio, omitr en el XML
     return $data;
   }
   public function actualizaFLFF($cuenta9,$carrera)
   {
      // Actualiza la información de una solicitud sep.
      $digito = substr($cuenta9,8,1);
      $cuenta8 = substr($cuenta9,0,8);
      // localizamos el registro en la tabla soslicitudes-sep (el noCta de tener 9 char)
      $dato = SolicitudSep::where('num_cta',$cuenta9)->
                            where('cve_carrera',$carrera)->first();
      // Busca en  Solicitudes-Sep la fecha de emsión del titulo, el folio y la fecha proveniente de Condoc
      $query  = 'SELECT  ';
      $query .= 'tit_fec_emision_tit AS fechaE, ';
      $query .= 'tit_libro, tit_folio, tit_foja ';
      $query .= 'FROM Titulos ';
      $query .= "where tit_ncta = '".$cuenta8."' and tit_plancarr='".$carrera."'";
      $infoFLFF = (array)DB::connection('sybase')->select($query);
      $fechaE = Carbon::parse($infoFLFF[0]->fechaE);
      // Busca en Condoc, los datos y errores del actual numero de cuenta y carrera
      $listaErrores = array();
      // Consulta el conjunto con destino al campo "datos" de solicitudes_sep (contiene datos y errores)
      $datosyerrores = $this->integraConsulta($cuenta8,$digito,$carrera);
      // actualizamos datos y errores, fecha_emision_tit, libro, foja y folio.
      if(count($infoFLFF)!==0)
      {
         // Actualizamos los 4 campos en Solicitudes Sep.
         DB::table('solicitudes_sep')
                    ->where('id', $dato->id)
                    ->update(['fec_emision_tit' => $fechaE,
                              'libro'   => trim($infoFLFF[0]->tit_libro),
                              'foja'    => trim($infoFLFF[0]->tit_foja),
                              'folio'   => trim($infoFLFF[0]->tit_folio),
                              'datos'   => serialize($datosyerrores[0]),
                              'errores' => serialize($datosyerrores[1])
                     ]);
      }
   }
   public function actualizaFLFFIds($ids)
   {
      // Actualiza la información de varias solicitudes previamente seleccionadas sep.
      foreach ($ids as $value) {
         $dato = SolicitudSep::find($value);
         $this->actualizaFLFF($dato->num_cta, $dato->cve_carrera);
      }
   }
   public function actualiza()
   {
      $lists = SolicitudSep::all();
      $total = count($lists);
      $listaErrores = array();
      foreach ($lists as $key => $elemento)
      {
         $digito = substr($elemento->num_cta,8,1);
         $cuenta = substr($elemento->num_cta,0,8);
         $carrera = $elemento->cve_carrera;
         // $cuenta = '08140248';$carrera = '0025139'; $digito = '9';
         $datos = $this->integraConsulta($cuenta,$digito,$carrera);
         // En esta seccion se consultan los sellos del registro de usuario.
         $sello1 = 'Sello 1'; $sello2 = 'Sello2'; $sello3 = 'Sello3';
         $nodos = $this->IntegraNodos($datos[0],$sello1,$sello2,$sello3);
         // Obtención de XML
         // $toXml = $this->tituloXml($nodos);
         // Obtención de la cadena original
         // $cadenaOriginal = $this->cadenaOriginal($nodos);
         // Obtención de los Errores.
         if (isset($datos[1])==null) {
            $errores = 'Sin errores';
            if (!in_array($errores,$listaErrores)) {
               $listaErrores[] = $value;
            }
         } else {
            $errores = serialize($datos[1]);
            foreach ($datos[1] as $value) {
               if (!in_array($value,$listaErrores)) {
                  $listaErrores[] = $value;
               }
            }
         }

         $errores = (isset($datos[1])==null)? 'Sin errores': serialize($datos[1]) ;
         // Consulta de la informacion
         $alumno = SolicitudSep::find($elemento->id);
         $alumno->datos = serialize($datos[0]);
         $alumno->errores = $errores;
         $alumno->save();
         // dd($cadenaOriginal,$toXml->xml(),$errores);
      }
      // sort($listaErrores);
      // dd($listaErrores);
      // // dd($lists);
      // $title = 'Solicitudes para Envio de Firma';
      // return view('menus/lista_solicitudes', compact('title','lists', 'total'));
   }
   public function actualizaxFecha($fecha)
   {
      $lists = SolicitudSep::where(Carbon::parse($fecha))->get();
      $total = count($lists);
      $listaErrores = array();
      foreach ($lists as $key => $elemento)
      {
         $digito = substr($elemento->num_cta,8,1);
         $cuenta = substr($elemento->num_cta,0,8);
         $carrera = $elemento->cve_carrera;
         $datos = $this->integraConsulta($cuenta,$digito,$carrera);
         dd('actualiza por fecha');
         if (isset($datos[1])==null) {
            $errores = 'Sin errores';
            if (!in_array($errores,$listaErrores)) {
               $listaErrores[] = $value;
            }
         } else {
            $errores = serialize($datos[1]);
            foreach ($datos[1] as $value) {
               if (!in_array($value,$listaErrores)) {
                  $listaErrores[] = $value;
               }
            }
         }

         $errores = (isset($datos[1])==null)? 'Sin errores': serialize($datos[1]) ;
         // Consulta de la informacion
         $alumno = SolicitudSep::find($elemento->id);
         $alumno->datos = serialize($datos[0]);
         $alumno->errores = $errores;
         $alumno->save();
         // dd($cadenaOriginal,$toXml->xml(),$errores);
      }
      // sort($listaErrores);
      // dd($listaErrores);
      // // dd($lists);
      // $title = 'Solicitudes para Envio de Firma';
      // return view('menus/lista_solicitudes', compact('title','lists', 'total'));
   }

  // XML para Cancelación de Títulos Electrónicos
  public function cancelaTituloXml($nodos){
     $tituloXML = new FluidXml('CancelaTituloElectronico');
     foreach ($nodos['CancelaTituloElectronico'] as $key => $value) {
       $tituloXML->setAttribute($key, $value);
     }
     $tituloXML->addChild('FolioControl','', $nodos['FolioControl']);
     $tituloXML->addChild('MotCancelacion',  $nodos['MotCancelacion']);
     $tituloXML->addChild('Autenticacion',   $nodos['Autenticacion']);

     return $tituloXML;
  }

  public function attrCancelaTE(){
    // Consulta de la Información
    $datos = array();
    $datos['xmlns'] = "https://www.sige.sep.gob.mx/titulos/";
    $datos['xmlns:xsi'] = "http://www.w3.oft/2001/XMLSchema-instace";
    $datos['version'] = '1.0';
    $datos['xsi:schemalocation'] = "https//www.siged.sep.gob.mx/titulos/ schema.xsd";
    return $datos;
  }

  public function attrFolio($folio){
    $data = array();
    $data['folio'] = $folio;
    return $data;
  }

  public function attrMotivo($motivo){
    $data = array();
    $data['cveMotivo'] = $motivo;
    return $data;
  }

  public function attrAutenticacion($user, $password){
    $data = array();
    $data['usuario'] = $user;
    $data['password'] = $password;
    return $data;
  }

  public function integraNodosC($folio, $motivo, $user, $password){
    // Integra todos los arreglos de atributos en un arreglos general
    $componentes = array();
    $componentes['CancelaTituloElectronico'] = $this->attrCancelaTE();
    $componentes['FolioControl'] = $this->attrFolio($folio);
    $componentes['MotCancelacion'] = $this->attrMotivo($motivo);
    $componentes['Autenticacion'] = $this->attrAutenticacion($user,$password);
    return $componentes;
  }

}
