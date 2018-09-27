<?php

namespace App\Http\Traits\Consultas;

use DB;
use Illuminate\Http\Request;
use Spatie\ArrayToXml\ArrayToXml;
use \FluidXml\FluidXml;
use App\Models\Estudio;
use App\Models\Entidad;
use App\Models\SolicitudSep;
use App\Models\Modo;
use Carbon\Carbon;

trait XmlCadenaErrores {

   public function integraConsulta($cuenta, $digito, $carrera)
   {
      // integramos consulta y errores,
      $errores = $items = $resultado = array();
      // Consulta por porOmision
      $consulta = $this->porOmision($cuenta,$digito,$carrera);
      $items = array_merge($items,$consulta);
      // Primer consulta
      $consulta = $this->carrerasProcedencia($cuenta,$digito,$carrera);
      if (isset($consulta['errores'])!=null) {
         $errores = array_merge($errores,$consulta['errores']);
         unset($consulta['errores']);
      }
      $items = array_merge($items,$consulta);
      // Segunda consultaDatos
      $consulta = $this->titulosEscprocedencia($cuenta,$carrera);
      if (isset($consulta['errores'])!=null) {
         $errores = array_merge($errores,$consulta['errores']);
         unset($consulta['errores']);
      }
      $items = array_merge($items,$consulta);
      // Tecer consulta de Datos
      $nivel = $items['atrib_nivelProf']; // Proviene de titulosProcedencia
      $consulta = $this->antecedente($cuenta,$nivel);
      if (isset($consulta['errores'])!=null) {
         $errores = array_merge($errores,$consulta['errores']);
         unset($consulta['errores']);
      }
      $items = array_merge($items,$consulta);
      unset($items['atrib_nivelProf']); // Eliminamos el nivel. No se ocupa en el documento final.
      // Cuarta consultaDatos
      $consulta = $this->titulosExamenes($cuenta,$carrera);
      if (isset($consulta['errores'])!=null) {
         $errores = array_merge($errores,$consulta['errores']);
         unset($consulta['errores']);
      }
      $items = array_merge($items,$consulta);
      // Quinta consultaDatos
      $consulta = $this->titulosDatos($cuenta,$digito,$carrera);
      if (isset($consulta['errores'])!=null) {
         $errores = array_merge($errores,$consulta['errores']);
         unset($consulta['errores']);
      }
      $items = array_merge($items,$consulta);

      // Ordenamos lo items
      ksort($items);

      // Verificamos si no se presentaron errores, colocamos el campo "sin errores"
      if ($errores==[]) {
         $errores['sin errores'] = 'sin errores';
      }

      // Integramos errores y Datos
      $resultado[0] = $items;
      $resultado[1] = $errores;

      return $resultado;
   }
   public function carrerasProcedencia($cuenta,$digito,$carrera)
   {
      // Clave y Nombre de la carrera SEP considerando la clave de carrera (unam) y el No. de Cuenta
      $query1  = 'SELECT ';
      $query1 .= "ori_cve_profesiones AS _09_cveCarrera, ";
      $query1 .= "carrera             AS _10_nombreCarrera ";
      $query1 .= 'from Datos ';
      $query1 .= 'join Orientaciones on dat_car_actual = ori_plancarr and dat_orientacion = ori_orienta ';
      $query1 .= 'join Carreras_Profesiones on convert(int,ori_cve_profesiones) = clave_carrera ';
      $query1 .= "where dat_ncta = '".$cuenta."' and dat_dig_ver = '".$digito."' and dat_car_actual = '".$carrera."' ";
      //  Buscamos el nombre (unam) de la carrera
      $query2  = 'SELECT ';
      $query2 .= 'ori_plancarr      AS _09_cveCarrera, ';
      $query2 .= 'ori_orienta_nom   AS _10_nombreCarrera ';
      $query2 .= 'from Orientaciones ';
      $query2 .= "where ori_plancarr = '".$carrera."'";

      $errores = $datos =  array(); // errores y faltantes
      // Buscamos el nombre y carrera SEP
      $info = (array)DB::connection('sybase')->select($query1);
      if ($info==[]) {
         // No existe la clave SEP para la clave de carrera local,
         $errores['_09_cveCarrera'] = 'Sin clave SEP';
         $info2 = (array)DB::connection('sybase')->select($query2);
         if ($info2==[]) {
            // No existe el nombre para esta carrera_Attr
            $errores['_10_nombreCarrera'] = "Sin nombre de carrera";
         } else {
            // para no ir vacio, Asignamos Clave y Carrera (unam) pero levantamos el error "Sin clave SEP"
            $datos = $info2[0];
         }
      } else {
         // pasamos Nombre y Carrera SEP al Arreglo Incluye _09_cveCarrera  y _10_nombreCarrera
         $datos = $info[0];
      }

      // Verificamos que el query de datos SEP no esta vacio.
      if ($datos!=[]) {
         //  Encontro clave y nombre SEP para la clave local ($cuenta y $carrera)
         $resultado = (array)$datos;
      } else {
         // No existe nombre de carrera ni clave
         $resultado['_09_cveCarrera'] = '----';
         $resultado['_10_nombreCarrera'] = '----';
      }

      // Si tenenmos errores, se los agregamos al arreglo
      if ($errores!=[]) {
         $resultado['errores'] = $errores;
      }

      // No tenemos numero Rvoe.
      $resultado['_15_numeroRvoe'] = '----';

      return $resultado;
   }
   public function titulosEscprocedencia($cuenta,$carrera)
   {
      // Periodo en que se cursa la carrera. Las fechas de deducen de un periodo (ejem: 20092011)
      // y se extrae el nivel para buscar posteriormente la escuela anterior (antecedente) en el
      // nivel anterior
      $query = 'SELECT ';
      $query .= "escpro_fec_exp     AS carrera_fechas, ";
      $query .= "escpro_nivel_pro   AS atrib_nivelProf ";
      $query .= "FROM Titulos ";
      $query .= "JOIN Escprocedencia ON escpro_ncta = tit_ncta AND escpro_plancarr_act = tit_plancarr ";
      $query .= "where escpro_ncta = '".$cuenta."' and tit_plancarr='".$carrera."'";
      $info = (array)DB::connection('sybase')
                     ->select($query);

      $datos = $errores = array();
      if ($info==[]) {
            $datos['_11_fechaInicio'] = '----';
            $datos['_12_fechaTerminacion'] = '----';
            $datos['atrib_nivelProf'] =  '--';
            $errores['_11_fechaInicio'] = 'periodo de estudios irregular';
            $errores['_12_fechaTerminacion'] = 'periodo de estudios irregular';
            $errores['_EscuelaProcedencia'] = 'carrera de procedencia sin clave de nivel';
      } else {
         // Existen el periode estudio y en nivle profesionl
         $datos = (array)$info[0];
         // Se separan los periodos anuales y se convierten en fechas yyyy/01/01
         $fecha1 = substr($datos['carrera_fechas'],0,4).'/01/01';
         $fecha2 = substr($datos['carrera_fechas'],4,4).'/01/01';
         // introducimos al arreglo los valores de las fechas, independientemente si tienen Errores
         $datos['_11_fechaInicio'] = $fecha1;
         $datos['_12_fechaTerminacion'] = $fecha2;
         // Errores: verificamos que los periodos sean correctos p.ejemplo 20082011 (de 2008 a 2011)
         if(strlen($datos['carrera_fechas'])!=8)
         {
            // El periodo de longitud diferente a 8 char solo puede suceder en estudios de preparatoria
            $errores['_11_fechaInicio'] = 'periodo de estudios irregular';
            $errores['_12_fechaTerminacion'] = 'periodo de estudios irregular';
         } else {
            // Debe poder formarse una fecha y esta no puede ser igual o menor a la final
            $datos['_11_fechaInicio'] = $fecha1;
            if (!strtotime($fecha1) || ($fecha1>$fecha2)) {
               $errores['_11_fechaInicio'] = 'inicio de estudios inválido';
            }
            // Debe poder formarse una fecha y esta no puede ser igual o menor a la final
            $datos['_12_fechaTerminacion'] = $fecha2;
            if (!strtotime($fecha2) || $fecha1>$fecha2) {
               $errores['_12_fechaTerminacion'] = 'término de estudios inválido';
            }
         }
         // Ya no necesitamos el campo 'carrera_fechas' en los datos de salida. lo retiramos del arreglo
         unset($datos['carrera_fechas']);
      }

      if ($errores!=[]) {
         $datos['errores'] = $errores;
      }

      // Retiramos el campo de antec_fechas
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
      $query2 .= "where num_cta = '".$cuenta.$digito."'";

      // Traemos los datos desde la bdd que los alumnos actualizan y del condoc
      $info_sybase = (array)DB::connection('sybase')->select($query1);
      $info_mysql = DB::connection('condoc_eti')->select($query2);
      // $dd($info_sybase,$info_mysql);

      // Separamos los nombres registrados en condoc para compararlo con el proporcionado por el alumno
      $nombreCondoc = $nombre = $apellidoP = $apellidoM = '';
      if (!$info_sybase==[]) {
         $asterisco = explode('*',$info_sybase[0]->_17_nombre);
         switch (count($asterisco)) {
             case 1: // un solo nombre
                 $nombre = $asterisco[0];
                 $nombreCondoc = $nombre;
                 break;
             case 2: // nombre y apellido paterno
                 $apellidoP = $asterisco[0];
                 $nombre = $asterico[1];
                 $nombreCondoc = $apellidoP.'*'.$nombre;
                 break;
             case 3: // nombre, apellido paterno y apellido materno
                 $apellidoP = $asterisco[0];
                 $apellidoM = $asterisco[1];
                 $nombre = $asterisco[2];
                 $nombreCondoc = $apellidoP.'*'.$apellidoM.'*'.$nombre;
                 break;
         }
      }
      $datos = $errores = array();
      // Consultamos si el usuario y actualizo los datos
      if ($info_mysql==[]) {
         // El usuario aun no ha actualizado los datos
         $datos['_16_curp'] = '----';
         // Si el alumno aún no ha registrado el nombre, lo agregamos de CONDOC
         $datos['_17_nombre']          = '----';
         $datos['_18_primerApellido']  = '----';
         $datos['_19_segundoApellido'] = '----';
         $datos['_20_correoElectronico'] = '----';
         $errores['_16_curp'] = 'alumno no ha proporcionado el curp';
         $errores['_17_nombre'] = 'alumno no a llenado nombre formulario';
         $errores['_18_primerApellido']  = 'alumno no a llenado apellido paterno formulario';
         $errores['_19_segundoApellido'] = 'alumno no a llenado apellido materno formulario';
         $errores['_20_correoElectronico'] = 'alumno no ha llenado correo electronico en formulario';
      } else
      {
         // El usuario si ha registrado los datos
         $datos = (array)$info_mysql[0];
         // Validamos el campo del curp
         if  ($datos['_16_curp']==null) {
            // no existe el curp
              $datos['_16_curp'] = '----';
            $errores['_16_curp'] = 'alumno aún no ha proporcionado Curp';
         } else {
            // Si existe, el curp, validamos si el automno ya valido la transferencia
            if ($info_mysql[0]->autoriza=='0')
            {
               // El curp existe de tabla alumnos, pero la bandera de autoriza esta en blanco
               $errores['_16_curp'] = 'alumno aún no ha autorizado transferencia de CURP a SEP';
            }
         }
         // Creamos el nombre con '*' para compararlo con el de condoc
         $nombreMysql = $info_mysql[0]->apellido1.'*'.$info_mysql[0]->apellido2.'*'.$info_mysql[0]->nombres;
         // Verificamos si el nombre es válido
         if($nombreMysql=='')
         {
            // No existe el nombre del usuario, levantamos un ellror
            $datos['_17_nombre'] = '----';
            $datos['_18_primerApellido']  = '----';
            $datos['_19_segundoApellido'] = '----';
            $errores['_17_nombre'] = 'alumno aún no ha proporcionado nombre y apellidos';
         } else {
            // el nombre ya se encuentra registrado en datos, pero falta validarlo
            $datos['_17_nombre']          = $info_mysql[0]->nombres;
            $datos['_18_primerApellido']  = $info_mysql[0]->apellido1;
            $datos['_19_segundoApellido'] = $info_mysql[0]->apellido2;
            unset($datos['nombres']);  unset($datos['apellido1']);
            unset($datos['apellido2']);unset($datos['autoriza']);
            // Ya utilizamos los camos nombres y apllidos, se retiran del arreglo firmaResponsable
            // Validamos incosistencias.
            if ($nombreMysql!=$nombreCondoc) {
               $errores['_17_nombre'] = 'nombre y apellidos del alumno no coinciden con condoc';
            } else {
               // Los nombres del formulario y en condoc coinciden, pero verificamos si ha autorizado
               if ($info_mysql[0]->autoriza=='0'){
                  // los nombres si coinciden pero aun lo lo autoriza el usuario
                  $errores['_17_nombre'] = 'alumno aún no ha autorizado transferencia de Nombre y apellidos a SEP';
               }

            }
         }

         // Validamos el campo del correo Electronico
         if  ($datos['_20_correoElectronico']==null) {
            // no existe el curp
              $datos['_20_correoElectronico'] = '----';
            $errores['_20_correoElectronico'] = 'alumno aún no ha proporcionado Curp';
         } else {
            // Si existe, el curp, validamos si el automno ya valido la transferencia
            $datos['_20_correoElectronico'] = $info_mysql[0]->_20_correoElectronico;
            if ($info_mysql[0]->autoriza=='0')
            {
               // El curp existe de tabla alumnos, pero la bandera de autoriza esta en blanco
               $errores['_20_correoElectronico'] = 'alumno aún no ha autorizado transferencia de correo a SEP';
            }
         }

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

      $datos = $errores = array();
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
            $datos['_23_ModalidadTitulacion'] =   '----';
            $errores['_23_ModalidadTitulacion'] = 'modalidad de titulación inexistente';

         } else {
            // Modalidad existencia si existe en catalogo.
            $datos['_23_modalidadTitulacion'] = $modo->MODALIDAD_TITULACION;
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

      return $datos;
   }
   public function antecedente($cuenta,$nivel)
   {
      // ante periodo se divide en 2 fechas y se omite de la salida
      $query = "Select distinct catproc_nombre as _31_institucionProcedencia, ";
      $query .= "escpro_cveproc as clave_escuela_previa, ";
      $query .= "escpro_nivel_pro as ante_nivel, ";
      $query .= "catproc_ent_fed as _34_idEntidadFederativa, ";
      $query .= "escpro_fec_exp as ante_periodo ";
      $query .= "from Escprocedencia ";
      $query .= "join Catprocedencia on catproc_cve = escpro_cveproc ";
      $query .= "join Paisedos on escpro_cveproc = catproc_cve ";
      $query .= "where escpro_ncta = '".$cuenta."' and escpro_nivel_pro < '".$nivel."' "; // $nivel es $info['nivelProf']
      $query .= "and escpro_nivel_pro<>'07' ";
      $query .= "order by escpro_nivel_pro asc";

      $info = (array)DB::connection('sybase')
                     ->select($query);
      // Escuela antecedente.
      $datos = $errores = $resultado = array(); $cuenta = 0;
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
            $resultado['_32_idTipoEstudioAntecedente'] = $tipoEstudio->ID_TIPO_ESTUDIO_ANTECEDENTE;
            $resultado['_33_tipoEstudioAntecedente'] = $tipoEstudio->TIPO_ESTUDIO_ANTECEDENTE;
         } else {
            $resultado['_32_idTipoEstudioAntecedente'] = '----';
            $resultado['_33_tipoEstudioAntecedente'] = '----';
            $errores['_32_idTipoEstudioAntecedente'] = 'tipo de estudio antecedente inválido';
         }
         // Mapeo de la entidad (Unam y Sep manejan diferentes claves)
         if ($datos['_34_idEntidadFederativa']<'00033') {
               $entidad = Entidad::where('pais_cve',$datos['_34_idEntidadFederativa'])->first();
         } else {
               // Todos las entidades se dejan como "EXTRANJERO"
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
         // verificamos la variable que contiene un periodo (> 8 char) o una fecha (8 char)
         if(strlen($datos['ante_periodo'])==8)
         {
            // Solo al nivel bachillerato se le permite tener una longitud de 8 caracteres.
            // Preguntamos si el nivel es bachillerato (catalogo unam nivel 02)
            if ($datos['ante_nivel']=='02') {
               // Se trata de bachillerato, entonces el formato de periordo es ddmmaaaa
               // y se trata de una sola fecha
               $fecha = substr($datos['ante_periodo'],4,4).'/'; // año
                        substr($datos['ante_periodo'],2,2).'/'; // mes
                        substr($datos['ante_periodo'],0,2); // dia
               // veficamos si la fecha es una fecha valida
               if (!strtotime($fecha)) {
                  // La fecha no se reconoce como valida
                  // Se prueba si se trata de un periodo
                  $fecha1 = substr($datos['ante_periodo'],0,4).'/01/01';
                  $fecha2 = substr($datos['ante_periodo'],4,4).'/01/01';
                  // unset($resultado['antec_fechas']);
                  if (!strtotime($fecha1)) {
                     $resultado['_36_fechaInicio'] = '----';
                     $errores['_36_fechaInicio'] = 'inicio de estudios antecedente inválido';
                  } else {
                     $resultado['_36_fechaInicio'] = $fecha1;
                  }
                  if (!strtotime($fecha2)) {
                     $resultado['_37_fechaTerminacion'] = '----';
                     $errores['_37_fechaTerminacion'] = 'término de estudios antecedente inválido';
                  } else {
                     $resultado['_37_fechaTerminacion'] = $fecha2;
                  }
                  if (strtotime($fecha1)>strtotime($fecha2)) {
                     $errores['_37_fechaTerminacion'] = 'Periodo de estudios antecedente inválido';
                  }

               } else {
                  // El periodo es una fecha valida.
                  // Se colocan las llaves independientemente si tienen error
                  $resultado['_36_fechaInicio'] = $fecha;
                  $resultado['_37_fechaTerminacion'] = '----';
                  $errores['_36_fechaInicio'] = 'periodo de estudios antecedentes irregular';
               }
            } else {
               // el periodo no tiene 8 caracteres, por lo que se trata de un periodo
               $fecha1 = substr($datos['ante_periodo'],0,4).'/01/01';
               $fecha2 = substr($datos['ante_periodo'],4,4).'/01/01';
               // unset($resultado['antec_fechas']);
               if (!strtotime($fecha1)) {
                  $resultado['_36_fechaInicio'] = '----';
                  $errores['_36_fechaInicio'] = 'inicio de estudios antecedente inválido';
               } else {
                  $resultado['_36_fechaInicio'] = $fecha1;
               }
               if (!strtotime($fecha2)) {
                  $resultado['_37_fechaTerminacion'] = '----';
                  $errores['_37_fechaTerminacion'] = 'término de estudios antecedente inválido';
               } else {
                  $resultado['_37_fechaTerminacion'] = $fecha2;
               }
               if (strtotime($fecha1)>strtotime($fecha2)) {
                  $errores['_37_fechaTerminacion'] = 'Periodo de estudios antecedente inválido';
               }
            }
         } else {
            // El periodo no tiene 8 caracteres
            $resultado['_36_fechaInicio'] = $datos['ante_periodo'];
            $resultado['_37_fechaTerminacion'] = '----';
            $errores['_36_fechaInicio'] = 'periodo de estudios antecedente longitud irregular';
         }
      }
      else {
         // la consulta no arrojo resultados
         $resultado['_31_institucionProcedencia'] = '----';
         $resultado['_32_idTipoEstudioAntecedente'] = '----';
         $resultado['_33_tipoEstudioAntecedente'] = '----';
         $resultado['_35_entidadFederativa'] = '----';
         $resultado['_36_fechaInicio'] = '----';
         $resultado['_37_fechaTerminacion'] = '----';
         $errores['_31_institucionProcedencia'] = 'escuela de procedencia sin datos';
      }
      // No tenemos el numero de cedula del periodo anterior.
      $resultado['_38_noCedula'] = '----';
      // agregamos los errores.
      if ($errores!=[]) {
         $resultado['errores'] = $errores;
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
      // Arreglo de valores por omisión.
      $datos = array();
      $datos['_01_version'] = '1.0';
      $datos['_07_cveInstitucion'] = '090001';
      $datos['_08_nombreInstitucion'] = 'UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO';
      $datos['_13_idAutorizacionReconocimiento'] = '8';
      $datos['_14_autorizacionReconocimiento'] = 'DECRETO DE CREACIÓN';
      $datos['_27_idFundamentoLegalServicioSocial'] = '2';
      $datos['_28_fundamentoLegalServicioSocial'] = 'ART. 55 LRART. 5 CONST';
      $datos['_29_idEntidadFederativa'] = '09';
      $datos['_30_entidadFederativa'] = 'CIUDAD DE MÉXICO';

      return $datos;
   }

   public function cadenaOriginal($nodos,$tipo)
   {
      $cadenaOriginal = '||';
      $cadenaOriginal.= $nodos['TituloElectronico']['version'].'|';
      $cadenaOriginal.= $nodos['TituloElectronico']['folioControl'].'|';
      switch ($tipo) {
         case 'General': // firma el Director General
               $cadenaOriginal.= $nodos['FirmaResponsable1']['curp'].'|';
               $cadenaOriginal.= $nodos['FirmaResponsable1']['idCargo'].'|';
               $cadenaOriginal.= $nodos['FirmaResponsable1']['cargo'].'|';
               $cadenaOriginal.= ($nodos['FirmaResponsable1']['abrTitulo']=='----')? '|':
               $nodos['FirmaResponsable1']['abrTitulo'].'|'; // opcional
            break;
         case 'Secretario':  // firma el Secretario General
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
         default:
            break;
      }

      $cadenaOriginal.= $nodos['Institucion']['cveInstitucion'].'|';
      $cadenaOriginal.= $nodos['Institucion']['nombreInstitucion'].'|';

      $cadenaOriginal.= $nodos['Carrera']['cveCarrera'].'|';
      $cadenaOriginal.= $nodos['Carrera']['nombreCarrera'].'|';
      $cadenaOriginal.= ($nodos['Carrera']['fechaInicio']=='----')? '|' :
                         $nodos['Carrera']['fechaInicio'].'|'; // opcional
      $cadenaOriginal.= $nodos['Carrera']['fechaTeminacion'].'|';
      $cadenaOriginal.= $nodos['Carrera']['idAutorizacionReconocimiento'].'|';
      $cadenaOriginal.= $nodos['Carrera']['autorizacionReconocimiento'].'|';
      $cadenaOriginal.= ($nodos['Carrera']['numeroRvoe']=='----')? '|' :
                         $nodos['Carrera']['numeroRvoe'].'|'; // opcional

      $cadenaOriginal.= $nodos['Profesionista']['curp'].'|';
      $cadenaOriginal.= $nodos['Profesionista']['nombre'].'|';
      $cadenaOriginal.= $nodos['Profesionista']['primerApellido'].'|';
      $cadenaOriginal.= ($nodos['Profesionista']['segundoApellido']=='----')? '|' :
                         $nodos['Profesionista']['segundoApellido'].'|'; // opcional
      $cadenaOriginal.= $nodos['Profesionista']['correoElectronico'].'|';

      $cadenaOriginal.= $nodos['Expedicion']['fechaExpedicion'].'|';
      $cadenaOriginal.= $nodos['Expedicion']['idModalidadTitulacion'].'|';
      $cadenaOriginal.= $nodos['Expedicion']['modalidadTitulacion'].'|';
      $cadenaOriginal.= ($nodos['Expedicion']['fechaExamenProfesional']=='----')? '|' :
                         $nodos['Expedicion']['fechaExamenProfesional'].'|'; // opcional
      $cadenaOriginal.= ($nodos['Expedicion']['fechaExencionExamenProfesional']=='----')? '|':
                         $nodos['Expedicion']['fechaExencionExamenProfesional'].'|'; // opcional
      $cadenaOriginal.= $nodos['Expedicion']['cumpioServicioSocial'].'|';
      $cadenaOriginal.= $nodos['Expedicion']['idFundamentoLegalServicioSocial'].'|';
      $cadenaOriginal.= $nodos['Expedicion']['fundamentoLegalServicioSocial'].'|';
      $cadenaOriginal.= $nodos['Expedicion']['inEntidadFederativa'].'|';
      $cadenaOriginal.= $nodos['Expedicion']['entidadFederativa'].'|';

      $cadenaOriginal.= $nodos['Antecedente']['institucionProcedencia'].'|';
      $cadenaOriginal.= $nodos['Antecedente']['idTipoEstudioAntecedente'].'|';
      $cadenaOriginal.= $nodos['Antecedente']['tipoEstudioAntecedente'].'|';
      $cadenaOriginal.= $nodos['Antecedente']['idEntidadFederativa'].'|';
      $cadenaOriginal.= $nodos['Antecedente']['entidadFederativa'].'|';
      $cadenaOriginal.= ($nodos['Antecedente']['fechaInicio']=='----')? '|' :
                         $nodos['Antecedente']['fechaInicio'].'|'; // opcional
      $cadenaOriginal.= $nodos['Antecedente']['fechaTerminacion'].'|';
      $cadenaOriginal.= ($nodos['Antecedente']['noCedula']=='----')? '|' :
                         $nodos['Antecedente']['noCedula'].'|'; // opcional
      $cadenaOriginal = '||';

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
   public function integraNodos($datos,$sello1,$sello2,$sello3)
   {
      // Integra todos los arreglos de atributos en un arreglos general
      $componentes = array();

      // Nodo Titulo Electronico Ejemplo 1.0|2345678|
      $folio = '201800001';
      $componentes['TituloElectronico'] = $this->tituloElectronico_Attr($folio);

      // Nodo Firma Responsable Ejemplo EICA750212HDFRNL01|3|RECTOR|LIC.|
      $nombre='Rector';$apellidoPat='Rector';$apeMat='UNAM';
      $curp='TSEP180817HRECTR00';$idCarg='3';$cargo='RECTOR';$titulo='DR.';
      $certR = 'CertificadoResponsable'; $noCertR='Numero de CertificadoResponsanble';
      $componentes['FirmaResponsable1'] = $this->firmaResp_Attr($nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo,$sello1,$certR,$noCertR);

      $nombre='Secretario';$apellidoPat='General';$apeMat='UNAM';
      $curp='TSEP180817HSECRE00';$idCarg='6';$cargo='SECRETARIO GENERAL';$titulo='DR.';
      $certR = 'CertificadoResponsable'; $noCertR='Numero de CertificadoResponsanble';
      $componentes['FirmaResponsable2'] = $this->firmaResp_Attr($nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo,$sello2,$certR,$noCertR);

      $nombre='Directora';$apellidoPat='DGAE';$apeMat='UNAM';
      $curp='TSEP180817HDGRAL00';$idCarg='9';$cargo='DIRECTOR GENERAL';$titulo='MTRA.';
      $certR = 'CertificadoResponsable'; $noCertR='Numero de CertificadoResponsanble';
      $componentes['FirmaResponsable3'] = $this->firmaResp_Attr($nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo,$sello3,$certR,$noCertR);

      // Nodo Institucion. Ejemplo 010033|UNIVERSIDAD TECNOLÓGICA DE AGUASCALIENTES|
      $cveInstitucion=$datos['_07_cveInstitucion'];$nombreInstitucion=$datos['_08_nombreInstitucion'];
      $componentes['Institucion'] = $this->institucion_Attr($cveInstitucion,$nombreInstitucion);

      // Nodo Carrera. Ejemplo 103339|INGENIERÍA EN NANOTECNOLOGÍA|2004-08-16|2009-06-12|5|ACTA DE SESIÓN|(vacio numeroRvoe)|
      $cveCarrera=$datos['_09_cveCarrera'];$nombreCarrera=$datos['_10_nombreCarrera'];$fechaInicio=$datos['_11_fechaInicio'];$fechaTerminacion=$datos['_12_fechaTerminacion'];
      $idAutorizacionReconocimiento=$datos['_13_idAutorizacionReconocimiento'];$autorizacionReconocimiento=$datos['_14_autorizacionReconocimiento'];$noRvoe=$datos['_15_numeroRvoe'];
      // $nombreCarrera = 'DOCTORADO EN CIENCIAS (BIOLOGÍA)';
      $componentes['Carrera'] = $this->carrera_Attr($cveCarrera,$nombreCarrera,$fechaInicio,$fechaTerminacion,$idAutorizacionReconocimiento,$autorizacionReconocimiento,$noRvoe);

      // Nodo Profesionista. Ejemplo:  AICA770112HDFRNL01|ANTONIO|ALPIZAR|CASTRO|antonio.alpizar@gmail.com|
      $curp=$datos['_16_curp'];$nombre=trim($datos['_17_nombre']);$apePat=$datos['_18_primerApellido'];$apeMat=$datos['_19_segundoApellido'];$correo=$datos['_20_correoElectronico'];
      // dd('codificacion de nombre:',mb_detect_encoding($nombre),$nombre);
      // $curp='SELIL890909FDFRL10';$nombre = 'LUZ MARIA GRACIELA'; $apePat='Serrano'; $apeMat = 'LIMON'; $correo='biologia@gmail.com';
      $componentes['Profesionista'] = $this->profesionista_Attr($curp,$nombre,$apePat,$apeMat,$correo);

      // Nodo Expedicion. 2011-08-10|1|POR TESIS|2010-08-16|(FechaExionExamenProfesional)|1|2|ART. 55 LRART. 5 CONST|09|CIUDAD DE MÉXICO|
      $fechaExpedicion=$datos['_21_fechaExpedicion'];$idModalidadTitulacion=$datos['_22_idModalidadTitulacion'];$modalidadTitulacion=$datos['_23_modalidadTitulacion'];$fechaExamenProfesional=$datos['_24_fechaExamenProfesional'];
      $fechaEExamenProfesional=$datos['_25_fechaExencionExamenProfesional'];$cumplioServicioSocial=$datos['_26_cumplioServicioSocial'];$idfundamentoSS=$datos['_27_idFundamentoLegalServicioSocial'];$fundamentoSS=$datos['_28_fundamentoLegalServicioSocial'];
      $idEntidadFederativa=$datos['_29_idEntidadFederativa'];$eFederativa=$datos['_30_entidadFederativa'];
      $componentes['Expedicion'] = $this->expedicion_Attr($fechaExpedicion,$idModalidadTitulacion,$modalidadTitulacion,$fechaExamenProfesional,$fechaEExamenProfesional,
                                                          $cumplioServicioSocial,$idfundamentoSS,$fundamentoSS,$idEntidadFederativa,$eFederativa);

      // Nodo Antecendente Ejemplo: C.E.T.I.S. NO. 80|4|BACHILLERATO|09|CIUDAD DE MÉXICO|2000-06-12|2003-08-12|(noCedula)||
      $inst=$datos['_31_institucionProcedencia'];$idTipoE=$datos['_32_idTipoEstudioAntecedente'];$tipoE=$datos['_33_tipoEstudioAntecedente'];$idEntFed=$datos['_34_idEntidadFederativa'];$entFed=$datos['_35_entidadFederativa'];
      $fechaI=$datos['_36_fechaInicio'];$fechaT=$datos['_37_fechaTerminacion'];$noCedula=$datos['_38_noCedula'];
      $componentes['Antecedente'] = $this->antecedente_Attr($inst,$idTipoE,$tipoE,$idEntFed,$entFed,$fechaI,$fechaT,$noCedula);

      return $componentes;
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
   public function firmaResp_Attr($nombre,$apellidoPat,$apeMat,$curp,$idCarg,$cargo,$titulo,$sello,$certR,$noCertR)
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
     $data['primerApelldo'] = $apePat;
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
            $errores = 'sin errores';
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
         if (isset($datos[1])==null) {
            $errores = 'sin errores';
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
}
