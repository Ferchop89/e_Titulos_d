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
            <a href="{{ route('admin/usuarios/nuevo') }}" class="btn btn-primary">Nuevo Usuario</a>
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

   {{-- Desplegado el acordion de solicitudes filtradas --}}
   {!! $acordeon !!}

    @else
        <p>
            No hay Solcitudes registradas.
        </p>
    @endif
</div>
{{-- <div class="paginador">
    {{ $lists->links()}}
</div> --}}
@endsection
