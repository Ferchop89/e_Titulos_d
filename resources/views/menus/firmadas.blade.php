@extends('layouts.app')
@section('title','CONDOC | '.$title)
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
<div class="solicitudes aFirma">
  <div class="d-flex justify-content-between align-items-end mb-3">
    <br>
      <h2 id="titulo">{{$title.": ".$total}}</h2>
    </div>
    <form class="form-group solicitud_f" method="POST" action="{{ url( 'registroTitulos/firmadas' ) }}">
        {!! csrf_field() !!}
      <div class="filtros">
         <table style="width:100%">
            <tr class="filtros">
               <td>
                  <div align="left">
                     <label for="fecha"> Selecciona una fecha: </label>
                     <input name = "fecha" type="text" id="datepicker" value ={{$fecha_formato}}>
                     <span>
                        <input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar_fecha" value="Solicitar"/>
                     </span>
                  </div>
               </td>
            </tr>
      <tr class="errores">
        <td>
          @if ($errors->has('fecha'))
          <div id="error" class="alert alert-danger">
            <ul>
               <li>{{ $errors->first('fecha') }}</li>
             </ul>
          </div>
          @endif
        </td>
      </tr>
    </table>
  </div>
  @if(count($lote)>0)
 {!! csrf_field() !!}
 {!! $acordeon !!}
 <div class="paginador">
     {{ $lote->links()}}
 </div>
</form>

  @else
  <br><br>
    <div class="alert alert-danger alert-block detalles_info">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>No hay registros.</strong>
    </div>
  @endif
</div>
<br><br>
@endsection
@section('animaciones')
   <script src="{{asset('js/check.js')}}"></script>
   <script  src="{{ asset('js/datepickerFirmas.js') }}" ></script>
@endsection
