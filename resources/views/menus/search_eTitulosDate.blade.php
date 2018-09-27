@extends('menus.fecha', ['title' => "Solicitud para firma por fecha"])
@section('esp', 'Solicitud para firma por fecha')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( 'registroTitulos/buscar/fecha' ) }}">
@endsection
