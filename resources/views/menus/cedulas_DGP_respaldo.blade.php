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
<div class="container solicitudes aFirma">
  @if(isset($lote))
  <div class="d-flex justify-content-between align-items-end mb-3">
    <br>
      <h2 id="titulo">{{$title.": ".$total}}</h2>
    </div>
    <form class="form-group solicitud_f" method="POST" action="{{ url( 'registroTitulos/cedulas_DGP' ) }}">
        {!! csrf_field() !!}
      <div class="filtros">
         <table style="width:100%">
           <tr class="filtros">
              <td>
                <table style="width: 100%"><tr>
                 <td>
                 <div align="left">
                    <label for="fecha"> Selecciona fecha de lote: </label>
                    <input id="fecha" type="date" name="fecha" value={{$aux}}>
                 </div>
              </td>
              <td>
                 <div align="left"><input type="submit" class="btn btn-primary waves-effect waves-light" name="seleccion" value="Solicitar"/></div>
              </td>
            </tr>
         </table></td>
              <td>
                <table style="width: 100%"><tr>
                <td><div align="right">
                   <label for="fecha_env"> Selecciona fecha de envío: </label>
                   <input id="fecha" type="date" name="fecha_env">
                </div></td>
              <td><div align="right"><input type="submit" class="btn btn-info waves-effect waves-light" name="impresion" value="Imprimir listado"/></div></td>
              </tr></table>
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
        <td>
          @if ($errors->has('fecha_env'))
          <div id="error" class="alert alert-danger">
            <ul>
               <li>{{ $errors->first('fecha_env') }}</li>
             </ul>
          </div>
          @endif
          @include('errors/flash-message')
        </td>
      </tr>
    </table><br>
  </div>
  @if(count($lote)>0)
 {!! csrf_field() !!}
 {!! $acordeon !!}
</form>

  @else
  <br><br>
    <div class="alert alert-danger alert-block detalles_info">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>No hay registros.</strong>
    </div>
  @endif
  @else
  <div class="d-flex justify-content-between align-items-end mb-3">
    <br>
      <h2 id="titulo">{{$title}}</h2>
  </div>
  <div class="alert alert-danger alert-block detalles_info centrar">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>No hay registros.</strong>
  </div>
  @endif
</div>
<br><br>
@endsection
@section('animaciones')
   <script src="{{asset('js/check.js')}}"></script>
@endsection
