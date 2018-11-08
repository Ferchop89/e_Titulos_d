@extends('layouts.app')
@section('title','CONDOC | '.$title)

@section('estilos')
   <link href="{{ asset('css/solicitudesPendientes.css') }}" rel="stylesheet">
   <link href="{{ asset('css/colordate.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container firmas progreso">
    <div class="d-flex justify-content-between align-items-end mb-3">
      <br>
        <h2 id="titulo">{{$title.": ".$total}}</h2>
    </div>
    <!-- <form class="form-group solicitud_f" method="POST" action="{{ url( 'registroTitulos/firmas_progreso' ) }}">
        {!! csrf_field() !!} -->
    <div class="filtros indice">
      <table style="width:100%">
        <tr>
            <td>
               <table style="width:100%">
                 <tr>
                    <td><b>Firmas: </b></td>
                    <td>Departamento Títulos - <span class='fa fa-check-square-o f_dirt'/></td>
                    <td>Directora DGAE - <span class='fa fa-check-square-o f_dir'/></td>
                    <td>Secretario Gral.- <span class='fa fa-check-square-o f_sec'/></td>
                    <td>Rector UNAM- <span class='fa fa-check-square-o f_rec'/></td>
                    <td>
                  </tr>
               </table>
            </td>
            <td>
               {!! Form::open(['class'=>'form','method'=>'GET','id'=>'filtraLote', 'route'=>'registroTitulos/firmas_progreso']) !!}
               <table>
                  <tr class="filtros">
                     <td>
                        <div class="div-datepicker center">
                           <div class="form-group">
                              <label for="datepicker" class="datepicker">Selecciona una fecha:</label>
                              <div class="input-group idatepicker">
                                 <input name = "datepicker" type="text" id="datepicker" class="form-control" value="{{$fecha_o}}">
                                 <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                 </span>
                              </div>
                           </div>
                        </div>
                     </td>
                     <td>
                        <div class="form-group">
                           <button id = "gestionL" type="submit" class="btn btn-primary">Consultar</button>
                        </div>
                     </td>
                  </tr>
               </table>
               {{ Form::close() }}
            </td>
         </tr>
         <tr class="errores">
            <td></td>
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
    <br/>
    @if($total>0)
      @include('errors/flash-message')
      {{-- Desplegado el acordion de solicitudes filtradas --}}
      {!! $acordeon !!}
    @else
    <br><br>
    <div class="alert alert-danger alert-block detalles_info">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>No hay solicitudes registradas.</strong>
    </div>
    @endif
  <!-- </form> -->
</div>
<br><br>
@endsection
@section('animaciones')
   <script  src="{{ asset('js/datepickerLotes.js') }}" ></script>
@endsection
