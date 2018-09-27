@extends('menus.numero_cuenta', ['title' => "Solicitud para firma por número de cuenta"])
@section('esp', 'Solicitud para firma por número de cuenta')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( 'registroTitulos/buscar' ) }}">

@endsection
