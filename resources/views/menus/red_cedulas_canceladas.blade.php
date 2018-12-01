@extends('menus.num_cuenta', ['title' => "Cédulas canceladas"])
@section('esp', 'Cédulas canceladas')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( 'registroTitulos/cedulas_canceladas' ) }}">
@endsection
