@extends('menus.numero_cuenta', ['title' => "Información sobre solicitudes canceladas"])
@section('esp', 'Información sobre solicitudes canceladas')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( 'registroTitulos/solicitudes_canceladas' ) }}">
@endsection
@section('estilos')
   @section('estilos')
   	<link href="{{ asset('css/loading.css') }}" rel="stylesheet">
      <link href="{{ asset('css/solicitudesPendientes.css') }}" rel="stylesheet">
   @endsection
   @yield('sub-estilos')
@endsection
@section('identidadAlumno')
   <div class="info-solicitud-alumno">
     <br><br><br>@include('errors/flash-message')
     @if($info != null)
      <table align="center">
         <tr><td class="datos-personales">
            <div class="info-personal">
              <div class="info-personal-header">
                {!! $foto !!}
                <div class="fila">
                 <label for="">Nombre: </label> {!! $info[0]->nombre_completo !!}
                </div>
            </div>
      </div>
   </td><td class="datos-escolares">
         <div class="info-trayectorias">
               <table class="table table-bordered" style="width: 100%;">
                  <thead class="thead-dark bg-primary">
                     <th class="center" scope="col">Nº de cuenta</th>
                     <th class="center" scope="col">Nivel</th>
                     <th class="center" scope="col">Cve Carrera</th>
                     <th class="center" scope="col">Fecha cancelación</th>
                     <th class="center" scope="col">Motivo</th>
                  </thead>
                  <tbody>
                    <td><b>{!! $info[0]->num_cta !!}</b></td>
                    <td>{!! $info[0]->nivel !!}</td>
                    <td>{!! $info[0]->cve_carrera !!}</td>
                    <td>{!! $info[0]->fecha_cancelacion !!}</td>
                    <td>{!! $motivo[0]->DESCRIPCION_CANCELACION !!}</td>
                  </tbody>
              </table>
      </div>
   </td></tr></table>
   @endif
   </div><br>
@endsection
@section('sub-animaciones')
   <script src="{{asset('js/solicitud_eTitulos.js')}}"></script>
@endsection
