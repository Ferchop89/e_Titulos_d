<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Spatie\ArrayToXml\ArrayToXml;
use \FluidXml\FluidXml;
use App\Models\Estudio;
use App\Models\Entidad;
use App\Models\Modo;
use Carbon\Carbon;

class EtitulosController extends Controller
{
   public function searchAlum()
   {
        return view('/menus/search_eTitulosXml');
   }

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

      // Integramos errores y Datos
      $resultado[0] = $items;
      $resultado[1] = $errores;

      return $resultado;
   }
   public function carrerasProcedencia($cuenta,$digito,$carrera)
   {

      $query1  = 'SELECT ';
      $query1 .= "ori_cve_profesiones AS _09_cveCarrera, ";
      $query1 .= "carrera AS _10_nombreCarrera ";
      $query1 .= 'from Datos ';
      $query1 .= 'join Orientaciones on dat_car_actual = ori_plancarr and dat_orientacion = ori_orienta ';
      $query1 .= 'join Carreras_Profesiones on convert(int,ori_cve_profesiones) = clave_carrera ';
      $query1 .= "where dat_ncta = '".$cuenta."' and dat_dig_ver = '".$digito."' and dat_car_actual = '".$carrera."' ";

      $query2  = 'SELECT ';
      $query2 .= 'ori_plancarr AS carrera_cveCarrera, ori_orienta_nom  AS carrera_nombreCarrera ';
      $query2 .= 'from Orientaciones ';
      $query2 .= "where ori_plancarr = '".$carrera."'";

      $info = (array)DB::connection('sybase')
                     ->select($query1);

      $errores = $datos =  array(); // errores y faltantes
      if ($info==[]) {
         // No existe la clave SEP para la clave de carrera locale
         $errores[0] = 'Sin clave SEP';
         if ($info==[]) {
            $info = (array)DB::connection('sybase')
                           ->select($query2);
            if ($info==[]) {
               // No existe el nombre para esta carrera_Attr
               $errores[1] = "Sin nombre de carrera";
            } else {
               $datos = $info[0];
            }
         }
      } else {
         // pasamos a un arreglo los objetos
         $datos = $info[0];
      }

      // Verificamos que cualquiera de los 2 querys arroja resultados.
      if ($datos!=[]) {
         $resultado = (array)$datos;
      } else {
         // No existe nombre de carrera ni clave
         $resultado['carrera_cveCarrera'] = '----';
         $resultado['carrera_nombreCarrera'] = '----';
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
      $query = 'SELECT ';
      $query .= "escpro_fec_exp AS carrera_fechas, ";
      $query .= "escpro_nivel_pro AS atrib_nivelProf ";
      $query .= "FROM Titulos ";
      $query .= "JOIN Escprocedencia ON escpro_ncta = tit_ncta AND escpro_plancarr_act = tit_plancarr ";
      $query .= "where escpro_ncta = '".$cuenta."' and tit_plancarr='".$carrera."'";

      $info = (array)DB::connection('sybase')
                     ->select($query);

      $datos = $errores = array();
      if ($info==[]) {
            $datos['carrera_fechas'] = '----';
            $datos['atrib_nivelProf'] =  'ND';
            $errores[0] = 'carrera sin periodo';
            $errores[1] = 'sin nivel de carrera';
      } else {
         $datos = (array)$info[0];
         // verificamos los periodos
         if(strlen($datos['carrera_fechas'])!=8)
         {
            $errores[0] = ['periodo de estudios irregular'];
         } else {
            $fecha1 = substr($datos['carrera_fechas'],0,4).'/01/01';
            $fecha2 = substr($datos['carrera_fechas'],4,4).'/01/01';
            // dd("fechas",$fecha1,$fecha2);
            unset($datos['carrera_fechas']); // retiramos el campo para sustituirlo por fechas
            if (!strtotime($fecha1)) {
               $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'inicio de estudios inválido';
            }
            if (!strtotime($fecha2)) {
               $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'térmico de estudios inválido';
            } else {
               $datos['_11_fechaInicio'] = $fecha1;
            }
            if ($fecha1>=$fecha2) {
               $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'Periodo de estudios inválido';
            } else {
               $datos['_12_fechaTerminacion'] = $fecha2;
            }
         }
      }

      if ($errores!=[]) {
         $datos['errores'] = $errores;
      }

      // Retiramos el campo de antec_fechas
      return $datos;
   }
   public function titulosExamenes($cuenta, $carrera)
   {
      $query = 'SELECT  ';
      $query .= "tit_fec_emision_tit AS _21_fechaExpedicion, ";
      $query .= "exa_tipo_examen AS _22_idModalidadTitulacion, ";
      $query .= "exa_fecha_examen_prof AS _24_fechaExamenProfesional, ";
      $query .= "exa_fecha_examen_prof AS _25_fechaExencionExamenProf, ";
      $query .= "exa_ini_ssoc AS exp_InicioSs, ";
      $query .= "exa_fin_ssoc AS exp_FinSs ";
      $query .= "FROM Titulos ";
      $query .= "JOIN Examenes ON exa_ncta = tit_ncta and exa_plancarr = tit_plancarr ";
      $query .= "where tit_ncta = '".$cuenta."' and tit_plancarr='".$carrera."'";

      $info = (array)DB::connection('sybase')
                     ->select($query);

      $datos = $errores = array();
      if ($info==[]) {
         $datos['_21_fechaExpedicion'] = '----';
         $datos['_22_idModalidadTitulacion'] = '--';
         $datos['_24_fechaExamenProfesional'] = '----';
         $datos['_25_fechaExencionExamenProfesional'] = '----';
         $errores[0] = 'falta fecha de expedición de examen profesional';
         $errores[1] = 'falta modalidad de titulación';
         $errores[2] = 'falta de examen profesional';
         $errores[3] = 'falta fecha de inicio de Servicio Social';
         $errores[4] = 'falta fecha de terminación de Servicio Social';
      } else {
         // Validamos la existencia y el tipo y valor de todos los campos
         // pasamos los objetos a un arreglo.
         $datos = (array)$info[0];
         // validamos la fecha de expedición del Título.
         if (Carbon::createFromFormat('Y-m-d', substr($datos['_21_fechaExpedicion'],0,10))==false) {
            $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'fecha de expedición de título inválida';
         } else {
            // dd(substr($datos['_21_fechaExpedicion'],0,10));
            $datos['_21_fechaExpedicion'] = substr($datos['_21_fechaExpedicion'],0,10);
         }
         // preguntamos si existe la modalida de titulación.
         $modo = Modo::where('cat_subcve',$datos['_22_idModalidadTitulacion'])->first();
         if ($modo==[]) {
            $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'no existe la modalidad de titulación';
         } else {
            $datos['_23_modalidadTitulacion'] = $modo->MODALIDAD_TITULACION;
         }
         // validamos la fecha de examen profesional.
         // $fecha = strtotime($datos['_24_fechaExamenProfesional']);
         $fecha = Carbon::parse($datos['_24_fechaExamenProfesional'])->format('Y-m-d');
         if (!$fecha) {
            $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'fecha de examen profesional inválida';
            $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'fecha de exensión de examen profesional inválida';
         } else {
            $datos['_24_fechaExamenProfesional'] = $fecha;
            $datos['_25_fechaExencionExamenProfesional'] = $fecha;
            unset($datos['_25_fechaExencionExamenProf']); // Se sustituye porque el nombre no es el adecuado (termina en "prof")
         }
         // validamos fechas de servicio social.
         $fecha1 = Carbon::parse($datos['exp_InicioSs'])->format('Y/m/d');
         $fecha2 = Carbon::parse($datos['exp_FinSs'])->format('Y/m/d');
         // Retiramos del arreglo las fechas de Servicios social que ya no se van a ocupar.
         unset($datos['exp_InicioSs']); unset($datos['exp_FinSs']);
         // Se trata de fechas y además la final no es menor que la inicial
         // Evaluamos las fechas para inconsistencias o bien validar el Servicio solcial.
         if (!$fecha1 || !$fecha2 || !$fecha2>$fecha1) {
            $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'periodo irregular de servicio social';
            $datos['_26_cumplioServicioSocial'] = '---';
         } else {
            $datos['_26_cumplioServicioSocial'] = '1';
         }
      }

      if ($errores!=[]) {
         $datos['errores'] = $errores;
      }

      return $datos;
   }
   public function titulosDatos($cuenta,$digito,$carrera)
   {
      // El nombre viene conbinado, se consulta, se divide en nombre y apellidos; y se omite del arreglo
      $query = 'SELECT ';
      $query .= "dat_curp AS _16_curp, ";
      $query .= "dat_nombre AS _17_nombre ";
      $query .= "FROM Titulos ";
      $query .= "JOIN Datos ON dat_ncta = tit_ncta  AND dat_car_actual = tit_plancarr AND dat_nivel = tit_nivel ";
      $query .= "where dat_ncta = '".$cuenta."' and dat_dig_ver = '".$digito."' and tit_plancarr='".$carrera."'";

      $info = (array)DB::connection('sybase')
                     ->select($query);
      $datos = $errores = array();
      if ($info==[]) {
         $datos['_16_curp'] = '----';
         $datos['_17_nombre'] = '----';
         $datos['_18_primerApellido'] = '----';
         $datos['_19_segundoApellido'] = '----';
         $errores[0] = 'falta curp';
         $errores[1] = 'falta nombre';
      } else
      {
         $datos = (array)$info[0];
         // dd($datos);
         if ($datos['_16_curp']==null) {
            $errores[0] = 'falta Curp';
            $datos['_16_curp'] = '----';
         }
         if ($datos['_17_nombre']=='') {
            $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'falta nombre';
         } else {
            // Expandimos el Nombre
            $nombre = explode('*',$datos['_17_nombre']);
            // Retiramos en nombre completo y los sustituimos por sus partes.
            unset($datos['_17_nombre']);
            // Agregamos sus partes.
            if (isset($nombre[2])!=null) {
               // Agregamos el nombre
               $datos['_17_nombre'] = $nombre[2];
            }
            if (isset($nombre[0])!=null) {
               // Agregamos el nombre
               $datos['_18_primerApellido'] = $nombre[0];
            }
            if (isset($nombre[1])!=null) {
               // Agregamos el nombre
               $datos['_19_segundoApellido'] = $nombre[1];
            }

         }

      }

      // Esta consulta no se trae el correo electrónico.
      $datos['_20_correoElectronico'] = '----';
      $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'falta correo electrónico';

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
            $errores[0] = 'No se cuenta con escuela de procedencia';
         }
         // Mapeo del idTipoEstudioAntecedente
         $tipoEstudio = Estudio::where('cat_subcve',$datos['ante_nivel'])->first();
         if (!$tipoEstudio==[]) {
            $resultado['_32_idTipoEstudioAntecedente'] = $tipoEstudio->ID_TIPO_ESTUDIO_ANTECEDENTE;
            $resultado['_33_tipoEstudioAntecedente'] = $tipoEstudio->TIPO_ESTUDIO_ANTECEDENTE;
         } else {
            $resultado['_32_idTipoEstudioAntecedente'] = '----';
            $resultado['_33_tipoEstudioAntecedente'] = '----';
            $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'sin clave de estudio antecedente';
         }
         // Mapeo de la entidad (Unam y Sep manejan diferentes claves)
         if ($datos['_34_idEntidadFederativa']<'00033') {
               $entidad = Entidad::where('pais_cve',$datos['_34_idEntidadFederativa'])->first();
         } else {
               // Todos las entidades se dejan como "EXTRANJERO"
               $entidad = Entidad::where('pais_cve','00033')->first();
         }

         // dd($entidad,$datos['_34_idEntidadFederativa']);
         if ($entidad) {
            $resultado['_34_idEntidadFederativa'] = $entidad->ID_ENTIDAD_FEDERATIVA;
            $resultado['_35_entidadFederativa'] = $entidad->C_NOM_ENT;
         } else {
            $resultado['_34_idEntidadFederativa'] = '----';
            $resultado['_35_entidadFederativa'] = '----';
            $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'sin clave de entidad para estudio antecedente';
         }

         $resultado['antec_fechas'] = $datos['ante_periodo'];
         // Probamos el periodo

         // verificamos los periodos
         if(!strlen($datos['ante_periodo'])==8)
         {
            $errores[0] = ['periodo de estudios irregular'];
         } else {
            $fecha1 = substr($datos['ante_periodo'],0,4).'/01/01';
            $fecha2 = substr($datos['ante_periodo'],4,4).'/01/01';
            unset($resultado['antec_fechas']);
            if (!strtotime($fecha1)) {
               $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'inicio de estudios antecedente inválido';
            } else {
               $resultado['_36_fechaInicio'] = $fecha1;
            }
            if (!strtotime($fecha2)) {
               $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'término de estudios antecedente inválido';
            } else {
               $resultado['_37_fechaTerminacion'] = $fecha2;
            }
            if (strtotime($fecha2)<strtotime($fecha1)) {
               $cuenta = ($errores==[])? 0 : count($errores); $errores[$cuenta] = 'Periodo de estudios antecedente inválido';
            }
         }
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

   public function consultaDatos($cuenta, $verif, $carrera)
   {
      $info = DB::connection('sybase')->table('Datos')->where('dat_ncta', $cuenta)->where('dat_dig_ver', $verif)->get();

      return $info;
   }
   public function postSearchAlum(Request $request)
   {
      // dd("algo");
        $request->validate([
            'num_cta' => 'required|numeric|digits:9'
        ],[
            'num_cta.required' => 'El campo es obligatorio',
            'num_cta.numeric' => 'El campo debe contener solo números',
            'num_cta.digits'  => 'El campo debe ser de 9 dígitos',
        ]);
        return redirect()->route('eSearchInfo', ['num_cta'=>$request->num_cta]);
    }
   public function showInfo($num_cta)
   {
      // Presentacion de Datos
      $cuenta = substr($num_cta, 0, 8);
      $verif = substr($num_cta, 8, 1);
      // $cuenta = '06401471'; $digito='3'; $carrera='0030581';
      // $cuenta = '50845104'; $digito='7'; $carrera='0965010';
      $cuenta = '98801868'; $digito='0'; $carrera='01206'; // 0123356
      $datos = $this->integraConsulta($cuenta,$digito,$carrera);
      // En esta seccion se consultan los sellos del registro de usuario.
      $sello1 = 'Sello 1'; $sello2 = 'Sello2'; $sello3 = 'Sello3';
      $nodos = $this->IntegraNodos($datos[0],$sello1,$sello2,$sello3);
      // Obtención de XML
      $toXml = $this->tituloXml($nodos);
      // Obtención de la cadena origianl
      $cadenaOriginal = $this->cadenaOriginal($nodos);
      // Obención de los Errores.
      $errores = (isset($datos[1])==null)? 'Sin errores': $datos[1] ;
      // verificación de invasion de fechas.
      // visualizacon de la información.
      // dd($datos);
      dd($cadenaOriginal,$toXml->xml(),$errores);
    }
    // Generacion de la Cadena Original
   public function cadenaOriginal($nodos)
   {
      $cadenaOriginal = '||';
      $cadenaOriginal.= $nodos['TituloElectronico']['version'].'|';
      $cadenaOriginal.= $nodos['TituloElectronico']['folioControl'].'|';

      $cadenaOriginal.= $nodos['FirmaResponsable1']['curp'].'|';
      $cadenaOriginal.= $nodos['FirmaResponsable1']['idCargo'].'|';
      $cadenaOriginal.= $nodos['FirmaResponsable1']['cargo'].'|';
      $cadenaOriginal.= $nodos['FirmaResponsable1']['abrTitulo'].'|';

      $cadenaOriginal.= $nodos['FirmaResponsable2']['curp'].'|';
      $cadenaOriginal.= $nodos['FirmaResponsable2']['idCargo'].'|';
      $cadenaOriginal.= $nodos['FirmaResponsable2']['cargo'].'|';
      $cadenaOriginal.= $nodos['FirmaResponsable2']['abrTitulo'].'|';

      $cadenaOriginal.= $nodos['FirmaResponsable3']['curp'].'|';
      $cadenaOriginal.= $nodos['FirmaResponsable3']['idCargo'].'|';
      $cadenaOriginal.= $nodos['FirmaResponsable3']['cargo'].'|';
      $cadenaOriginal.= $nodos['FirmaResponsable3']['abrTitulo'].'|';

      $cadenaOriginal.= $nodos['Institucion']['cveInstitucion'].'|';
      $cadenaOriginal.= $nodos['Institucion']['nombreInstitucion'].'|';

      $cadenaOriginal.= $nodos['Carrera']['cveCarrera'].'|';
      $cadenaOriginal.= $nodos['Carrera']['nombreCarrera'].'|';
      $cadenaOriginal.= $nodos['Carrera']['fechaInicio'].'|';
      $cadenaOriginal.= $nodos['Carrera']['fechaTeminacion'].'|';
      $cadenaOriginal.= $nodos['Carrera']['idAutorizacionReconocimiento'].'|';
      $cadenaOriginal.= $nodos['Carrera']['autorizacionReconocimiento'].'|';
      $cadenaOriginal.= ($nodos['Carrera']['numeroRvoe']=='----')? '|' : $nodos['Carrera']['numeroRvoe'].'|';

      $cadenaOriginal.= $nodos['Profesionista']['curp'].'|';
      $cadenaOriginal.= $nodos['Profesionista']['nombre'].'|';
      $cadenaOriginal.= $nodos['Profesionista']['primerApelldo'].'|';
      $cadenaOriginal.= $nodos['Profesionista']['segundoApellido'].'|';
      $cadenaOriginal.= $nodos['Profesionista']['correoElectronico'].'|';

      $cadenaOriginal.= $nodos['Expedicion']['fechaExpedicion'].'|';
      $cadenaOriginal.= $nodos['Expedicion']['idModalidadTitulacion'].'|';
      $cadenaOriginal.= $nodos['Expedicion']['modalidadTitulacion'].'|';
      $cadenaOriginal.= $nodos['Expedicion']['fechaExamenProfesional'].'|';
      $cadenaOriginal.= $nodos['Expedicion']['fechaExencionExamenProfesional'].'|';
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
      $cadenaOriginal.= $nodos['Antecedente']['fechaInicio'].'|';
      $cadenaOriginal.= $nodos['Antecedente']['fechaTerminacion'].'|';
      $cadenaOriginal.= ($nodos['Antecedente']['noCedula']=='----')? '|' : $nodos['Antecedente']['noCedula'].'||';

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
    // Arreglos de información de los atributos para los nodos del Titulo Electrónico

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
}
