@extends('menus.numero_cuenta', ['title' => "Títulos Electrónicos"])
@section('esp', 'Títulos Electrónicos')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( 'registroTitulos/buscar' ) }}">

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
                  <th class="center" scope="col">Nº</th>
                  <th class="center" scope="col">Cve Plantel</th>
                  <th class="center" scope="col">Nombre del Plantel</th>
                  <th class="center" scope="col">Nivel</th>
                  <th class="center" scope="col">Cve Carrera</th>
                  <th class="center" scope="col">Cve Registro SEP</th>
                  <th class="center" scope="col">Nombre Carrera</th>
                  <th class="center" scope="col">Acción</th>
               </thead>
               <tbody>
                  @for($i=0; $i < count($trayectorias); $i++)

                  @endfor
                  @foreach ($trayectorias as $key => $value)
                     {{-- {{dd($trayectorias, $key)}} --}}
                     {{-- {{dd($value['tit_plancarr'])}} --}}
                     <tr>
                        <th class="center">{{$key+1}}</th>
                        <td>{!! $value['carrp_unidad'] !!}</td>
                        <td>{!! strtoupper($value['plan_nombre']) !!}</td>
                        <td>{!! $value['tit_nivel'] !!}</td>
                        <td>{!! $value['tit_plancarr'] !!}</td>
                        <td id="solicitud_{{$key}}">{!! $value['solicitud']!!}</td>
                        <td>{!! $value['solicitud']!!}</td>
                        {{-- <td>{!! $value->carrera !!}</td> --}}
                        <td>
                           <a href = "{{ route('solicitar_SEP',[ 'numCta'=>$numCta, 'nombre'=> $identidad->dat_nombre, 'carrera'=>$value['tit_plancarr'], 'nivel'=>$value['tit_nivel']]) }}"class="btn btn-info">Solicitar</a>
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
@section('sub-animaciones')
   <script src="{{asset('js/solicitud_eTitulos.js')}}"></script>
@endsection
