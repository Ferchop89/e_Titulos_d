@extends('layouts.app')
@section('title','CONDOC | '.$title)
{{-- @section('location')
    <div>
    	<p id="navegacion">
            <a href="{{ route('admin_dashboard') }}"><i class="fa fa-home" style="font-size:28px"></i></a>
    		<span> >> </span>
    		<a> Administración </a>
            <span> >> </span>
    		<a href="#"> {{$title}} </a> </p>
    </div>
@endsection --}}
@section('estilos')
   <link href="{{ asset('css/solicitudesPendientes.css') }}" rel="stylesheet">
   <link href="{{ asset('css/switch.css') }}" rel="stylesheet">
   <link href="{{ asset('css/loading.css') }}" rel="stylesheet">
   <link href="{{ asset('css/colordate.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container solicitudes">
    <div class="d-flex justify-content-between align-items-end mb-3">
      <br>
        <h2 id="titulo">{{$title.": ".$total}}</h2><br><br>
        <div class="loader"></div>
        {{-- <p class="button">
            <a href="{{ route('infoCedula',['ids'=>'check']) }}" class="btn btn-primary">ACTUALIZAR TODO</a>
        </p> --}}
    </div>
      <div class="filtros">
         {!! Form::open(['class'=>'form','method'=>'GET','id'=>'filtraSol', 'route'=>'filtraCedula']) !!}
            <table style="width: 100%; height:auto;">
               <tr>
                  <td>
                     <div class="form-group">
                       <label for="eSelect[]"> Filtro: </label>
                       {!! Form::select('eSelect[]', $listaErrores, $seleccion ) !!}
                       {{-- <button class="btn btn-success" type="submit">Filtrar</button> --}}
                     </div>
                  </td>
                  <td class="td-datepicker">
                     <div class="form-group">
                        <label for="datepicker" class="datepicker">Emisión de título:</label>
                        <div class="input-group idatepicker">
                           <input name = "datepicker" type="text" id="datepicker" class="form-control" value ={{$fecha_d}}>
                           <span class="input-group-addon">
                              <i class="fa fa-search"></i>
                           </span>
                        </div>
                        {{-- {{ Form::text('datepicker','',array('id'=>'datepicker','readonly', 'class' => '')) }} --}}
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
      </div>
      @if(count($lists)>0)

      @include('errors/flash-message')
   {{-- Desplegado el acordion de solicitudes filtradas --}}
   <div align="right">
      <form action='/registroTitulos/firma' method='post'>
         {!! csrf_field() !!}
         {!! $acordeon !!}
         <input type='submit' id="btn-update" class="btn btn-primary waves-effect waves-light b_btn" name='actualizar' value='Actualizar Información'/>
         <input type='submit' id="btn-enviar" class="btn btn-primary waves-effect waves-light b_btn" name='enviar' value='Enviar a Firma'/>
      </form>
    </div>
    @else
    <br><br>
    <div class="alert alert-danger alert-block detalles_info">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>No hay solicitudes registradas.</strong>
    </div>
    @endif
</div>

@endsection
@section('animaciones')
   <script  src="{{asset('js/check.js')}}"></script>
   <script  src="{{ asset('js/datepickerBlock.js') }}" ></script>
   <script  src="{{ asset('js/hoverRow.js') }}" ></script>
   <script src="{{asset('js/loading.js')}}"></script>
@endsection
