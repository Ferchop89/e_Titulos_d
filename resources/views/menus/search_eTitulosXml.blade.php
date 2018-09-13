@extends('menus.numero_cuenta', ['title' => "Títulos Electrónicos"])
@section('esp', 'Títulos Electrónicos')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( '/buscar' ) }}">

@endsection
