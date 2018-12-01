@extends('menus.num_cuenta', ['title' => "Solicitudes canceladas"])
@section('esp', 'Solicitudes canceladas')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( 'registroTitulos/solicitudes_canceladas' ) }}">
@endsection
