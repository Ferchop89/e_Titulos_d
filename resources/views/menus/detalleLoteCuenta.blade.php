@extends('layouts.app')
@section('title','CONDOC | '.$title)
{{-- @section('location')
    <div>
    	<p id="navegacion">
            <a href="{{ route('admin_dashboard') }}"><i class="fa fa-home" style="font-size:28px"></i></a>
    		<span> >> </span>
    		<a> Administración </a>
            <span> >> </span>
    		<a href="#"> {{$title}} </a> </p>
    </div>
@endsection --}}
@section('estilos')
   <link href="{{ asset('css/solicitudesPendientes.css') }}" rel="stylesheet">
   <link href="{{ asset('css/loading.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="detalle-lote">
    <div class="d-flex justify-content-between align-items-end mb-3">
      <br>
        <h2 id="titulo">{{$title}}</h2>
         <a class="" href="../../proceso?numCta={{$num_cta}}&nombre={{$nombre}}&carrera={{$carrera}}&nivel={{$nivel}}">
           <i class="fa fa-arrow-circle-o-left"></i>
        </a>
        <div class="loader"></div>
    </div>
      @if(count($list)>0)
         @include('errors/flash-message')
         {{-- Desplegado el acordion de solicitudes filtradas --}}
         {!!$list!!}
      @else
         <div class="alert alert-danger alert-block detalles_info">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>No hay solicitudes registradas.</strong>
         </div>
      @endif
   </div>
@endsection
@section('animaciones')
   <script src="{{asset('js/loading.js')}}"></script>
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
      var num_cta =  "<?php echo $num_cta ?>";
      var elmnt = window.innerHeight;
      var y = getOffset( document.getElementById(num_cta) ).top;
      window.scroll(0, y-(elmnt/2));
      //document.getElementById(num_cta).style.background = "rgb(255, 235, 204)";
      document.getElementById(num_cta).style.background = "rgb(230, 240, 255)";
  </script>
@endsection
