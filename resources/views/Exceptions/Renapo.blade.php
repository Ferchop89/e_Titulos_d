@extends('layouts.app')
@section('title','CONDOC | Servicio no disponible')
{{-- @section('location')
    <div>
    	<p id="navegacion">
            <a href="{{ route('admin_dashboard') }}"><i class="fa fa-home" style="font-size:28px"></i></a>
    		<span> >> </span>
            <span> >> </span>
    		<a href="#"> Error  </a> </p>
    </div>
@endsection --}}
@section('estilos')
   <link href="{{ asset('css/solicitudesPendientes.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container solicitudes aFirma">
  <div class="d-flex justify-content-between align-items-end mb-3">
    <br>
      <h2 id="titulo">Servicio no disponible.</h2> <br><br><br><br>
      <h4 align="center">Validación de CURP temporalmente fuera de servicio. Intente más tarde.</h4>
    </div>
  </div>
@endsection
