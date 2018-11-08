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
@endsection
@section('content')
<div class="container solicitudes">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 id="titulo">{{$title.": ".$total}}</h2>
        {{-- <p class="button">
            <a href="{{ route('infoCedula',['ids'=>'check']) }}" class="btn btn-primary">ACTUALIZAR TODO</a>
        </p> --}}
    </div>
    @if(count($lists)>0)
      <div class="filtros">
         <div class="right">
            {!! Form::open(['class'=>'form','method'=>'GET','id'=>'filtraSol', 'route'=>'filtraCedula']) !!}
            {!! Form::select('listaErrores[]', $listaErrores, null,array('multiple') ) !!}
            <button class="btn btn-success" type="submit">Filtrar</button>
            {{ Form::close() }}
         </div>
      </div>
      @include('errors/flash-message')
   {{-- Desplegado el acordion de solicitudes filtradas --}}
      <form action='/registroTitulos/firma' method='post'>
         {!! csrf_field() !!}
         {!! $acordeon !!}
         <input type='submit' name='enviar' value='Enviar'/>
         <input type='submit' name='actualizar' value='Actualizar Todo'/>
      </form>


    @else
    <br><br>
    <div class="alert alert-danger alert-block detalles_info">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>No hay solicitudes registradas.</strong>
    </div>
    @endif
</div>

@endsection
@section('animaciones')
   <script src="{{asset('js/check.js')}}"></script>
@endsection
