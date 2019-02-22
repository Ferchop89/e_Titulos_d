@extends('menus.numero_cuenta', ['title' => "Cancelación de cédulas"])
@section('esp', 'Cancelación de cédulas')
@section('ruta')
    <form class="form-group solicitud" method="POST" action="{{ url( 'registroTitulos/cedulas_canceladas' ) }}">
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
     <br><br><br>
     @if($info != null)
      <table align="center">
         <tr><td class="datos-personales">
            <div class="info-personal">
              <div class="info-personal-header">
                {!! $foto !!}
                <div class="fila">
                 <label for="">N° de cuenta: </label> {!! $info[0]->num_cta !!}
                </div>
                <div class="fila">
                 <label for="">Nombre: </label> {!! $info[0]->nombre_completo !!}
                </div>
            </div>
      </div>
   </td><td class="datos-escolares">
     {!! Form::open(['class'=>'form','method'=>'POST','id'=>'cancelaC', 'route'=>'cancelaC']) !!}
         <div class="info-trayectorias">
           @include('errors/flash-message')
           <input type="hidden" name="num_cta" value="{{$info[0]->num_cta}}">
               <table class="table table-bordered" style="width: 100%;">
                  <thead class="thead-dark bg-primary">
                     <th class="center" scope="col">Nº</th>
                     <th class="center" scope="col">Nivel</th>
                     <th class="center" scope="col">Cve Carrera</th>
                     <th class="center" scope="col">Motivo</th>
                     <th class="center" scope="col">Acción</th>
                  </thead>
                  <tbody>
                    @foreach ($info as $key => $value)
                      <th class="center">{{$key+1}}</th>
                      <td>{!! $info[$key]->nivel !!}</td>
                      <input type="hidden" name="cve_carrera" value="{{$info[$key]->cve_carrera}}">
                      <td>{!! $info[$key]->cve_carrera !!}  </td>
                      <td><select id="motivo" name="motivo" style="width:70%;">
              			     @foreach($motivos as $key=>$value)
                              <option value="{{ $value['id'] }}">{!! $value['DESCRIPCION_CANCELACION'] !!}</option>
              			     @endforeach
              			  </select></td>
                      <td>
                        @if($info[0]->status == 7)
                          <button id = "cancelaC" type="submit" class="btn btn-danger">Cancelar</button>
                        @else
                          NA
                        @endif
                      </td>
                    @endforeach
                  </tbody>
              </table>
      </div>
      {{ Form::close() }}
   </td></tr></table>
   @else
    @include('errors/flash-message')
   @endif
   </div><br>
@endsection
@section('sub-animaciones')
   <script src="{{asset('js/solicitud_eTitulos.js')}}"></script>
@endsection
