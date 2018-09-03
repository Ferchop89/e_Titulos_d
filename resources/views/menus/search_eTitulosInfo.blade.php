@extends('menus.numero_cuenta', ['title' => "Títulos Electrónicos"])
@section('esp', 'Títulos Electrónicos')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( '/buscar' ) }}">

@endsection
@section('identidadAlumno')
   @include('errors/flash-message')
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
@endsection
@section('info-alumno')
   @if ($trayectorias != null)
      <div class="info-trayectorias">
            <table class="table table-bordered">
               <thead class="thead-dark bg-primary">
                  <th scope="col">Nº</th>
                  <th scope="col">Cve Plantel</th>
                  <th scope="col">Nombre del Plantel</th>
                  <th scope="col">Nivel</th>
                  <th scope="col">Cve Carrera</th>
                  <th scope="col">Cve Registro SEP</th>
                  <th scope="col">Nombre Carrera</th>
                  <th scope="col">Acción</th>
               </thead>
               <tbody>
                  @foreach ($trayectorias as $key => $value)
                     <tr>
                           <th>{{$key+1}}</th>
                           <td>{!! $value->carrp_unidad !!}</td>
                           <td></td>
                           <td>{!! $value->tit_nivel !!}</td>
                           <td>{!! $value->tit_plancarr !!}</td>
                           <td></td>
                           <td></td>
                           <td>
                              <a href = "{{ route('solicitar_SEP',[ 'numCta'=>$numCta, 'carrera'=>$value->tit_plancarr, 'nivel'=>$value->tit_nivel]) }}"class="btn btn-info">Solicitar</a>
                           </td>
                       </tr>
                  @endforeach
               </tbody>
           </table>
   </div>
   {{-- @if (Session::has('message'))
      <div class="alert alert-info">{{ Session::get('message') }}</div>
   @endif --}}

@endif

@endsection
