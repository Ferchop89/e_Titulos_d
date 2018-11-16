@extends('layouts.app')
@section('title','CONDOC | '.$title)

@section('estilos')
   <link href="{{ asset('css/solicitudesPendientes.css') }}" rel="stylesheet">
   <link href="{{ asset('css/loading.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container firmas">
    <div class="d-flex justify-content-between align-items-end mb-3">
      <br>
        <h2 id="titulo">{{$title.": ".$total}}</h2>
        <div class="loader"></div>
    </div>
    <br>
    @include('errors/flash-message')
    @if(count($lists)>0)
      {{-- Desplegado el acordion de solicitudes filtradas --}}
      {!! $acordeon !!}
      <div class="paginador">
          {{ $lists->links()}}
      </div>
    @else
    <br><br>
    <div class="alert alert-danger alert-block detalles_info">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>No hay lotes pendientes de firma.</strong>
    </div>
    @endif
</div>
<br><br>
@endsection
@section('animaciones')
   <script src="{{asset('js/session.js')}}"></script>
   <script src="{{asset('js/loading.js')}}"></script>
@endsection
