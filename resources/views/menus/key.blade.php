@extends('layouts.app')
@section('title','CONDOC | '.$title)
@section('estilos')
   <link href="{{ asset('css/solicitudesPendientes.css') }}" rel="stylesheet">
   <link href="{{ asset('css/loading.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="detalle-lote">
    <div class="d-flex justify-content-between align-items-end mb-3">
      <br>
        <h2 id="titulo">{{$title}}</h2>
        <div class="loader"></div>
    </div>
      <div class="container firma_avanzada">
         <form class="" action="{{ route('firmaSat') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div><div class="form-group row">
              <label class="col-sm-5 col-form-label" for="file"> Selecciona un archivo: </label>
              <input type="file" name="file" id="file" class="form-control col-sm-7 file_in">
            </div>
            <input type="hidden" name="lote" value="{{$lote}}">
            <input type="hidden" name="curp" value="{{$curp}}">
            <input type="hidden" name="cuentas" value="{{$cuentas}}">
            <input type="hidden" name="cadenas" value="{{$cadenas}}">
            <div class="form-group row">
              <label class="col-sm-5 col-form-label" for="passFirma">Introduce la contraseña:</label>
              <input type="password" name="passFirma" value="" placeholder="Contraseña SAT" class="form-control file_in">
            </div>
            @include('errors/flash-message')
            @if ($errors->any())
                <div id="error" class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div align="right">
              <a href="{{route('registroTitulos/response/firma')}}"><button type="button" class="btn btn-default waves-effect waves-light">Regresar</button></a>
              <input type="submit" name="btnFirmar" class="btn btn-primary waves-effect waves-light" value="Firmar">
            </div>
          </div>
         </form>
      </div>
   </div>
@endsection
@section('animaciones')
   <script src="{{asset('js/loading.js')}}"></script>
@endsection
