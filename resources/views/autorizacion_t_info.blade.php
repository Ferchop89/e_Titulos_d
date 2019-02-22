@extends('layouts.app')
@section('title', 'CONDOC | Autorización de Transferencia de Información y actualización de datos personales')

@section('estilos')
	<link href="{{ asset('css/alumnos.css') }}" rel="stylesheet">
@endsection

{{-- @section('location')
<div>
	<p id="navegacion">
		<a href="{{ route('home') }}"><i class="fa fa-home" style="font-size:28px"></i></a> </p>
</div>
@endsection --}}

@section('content')
	<div class="loader"></div>
<div class="container cont_aut">
	<div  class="ati_vista">
		<h2 align="center">AUTORIZACIÓN DE TRANSFERENCIA DE INFORMACIÓN</h2>
		<h2 align="center">A LA DIRECCIÓN GENERAL DE PROFESIONES DE LA SECRETARÍA DE EDUCACIÓN PÚBLICA</h2>
		<h2 align="center">Y ACTUALIZACIÓN DE DATOS PERSONALES</h2>
		{{-- <div class="d_m">
			<p><i>Por favor, completa los campos con la información correcta y posteriormente acepta lo establecido en este apartado.</i></p>
		</div> --}}
		<form method="POST" action="{{ url('alumnos/ati') }}">
			{!! csrf_field() !!}
	  <div class="div_cont">
			<p class="date"> Ciudad Universitaria, Cd. Mx., a {{$fecha->day}} de {{$fecha->month}} de {{$fecha->year}}.</p>
			<input name="fecha_completa" type="hidden" value="{{$fecha->day}}.{{$fecha->month}}.{{$fecha->year}}.">
			<input name="num_cta" type="hidden" value="{{$alumno->num_cta}}">
			<br>
			<p align="justify">
				<strong>
					<div>Dirección General de Administración Escolar</div>
					<div>Universidad Nacional Autónoma de México</div>
					<div>Presente.</div>
				</strong>
			</p>
			<br>
			<p align="justify">
				Por medio de la presente manifiesto que el área de Servicios Escolares de la (del) Facultad, Escuela, Centro, Instituto o
				Programa de Posgrado
				{{-- <select name="plantel" id="plantel" class="form-control" autocomplete="off" @if ($errors->has('plantel')) autofocus @endif>
					<option value="" selected>Selecciona una opción</option>}
						@foreach ($planteles as $key => $value)
                  	<option value="{!!$value!!}">{!! $value !!}</option>
						@endforeach
				</select> --}}
				<input type="text" id="plantel" name="plantel" class="width_n mayus form-control" placeholder="Escribe el plantel" value="{{ old('plantel') }}" @if ($errors->has('plantel')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()">
					@if ($errors->has('plantel'))
						 <span class="help-block">
								 <strong>{{ $errors->first('plantel') }}</strong>
						 </span>
				 @endif
				me informó que la Dirección General de Administración Escolar de la UNAM (DGAE-UNAM) debe transferir la información
				de mis datos académicos y personales como egresado(a) de esta Universidad Nacional, a la Dirección General de
				Profesiones de la Secretaría de Educación Pública (DGP-SEP), para que yo pueda realizar el trámite de registro de
				título o grado para la obtención de cédula profesional ante la citada dependencia gubernamental.
			</p>
			<p align="justify">
				Para ello, se me solicita actualizar los siguientes datos personales y manifiesto, <strong>bajo protesta de decir verdad</strong>, que
				son verídicos y fehacientes:
			</p>

			<br>
				<div class="form">
					<div class="form-group row">
						<h4>Información personal.</h4><br>
				    	<label for="nombres" class="col-sm-2 col-form-label">Nombre(s):</label>
				    	<div class="col-sm-10">
				      	<input type="text" id="nombres" name="nombres" class="width_n mayus form-control" placeholder="Nombre(s)" value="{{old('nombres', $alumno->nombres)}}" @if ($errors->has('nombres')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()">
								@if ($errors->has('nombres'))
	 								 <span class="help-block">
	 										 <strong>{{ $errors->first('nombres') }}</strong>
	 								 </span>
	 						 @endif
							</div>
				  	</div>
					<div class="form-group row">
				    	<label for="apellido1" class="col-sm-2 col-form-label">Primer apellido: </label>
				    	<div class="col-sm-10">
				      	<input type="text" id="apellido1" name="apellido1" class="width_n mayus form-control" placeholder="Primer apellido" value="{{old('apellido1', $alumno->apellido1)}}" @if ($errors->has('apellido1')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()">
								@if ($errors->has('apellido1'))
	 								 <span class="help-block">
	 										 <strong>{{ $errors->first('apellido1') }}</strong>
	 								 </span>
	 						 @endif
							</div>
				  	</div>
					<div class="form-group row">
				    	<label for="apellido2" class="col-sm-2 col-form-label">Segundo apellido: </label>
				    	<div class="col-sm-10">
				      	<input type="text" id="apellido2" name="apellido2" class="width_n mayus form-control" placeholder="Segundo apellido" value="{{old('apellido2', $alumno->apellido2)}}" @if ($errors->has('apellido2')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()">
								@if ($errors->has('apellido2'))
	 								 <span class="help-block">
	 										 <strong>{{ $errors->first('apellido2') }}</strong>
	 								 </span>
	 						 @endif
							</div>
						</div>
					<div class="form-group row">
				    	<label for="curp" class="col-sm-2 col-form-label">CURP (18 caracteres):</label>
				    	<div class="col-sm-10">
				      	<input type="text" id="curp" name="curp" class="width_n mayus form-control" maxlength="18" placeholder="CURP (18 caracteres):" value="{{old('curp', $alumno->curp)}}" @if ($errors->has('curp')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()">
								@if ($errors->has('curp'))
										<span class="help-block">
												<strong>{{ $errors->first('curp') }}</strong>
										</span>
								@endif
							</div>
						</div>
					<div class="form-group row">
				    	<label for="num_tel" class="col-sm-2 col-form-label">Núm. telefónico fijo:</label>
				    	<div class="col-sm-10">
				      	<input type="text" id="num_fijo" name="num_tel" class="width_n form-control" placeholder="10 dígitos (lada incluida):" maxlength="10" value="{{old('num_tel', $alumno->tel_fijo)}}" @if ($errors->has('num_tel')) autofocus @endif>
								@if ($errors->has('num_tel'))
	 								 <span class="help-block">
	 										 <strong>{{ $errors->first('num_tel') }}</strong>
	 								 </span>
	 						 @endif
							</div>
				  	</div>
					<div class="form-group row">
				    	<label for="num_cel" class="col-sm-2 col-form-label">Núm. telefónico celular:</label>
				    	<div class="col-sm-10">
				      	<input type="text" id="num_cel" name="num_cel" class="width_n form-control" placeholder="10 dígitos (lada incluida):" maxlength="10" value="{{old('num_cel', $alumno->tel_celular)}}" @if ($errors->has('num_cel')) autofocus @endif>
								@if ($errors->has('num_cel'))
	 								 <span class="help-block">
	 										 <strong>{{ $errors->first('num_cel') }}</strong>
	 								 </span>
	 						 @endif
							</div>
						</div>
					<div class="form-group row">
				    	<label for="correo" class="col-sm-2 col-form-label">Correo electrónico:</label>
				    	<div class="col-sm-10">
				      	<input type="email" id="correo" name="correo" class="width_n form-control" placeholder="email@correo.com" value="{{old('correo', $alumno->correo)}}" @if ($errors->has('correo')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toLowerCase()">
								<label for="correo" class="correo">Donde recibirá información del trámite, incluido el número de cédula profesional.</label>
								@if ($errors->has('correo'))
									<span class="help-block">
											<strong>{{ $errors->first('correo') }}</strong>
									</span>
								@endif
							</div>
						</div>
					<div class="form-group row">

						<h4>Domicilio.</h4><br>
					   	<label for="codigo_postal" class="col-sm-2 col-form-label">C.P.:</label>
					   	<div class="col-sm-10">
								@if(isset($info_dl))
						     	<input class="col-sm-8" type="text" id="codigo_postal" name="codigo_postal" class="width_n form-control" placeholder="Código postal" maxlength="5" value="{{old('codigo_postal', $info_dl->codigo_postal)}}" @if ($errors->has('codigo_postal')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()">
								@else
									<input class="col-sm-8" type="text" id="codigo_postal" name="codigo_postal" class="width_n form-control" placeholder="Código postal" maxlength="5" onfocusout="filtraCP()">
								@endif
								<input class="col-sm-2" type="submit" name="filtraXCodigo" id="filtraXCodigo" value="Buscar" onclick="visualiza();" />
									@if ($errors->has('codigo_postal'))
			 							<span class="help-block">
			 								<strong>{{ $errors->first('codigo_postal') }}</strong>
			 							</span>
			 					 	@endif
							</div>
					 	</div>
					<div class="form-group row">
					   	<label for="estado" class="col-sm-2 col-form-label">Estado:</label>
					   	<div class="col-sm-10">
								@if(isset($info_dl->estado))
								<select name="estado" class="width_n form-control">
									<option value="{{$info_dl->estado}}">{{$info_dl->estado}}</option>
								</select>
								@else
									@if(isset($estados))
									<select name="estado" class="width_n form-control">
										@foreach($estados as $edo)
												<option value="{{ mb_strtoupper($edo->d_estado,'utf-8') }}" {{ (Request::old("estado") == $edo->d_estado ? "selected":"") }}>{{mb_strtoupper($edo->d_estado,'utf-8')}}</option>
												<!-- <option value="{{$edo->d_estado}}">{{$edo->d_estado}}</option> -->
										@endforeach
									</select>
									@else
						     		<select name="estado" class="width_n form-control" disabled>
											<!-- <option value="volvo">MÉXICO</option>
									  	<option value="saab">Sinaloa</option> -->
										</select>
									@endif
								@endif

								@if ($errors->has('estado'))
			 						 <span class="help-block">
			 								 <strong>{{ $errors->first('estado') }}</strong>
			 						 </span>
			 				 @endif
							</div>
					 	</div>
					<div class="form-group row">
					   	<label for="municipio" class="col-sm-2 col-form-label">Municipio/Alcaldía:</label>
					   	<div class="col-sm-10">
								@if(isset($info_dl->municipio))
								<select name="municipio" class="width_n form-control">
									<option value="{{$info_dl->municipio}}">{{$info_dl->municipio}}</option>
								</select>
								@else
									@if(isset($municipios))
									<select name="municipio" class="width_n form-control">
										@foreach($municipios as $mnp)
											<option value="{{mb_strtoupper($mnp->d_mnpio,'utf-8')}}">{{mb_strtoupper($mnp->d_mnpio,'utf-8')}}</option>
										@endforeach
									</select>
									@else
						     		<select name="municipio" class="width_n form-control" disabled>
											<!-- <option value="volvo">MÉXICO</option>
									  	<option value="saab">Sinaloa</option> -->
										</select>
									@endif
								@endif

								@if ($errors->has('municipio'))
				 					 <span class="help-block">
				 							 <strong>{{ $errors->first('municipio') }}</strong>
				 					 </span>
				 			 @endif
							</div>
					 	</div>
					<div class="form-group row">
					   	<label for="colonia" class="col-sm-2 col-form-label">Colonia:</label>
					   	<div class="col-sm-10">
								@if(isset($info_dl->colonia))
								<select name="colonia" class="width_n form-control">
									<option value="{{$info_dl->colonia}}">{{$info_dl->colonia}}</option>
								</select>
								@else
									@if(isset($colonias))
									<select name="colonia" class="width_n form-control">
										@foreach($colonias as $col)
											<option value="{{mb_strtoupper($col->d_asenta,'utf-8')}}">{{mb_strtoupper($col->d_asenta,'utf-8')}}</option>
										@endforeach
									</select>
									@else
						     		<select name="colonia" class="width_n form-control" disabled>
											<!-- <option value="volvo">MÉXICO</option>
									  	<option value="saab">Sinaloa</option> -->
										</select>
									@endif
								@endif

								@if ($errors->has('colonia'))
										 <span class="help-block">
												 <strong>{{ $errors->first('colonia') }}</strong>
										 </span>
								 @endif
							</div>
					 	</div>
					<div class="form-group row">
					   	<label for="calle_numero" class="col-sm-2 col-form-label">Calle y número:</label>
					   	<div class="col-sm-10">
								@if(isset($info_dl))
					     		<input type="text" id="calle_numero" name="calle_numero" class="width_n form-control" placeholder="Calle y número" value="{{old('calle_numero', $info_dl->calle_numero)}}" @if ($errors->has('calle_numero')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()">
								@else
									<input type="text" id="calle_numero" name="calle_numero" class="width_n form-control" placeholder="Calle y número">
								@endif
								@if ($errors->has('calle_numero'))
				 					 <span class="help-block">
				 							 <strong>{{ $errors->first('calle_numero') }}</strong>
				 					 </span>
				 			 @endif
							</div>
					 	</div><br>
					<div class="form-group row">
						<h4>Información laboral.</h4><br>
					   	<label for="empleo" class="col-sm-2 col-form-label">¿Trabajas?:</label>
					   	<div class="col-sm-10">
								<table style="width:20%;"><tr>
									@if(isset($info_dl->labora))
										<td><p><input id="empleo_si" name="empleo" type="radio" value="{{old('empleo')}}" @if(old('empleo') == 1) checked @endif/> Si</p></td>
										<td><p><input id="empleo_no" name="empleo" type="radio" value="{{old('empleo')}}" @if(old('empleo') == 0) checked @endif/> No</p></td>
									@else
										<td><p><input checked id="empleo_si" name="empleo" type="radio" value=1/> Si</p></td>
										<td><p><input id="empleo_no" name="empleo" type="radio" value=0/> No</p></td>
									@endif
								</tr></table>
								<!-- <label for="empleo" class="correo">Selecciona "Si" sólo en caso de ejercer en relación con la carrera que estudiaste.</label> -->
								@if ($errors->has('empleo'))
									 <span class="help-block">
											 <strong>{{ $errors->first('empleo') }}</strong>
									 </span>
							 @endif
							</div>
					 	</div>
					<div id="info_laboral_nombre" class="form-group row">
					   	<label for="nombre_laboral" class="col-sm-2 col-form-label">Empresa/Institución:</label>
					   	<div class="col-sm-10">
								@if(isset($info_dl))
					     		<input type="text" id="nombre_laboral" name="nombre_laboral" class="width_n form-control" placeholder="Nombre de la empresa/institución donde laboras" value="{{old('lugar_laboral', $info_dl->lugar_laboral)}}" @if ($errors->has('nombre_laboral')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()">
								@else
									<input type="text" id="nombre_laboral" name="nombre_laboral" class="width_n form-control" placeholder="Nombre de la empresa/institución donde laboras">
								@endif
								@if ($errors->has('nombre_laboral'))
									 <span class="help-block">
											 <strong>{{ $errors->first('nombre_laboral') }}</strong>
									 </span>
							 @endif
							</div>
					 	</div>
					<div id="info_laboral_cargo" class="form-group row">
					   	<label for="cargo" class="col-sm-2 col-form-label">Cargo:</label>
					   	<div class="col-sm-10">
								@if(isset($info_dl))
					     		<input type="text" id="cargo" name="cargo" class="width_n form-control" placeholder="Cargo" value="{{old('cargo_laboral', $info_dl->cargo_laboral)}}" @if ($errors->has('cargo')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()">
								@else
									<input type="text" id="cargo" name="cargo" class="width_n form-control" placeholder="Cargo">
								@endif
								@if ($errors->has('cargo'))
			 						 <span class="help-block">
			 								 <strong>{{ $errors->first('cargo') }}</strong>
			 						 </span>
			 				 @endif
							</div>
					</div>
					<div id="info_laboral_ingreso" class="form-group row">
					   	<label for="ingreso" class="col-sm-2 col-form-label">Fecha de ingreso:</label>
					   	<div class="col-sm-10">
								@if(isset($info_dl))
									@if(isset($info_dl->ingreso))
										<input class="width_n form-control fecha datepicker_esp" placeholder="dd/mm/aaaa" type="text" name="ingreso" maxlength="10" value="{{old('ingreso', date('d/m/Y', strtotime($info_dl->ingreso_laboral)))}}" @if ($errors->has('ingreso')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()">
									@else
										<input class="width_n form-control fecha datepicker_esp" placeholder="dd/mm/aaaa" type="text" name="ingreso" maxlength="10">
									@endif
								@else
									<input class="width_n form-control fecha datepicker_esp" placeholder="dd/mm/aaaa" type="text" name="ingreso" maxlength="10">
								@endif
								<!-- <input type="text" id="ingreso" name="ingreso" class="width_n form-control" placeholder="Fecha de ingreso (dd/mm/aaaa)" maxlength="10" value="{{old('cargo', $alumno->ingreso)}}" @if ($errors->has('ingreso')) autofocus @endif onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()"> -->
								@if ($errors->has('ingreso'))
			 						 <span class="help-block">
			 								 <strong>{{ $errors->first('ingreso') }}</strong>
			 						 </span>
			 				 @endif
							</div>
					</div>

				</div>
			<br>

			<p align="justify">
				Por lo antes descrito, acepto y autorizo <strong><sup>1</sup></strong> que la DGAE-UNAM, en cumplimiento a lo establecido en el Decreto por el que
				se reforman y derogan diversas disposiciones del Reglamento de la Ley Reglamentaria del Artículo 5° Constitucional,
				relativo al ejercicio de las profesiones en el Distrito Federal, publicado en el Diario Oficial de la Federación el 5 de abril
				de 2018 <strong><sup>2</sup></strong>, realice la transferencia electrónica de mis datos personales y académicos (que hasta hoy se mantienen en
				custodia de la DGAE-UNAM) a la DGP-SEP y que, una vez actualizados, formarán parte de la base de datos de dicha
				dependencia gubernamental. Lo anterior, primero para que cuando la DGAE-UNAM lo requiera, me identifique, ubique,
				comunique, contacte y envíe información por cualquier medio posible, y en segundo término para que la DGP-SEP
				en el momento que yo lo requiera o decida, acepte mi solicitud del trámite de registro de Título o Grado y me emita la
				cédula profesional correspondiente.
			</p>
			<p class="nota">
				<strong>Nota: </strong>A continuación, se te mostrará este mismo documento en formato PDF que deberás imprimir.
			</p>
			<div align="center" class="cont_aut"><p>
			 		<input type="checkbox" name="acepto" @if ($errors->has('acepto')) autofocus="true"@endif> Sí, acepto
					<br>
	 			 @if ($errors->has('acepto'))
	 					 <span class="help-block">
	 							 <strong>{{ $errors->first('acepto') }}</strong>
	 					 </span>
	 			 @endif
				 <br>
			 		<input type="submit" name='btnEnviar' id='btnEnviar' class="btn btn-primary" value="Enviar">
			</p></div>
			</div>
		</form>
			<div class="pie">
				<p><strong>1 </strong>
					Artículo 8° del Reglamento de Transparencia, Acceso a la Información Pública y Protección de Datos Personales para la
					Universidad Nacional Autónoma de México (Consulte:
					<a href="http://www.abogadogeneral.unam.mx/legislacion/abogen/documento.html?doc_id=66" target="_blank">
						http://www.abogadogeneral.unam.mx/legislacion/abogen/documento.html?doc_id=66
					</a>).
				</p>
				<p><strong>2 </strong>
					Consulte: <a href="http://www.dof.gob.mx/nota_detalle.php?codigo=5518146&fecha=05/04/2018" target="_blank">
						http://www.dof.gob.mx/nota_detalle.php?codigo=5518146&fecha=05/04/2018>
					</a>
				</p>
			</div>
	</div>
</div>
@endsection
@section('animaciones')
	<script src="{{asset('js/loadingDownload.js')}}"></script>

	{{-- Para elegir fecha en español --}}
  <script src="{{asset('js/datepicker_esp.js')}}"></script>

	{{-- Para visualizar la zona en que está el código postal una vez hecha la búsqueda --}}
  <script>
		function getOffset( el ) {
		 var _x = 0;
		 var _y = 0;
		 while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
				 _x += el.offsetLeft - el.scrollLeft;
				 _y += el.offsetTop - el.scrollTop;
				 el = el.offsetParent;
		 }
		 return { top: _y, left: _x };
		}

		function visualiza(){}
			var elmnt = window.innerHeight;
			var y = getOffset( document.getElementById('codigo_postal') ).top;
			window.scroll(0, y-(elmnt/2));
		}
	</script>

	{{-- Para evitar mostrar los campos de información laboral en caso de no trabajar --}}
	<script>
		if($('#empleo_si').is(':checked')) {
			$("#info_laboral_nombre").show();
			$("#info_laboral_cargo").show();
			$("#info_laboral_ingreso").show();
		}else{
			$("#info_laboral_nombre").hide();
			$("#info_laboral_cargo").hide();
			$("#info_laboral_ingreso").hide();
		}

		$(function() {
			$('input[type=radio][name=empleo]').change(function() {
				if (this.value == 1) {
					$("#info_laboral_nombre").show();
					$("#info_laboral_cargo").show();
					$("#info_laboral_ingreso").show();
				} else {
					$("#info_laboral_nombre").hide();
					$("#info_laboral_cargo").hide();
					$("#info_laboral_ingreso").hide();
				}
			})
		});
	</script>
@endsection
