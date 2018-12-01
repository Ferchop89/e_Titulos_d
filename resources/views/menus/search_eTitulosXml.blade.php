@extends('menus.numero_cuenta', ['title' => "olicitud de información de títulos por número de cuenta"])
@section('esp', 'olicitud de información de títulos por número de cuenta')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( '/buscar' ) }}">

@endsection
