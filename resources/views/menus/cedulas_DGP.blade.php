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
   <link href="{{ asset('css/colordate.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container solicitudes aFirma">
   <div class="d-flex justify-content-between align-items-end mb-3">
      <br>
      <h2 id="titulo">{{$title}}</h2>
   </div>
   <form class="form-group solicitud_f" method="POST" action="{{ url( 'registroTitulos/cedulas_DGP' ) }}">
      {!! csrf_field() !!}
      <div class="filtros">
         <table style="width:100%">
            <tr class="filtros">
               <td>
                  <table style="width: 100%">
                     <tr>
                        <td class="td-datepicker">
                          <div class="form-group">
                             <label for="datepicker" class="datepicker">Fecha de envio a la DGP:</label>
                             <div class="input-group idatepicker">
                                @if ($fecha_inicial=="*")
                                    <input name = "fecha" type="text" id="datepicker" class="form-control" onchange="this.form.submit()">
                                @else
                                    <input name = "fecha" type="text" id="datepicker" value={{$fecha_inicial}} class="form-control" onchange="this.form.submit()">
                                @endif
                             </div>
                             <button class="btn btn-default" data-toggle="tooltip" title="borra"><i class="fa fa-times"></i></button>
                          </div>
                       </td>
                     </tr>
                  </table>
               </td>
               <td>
                  <table style="width: 100%">
                     <tr>
                        <td>
                           <label for="nivel"> Selecciona un nivel: </label>
                             <select id="nivel" name="nivel" style="width: 50%;" onchange="submit()">
                               @foreach($niveles as $niveles=>$nombre)
                                   @if ($nivel!=$niveles)
                                     <option value="{{ $niveles }}">{{ $nombre }}</option>
                                   @else
                                      <option value="{{ $niveles }}" selected>{{ $nombre }}</option>
                                   @endif
                               @endforeach
                             </select>
                        </td>
                        <td>
                           <a href="{{ route('pdf_DGP',[$nivel,$fecha_inicial]) }}" target="_blank"><i class="fa fa-file-pdf-o" style="font-size:36px;color:red">PDF</i></a>
                        </td>
                     </tr>
                  </table>
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

   </form>
      @if(!empty($niveles))
         {{-- Al existir niveles, la consulta arrojo resultados, por tanto, existe un acordion con datos --}}
         {!! $acordeon !!}
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
   <script src="{{asset('js/datepickerDGP.js')}}"></script>
@endsection
