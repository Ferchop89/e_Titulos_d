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
         <form class="" action="{{ route('postAutoriza') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="lote" value="{!! $lote !!}">
            <div class="form-group row">
              <label class="col-sm-5 col-form-label" for="autorizaPass"> Introduce la contraseña: </label>
              <input type="password" name="autorizaPass" value="" placeholder="Contraseña" class="file_in">
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
              <input type="submit" name="btnAutoriza" value="Autorizar" class="btn btn-primary waves-effect waves-light">
            </div>
         </form>
      </div>
   </div>
@endsection
@section('animaciones')
   <script src="{{asset('js/loading.js')}}"></script>
@endsection
