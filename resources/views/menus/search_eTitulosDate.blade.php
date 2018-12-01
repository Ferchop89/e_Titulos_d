@extends('menus.fecha', ['title' => "Solicitud de información de títulos por fecha"])
@section('esp', 'Solicitud de información de títulos por fecha')
@section('ruta')

    <form class="form-group solicitud" method="POST" action="{{ url( 'registroTitulos/buscar/fecha' ) }}">
@endsection
