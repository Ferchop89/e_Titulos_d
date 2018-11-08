@extends('layouts.app')
@section('title', 'CONDOC | Autorización de Transferencia de Información')

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
		{{-- <div class="d_m">
			<p><i>Por favor, completa los campos con la información correcta y posteriormente acepta lo establecido en este apartado.</i></p>
		</div> --}}
		<form method="POST" action="{{ url('alumnos/ati') }}">
			{!! csrf_field() !!}
	  <div class="div_cont">
			<p class="date"> Ciudad Universitaria, Cd. Mx., a {{$fecha->day}} de {{$fecha->month}} de {{$fecha->year}}.</p>
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
					{{-- @if (old('nombres'))

					@endif --}}
					{{-- {{ dd( )}} --}}
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
@endsection
