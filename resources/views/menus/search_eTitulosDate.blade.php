@extends('menus.fecha', ['title' => "Solicitud de FEU por fecha"])
@section('esp', 'Solicitud de FEU por fecha')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( 'registroTitulos/buscar/fecha' ) }}">

@endsection
