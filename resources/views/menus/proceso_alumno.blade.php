@extends('layouts.app')
@section('title')
    CONDOC | {{$title}}
@endsection
{{-- @section('title','CONDOC | Proceso de Títulos') --}}
{{-- @section('location')
    <div>
    	<p id="navegacion">
            <a href="{{ route('admin_dashboard') }}"><i class="fa fa-home" style="font-size:28px"></i></a>
    		<span> >> </span>
            <span> >> </span>
    		<a href="#"> {{$title}} </a> </p>
    </div>
@endsection --}}
@section('estilos')
   <link href="{{ asset('css/solicitudesPendientes.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container solicitudes proceso_t">
  <form class="form-group solicitud_f" method="POST" action="{{ route( 'cancelaProcesoAlumno' ) }}">
      {!! csrf_field() !!}
  <div class="d-flex justify-content-between align-items-end mb-3">
    <br>
    {{-- <h2 id="titulo">{{"Proceso de Titulos : ".$nombre." - ".$num_cta}}</h2> --}}
    <h2 id="titulo">{{$title}} <h3>{{$nombre." - ".$num_cta}}</h3></h2>

    <br>
  </div>
  <br>
  <table style="width: 90%;" align="center" class="proceso_alumno">
    <tr>
      <th>Proceso</th>
      <th>Estado</th>
      <th class="center">Fecha</th>
      <th class="center">Tiempo Transcurrido</th>
    </tr>
    <tr>
      <td>
        Solicitud de cédula profesional electrónica
        <input type="hidden" name="num_cta" value="{{$num_cta}}">
        <input type="hidden" name="nivel" value="{{$nivel}}">
      </td>
      @if($info['solicitud'] != false)
      <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
      <td align="center"><span><a>{{ $info['fecha_sol'] }}</a></span></td>
      <td align="center"><span><a href=''>{{ $info['tiempo_sol'] }}</a></span></td>
      @else
      <td align="center"><span class='fa fa-clock-o f_rec'/></td>
      <td></td>
      @endif
    </tr>
    <tr>
      <td>Autorizado por el depto. de Títulos <input type="hidden" name="nombre" value="{{$nombre}}"></td>
      @if($info['autorizacion'] != false)
      <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
      <td align="center"><span><a href=''>{{ $info['fecha_aut'] }}</a></span></td>
      <td align="center"><span><a href=''>{{ $info['tiempo_aut'] }}</a></span></td>
      @else
      <td align="center"><span class='fa fa-clock-o f_rec'/></td>
      <td></td>
      <td></td>
      @endif
    </tr>
    <tr>
      <td>Firmado por el depto. de Títulos <input type="hidden" name="carrera" value="{{$carrera}}"></td>
      @if($info['firma_tit'] != false)
      <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
      <td align="center"><span><a href=''>{{ $info['fecha_tit'] }}</a></span></td>
      <td align="center"><span><a href=''>{{ $info['tiempo_tit'] }}</a></span></td>
      @else
      <td align="center"><span class='fa fa-clock-o f_rec'/></td>
      <td></td>
      <td></td>
      @endif
    </tr>
    <tr>
      <td>Firmado por la Directora DGAE</td>
      @if($info['firma_dgae'] != false)
      <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
      <td align="center"><span><a href=''>{{ $info['fecha_dgae'] }}</a></span></td>
      <td align="center"><span><a href=''>{{ $info['tiempo_dgae'] }}</a></span></td>
      @else
      <td align="center"><span class='fa fa-clock-o f_rec'/></td>
      <td></td>
      <td></td>
      @endif
    </tr>
    <tr>
      <td>Firmado por el Secretario General</td>
      @if($info['firma_sec'] != false)
      <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
      <td align="center"><span><a href=''>{{ $info['fecha_sec'] }}</a></span></td>
      <td align="center"><span><a href=''>{{ $info['tiempo_sec'] }}</a></span></td>
      @else
      <td align="center"><span class='fa fa-clock-o f_rec'/></td>
      <td></td>
      <td></td>
      @endif
    </tr>
    <tr>
      <td>Firmado por el Rector</td>
      @if($info['firma_rec'] != false)
      <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
      <td align="center"><span><a href=''>{{ $info['fecha_rec'] }}</a></span></td>
      <td align="center"><span><a href=''>{{ $info['tiempo_rec'] }}</a></span></td>
      @else
      <td align="center"><span class='fa fa-clock-o f_rec'/></td>
      <td></td>
      <td></td>
      @endif
    </tr>
    <tr>
      <td>Enviado a la Dirección General de Profesiones</td>
      @if($info['enviada'] != false)
      <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
      @else
      <td align="center"><span class='fa fa-clock-o f_rec'/></td>
      @endif
      <td></td>
      <td></td>
    </tr>
  </table>
  <br><br>
  <div>
    <p><b>¿Desea cancelar la solicitud?</b></p>
    <p>
      <table style="width:50%; margin-left: 6%;"><tr>
        <td>Seleccione un motivo:</td>
        <td><select id="motivo" name="motivo" style="width:95%;">
			     @foreach($motivos as $key=>$value)
                <option value="{{ $value['id'] }}">{!! $value['DESCRIPCION_CANCELACION'] !!}</option>
			           <!-- <option value="{{ $motivos[$key]->id }}">{!! $motivos[$key]->DESCRIPCION_CANCELACION !!}</option> -->
			     @endforeach
			  </select></td>
        <td><input type="submit" class="btn btn-danger waves-effect waves-light" value="Cancelar"/></td>
      </tr></table>
    </p>
  </div>
</form>
  <br><br>
</div>
@endsection
