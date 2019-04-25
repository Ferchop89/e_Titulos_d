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
<div class="solicitudes proceso_t">
  <form class="form-group solicitud_f" method="POST" action="{{ route( 'cancelaProcesoAlumno' ) }}">
      {!! csrf_field() !!}
  <div class="d-flex justify-content-between align-items-end mb-3">
    <br>
    {{-- <h2 id="titulo">{{"Proceso de Titulos : ".$nombre." - ".$num_cta}}</h2> --}}
    <h2 id="titulo">{{$title}}</h2>
    <table style="width: 90%;" align="center">
      <td align="center">{!! $foto !!}</td>
      <td align="left"><h3>{{$nombre." - ".$num_cta}}</h3><h5>Nivel: [{!! $nivel !!}] {!! $n_nivel !!} | Carrera: [{!! $carrera !!}] {!! $n_carrera !!}</h5></td>
    </table>
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
      <td align="center"><span>{{ $info['fecha_sol'] }}</span></td>
      <td align="center"><span>{{ $info['tiempo_sol'] }}</span></td>
      @else
      <td align="center"><span class='fa fa-clock-o f_rec'/></td>
      <td></td>
      @endif
    </tr>
    <tr>
      <td>Revisión por el depto. de Títulos <input type="hidden" name="nombre" value="{{$nombre}}"></td>
      @if($info['revision'] != false)
      <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
      <td align="center"><span>{{ $info['fecha_rev'] }}</span></td>
      <td align="center"><span>{{ $info['tiempo_rev'] }}</span></td>
      @else
      <td align="center"><span class='fa fa-clock-o f_rec'/></td>
      <td></td>
      <td></td>
      @endif
    </tr>
    <tr>
      <td>Firmado por el depto. de Títulos <input type="hidden" name="carrera" value="{{$carrera}}"></td>
      @if($info['autorizacion'] != false)
      <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
      <td align="center"><span>{{ $info['fecha_aut'] }}</span></td>
      <td align="center"><span>{{ $info['tiempo_aut'] }}</span></td>
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
      <td align="center"><span><a href=".../../informacionDetallada/cuenta/lote?fechaLote={{$lote}}&num_cta={{$num_cta}}&carrera={{$carrera}}">{{ $info['fecha_dgae'] }}</a></span></td>
      <td align="center"><span>{{ $info['tiempo_dgae'] }}</span></td>
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
    <tr>
      <td>Descargado de la Dirección General de Profesiones</td>
      @if($info['descargada'] != false)
      <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
      @else
      <td align="center"><span class='fa fa-clock-o f_rec'/></td>
      @endif
      <td></td>
      <td></td>
    </tr>
    <tr>
      @if($status_tit == 7)
        <td>Rechazado por la Dirección General de Profesiones</td>
        @if($info['tit_acepta'] != false)
        <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
        @else
        <td align="center"><span class='fa fa-clock-o f_rec'/></td>
        @endif
        <td></td>
        <td></td>
      @elseif($status_tit == 8)
        <td>Aprobado por la Dirección General de Profesiones</td>
        @if($info['tit_rechaza'] != false)
        <td align="center"><span class='fa fa-check-square-o f_dir'/></td>
        @else
        <td align="center"><span class='fa fa-clock-o f_rec'/></td>
        @endif
        <td></td>
        <td></td>
      @endif
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
