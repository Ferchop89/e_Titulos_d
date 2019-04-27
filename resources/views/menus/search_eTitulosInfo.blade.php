@extends('menus.numero_cuenta', ['title' => "Solicitud de información de títulos por número de cuenta"])
@section('esp', 'Solicitud de información de títulos por número de cuenta')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( 'registroTitulos/buscar' ) }}">
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
     <br><br>@include('errors/flash-message')
     @if($identidad != null)
      <table align="center">
         <tr>
            <td class="datos-personales">
               <div class="info-personal">
                  <div class="info-personal-header">
                     @if ($identidad != null)
                        {!! $foto !!}
                        <div class="fila">
                           <label for="">Nº de Cuenta: </label> {!! $numCta !!}
                        </div>
                        <div class="fila">
                           <label for="">Nombre: </label> {!! $identidad->dat_nombre !!}
                        </div>
                        <div class="fila">
                           <label for="">CURP: </label>@if($identidad->dat_curp) {!! $identidad->dat_curp !!} @endif
                        </div>
         @endif
            </div>
      </div>
   </td><td class="datos-escolares">
      @if ($trayectorias != null)
         <div class="info-trayectorias">
               <table class="table table-bordered" style="width: 100%;">
                  <thead class="thead-dark bg-primary">
                     <th class="center" scope="col">Nº</th>
                     <th class="center" scope="col">Cve Plantel</th>
                     <th class="center" scope="col">Nombre del Plantel</th>
                     <th class="center" scope="col">Nivel</th>
                     <th class="center" scope="col">Cve Carrera</th>
                     <th class="center" scope="col">Nombre Carrera</th>
                     <th class="center" scope="col">Fecha emisión título</th>
                     <th class="center" scope="col">Acción</th>
                  </thead>
                  <tbody>
                     @foreach ($trayectorias as $key => $value)
                        <tr>
                           <th class="center">{{$key+1}}</th>
                           <td>{!! $value['carrp_plan'] !!}</td>
                           @if($value['plan_nombre']!='')
                              <td>{!! strtoupper($value['plan_nombre']) !!}</td>
                           @else
                              <td class="alert alert-danger">{!!strtoupper("Clave sin registro")!!}</td>
                           @endif
                           <td>{!! $value['tit_nivel'] !!}</td>
                           <td>{!! $value['tit_plancarr'] !!}</td>
                           <td>{!! $value['carrp_nombre'] !!}</td>
                           <td>{!! $value['tit_fec_emision_tit'] !!}</td>
                           <td>
                             @if($value['solicitud'] == false)
                             <a href = "{{ route('solicitar_SEP',[ 'numCta'=>$numCta, 'nombre'=> $identidad->dat_nombre, 'carrera'=>$value['tit_plancarr'], 'nivel'=>$value['tit_nivel']]) }}"class="btn btn-info">Solicitar</a>
                             @else
                             <a href = "{{ route('procesoAlumno',[ 'numCta'=>$numCta, 'nombre'=> $identidad->dat_nombre, 'carrera'=>$value['tit_plancarr'], 'nivel'=>$value['tit_nivel']]) }}" class="btn btn-info">En proceso</a>
                             @endif
                           </td>
                        </tr>
                     @endforeach
                  </tbody>
              </table>
      </div>
   @else
   <br><br>
     <div class="alert alert-danger alert-block detalles_info">
     	<button type="button" class="close" data-dismiss="alert"></button>
     	<strong> No es posible realizar este proceso. <br> El usuario no se encuentra en la Tabla de Títulos.</strong>
     </div>
   @endif
   </td></tr></table>
   @endif
   </div><br>
@endsection
@section('sub-animaciones')
   <script src="{{asset('js/solicitud_eTitulos.js')}}"></script>
@endsection
