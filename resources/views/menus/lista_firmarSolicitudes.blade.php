@extends('layouts.app')
@section('title','CONDOC | '.$title)

@section('estilos')
   <link href="{{ asset('css/solicitudesPendientes.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container solicitudes">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 id="titulo">{{$title.": ".$total}}</h2>
    </div>
    @if(count($lists)>0)

      {{-- Desplegado el acordion de solicitudes filtradas --}}
      {!! $acordeon !!}

    @else
        <p>
            No hay Solcitudes registradas.
        </p>
    @endif
</div>
@endsection
