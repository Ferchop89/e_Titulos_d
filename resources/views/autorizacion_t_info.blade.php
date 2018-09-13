@extends('layouts.app')
@section('title', 'CONDOC | Autorización de Transferencia de Información')

@section('estilos')
	<link href="{{ asset('css/rev_estudios.css') }}" rel="stylesheet">
@endsection

@section('location')
<div>
	<p id="navegacion">
		<a href="{{ route('home') }}"><i class="fa fa-home" style="font-size:28px"></i></a> </p>
</div>
@endsection

@section('content')
<div class="container cont_aut">
	<div  class="ati_vista">
		<h1 align="center">AUTORIZACIÓN DE TRANSFERENCIA DE INFORMACIÓN</h1>
		<div class="d_m">
			<p><i>Por favor, completa los campos con la información correcta y posteriormente acepta lo establecido en este apartado.</i></p>
		</div>
		<form method="POST" action="{{ url('registroTitulos/contactos/ati') }}">
			{!! csrf_field() !!}
			@if ($errors->any())
			    <div id="error" class="alert alert-danger">
			        <ul>
			            @foreach ($errors->all() as $error)
			                <li>{{ $error }}</li>
			            @endforeach
			        </ul>
			    </div>
			@endif
	  <div class="div_cont">
			<p> Ciudad Universitaria, Cd. Mx., a {{$day}} de {{$month}} de {{$year}} </p>
			<br>
			<p align="justify">
				<div>Director de Certificación y</div>
				<div>Control Documental,</div>
				<div>D.G.A.E.</div>
				<div>P r e s e n t e.</div>
			</p>
			<br>
			<p align="justify">Por medio de la presente manifiesto que se me ha informado de que la Dirección de Certificación y Control
			Documental de la DGAE-UNAM, debe de enviar información de mis datos académicos y profesionales como
			egresado(a), a la Dirección General de Profesiones de la Secretaría de Educación Pública, para que yo pueda
			en su oportunidad realizar el trámite de registro de título y obtención de cédula profesional ante la citada
			dependencia y que además debo actualizar mis datos personales siguientes: </p>
			<br>
			<p align="justify">Nombre completo: <input type="text" class="width_n" name="nombre"></p>
			<p align="justify">CURP: <input type="text" class="width_c" name="curp" maxlength="18"></p>
			<p align="justify"><span>Núm. telefónico fijo: <input type="text" class="width_t" name="num_tel" maxlength="10"></span>
			<span> Núm. telefónico celular: <input type="text" class="width_t" name="num_cel" maxlength="10"></span></p>
			<p align="justify"><b>Correo electrónico</b>(a donde será enviada cualquier información del trámite, incluido el número de cédula profesional):
				<input type="email" class="width_ce" name="correo">
			</p>
			<br>
			<p align="justify">De acuerdo con el artículo octavo del Reglamento de Transparencia, acceso a la información pública y
			protección de datos personales para la Univerdidad Autponoma de México, se considera como información
			confidencial los datos personales de todos y cada uno de los egresados que deseen realizar el trámite de
		 	registro y otención de cédula profesional.</p>
			<p align="justify">Por lo anterior acepto y autorizo que la DGAE-UNAM utilice de forma automatizada mis datos personales y
			académicos, los cuales formarán parte de la base de datos de la misma dependencia con la finalidad de usarlos
			en forma enunciativa más no limitativa para que me identifiquen, ubiquen, comuniquen, contacten, y envíen
			información por cualquier medio posible además de transferirlos a la Dirección General de Profesiones de la
		 	Secretaría de Educación Pública, para los fines antes señalados.</p>
			<hr>
			<p><strong>Nota: </strong>A continuación, se mostrará un documento que deberás imprimir.</p>
			<div align="center" class="cont_aut"><p>
			 		<input type="checkbox" name="acepto"> Sí, acepto
					<br><br>
			 		<input type="submit" class="btn btn-primary" value="Guardar">
			</p></div>
			</div>
		</form>
	</div>
</div>
@endsection
