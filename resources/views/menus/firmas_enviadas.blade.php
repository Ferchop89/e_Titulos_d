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
<div class="solicitudes aFirma">
   <div class="d-flex justify-content-between align-items-end mb-3">
      <br>
      <h2 id="titulo">{{$title.": ".$total}}</h2>
      </div>
      @if(!empty($lotes))
         <form class="form-group solicitud_f" method="POST" action="{{ route( 'registroTitulos/firmas_busqueda/seleccion' ) }}">
             {!! csrf_field() !!}
              <div class="filtros">
                <table style="width:100%">
                    <tr class="filtros">
                      {{ Form::open() }}
                       <td class="td-datepicker">
                          <div class="form-group">
                             <label for="datepicker" class="datepicker">Emisión de título:</label>
                             <div class="input-group idatepicker">
                                {{-- <input name = "fecha" type="text" id="datepicker" class="form-control" value =""> --}}
                                <input name = "fecha" type="text" id="datepicker" class="form-control" value ="{{$fechaOmision}}" onchange="this.form.submit()">
                                <span class="input-group-addon">
                                   <i class="fa fa-search"></i>
                                </span>
                             </div>
                          </div>
                       </td>
                       {{ Form::close() }}
                       {{-- <td><div align="right"><input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar" value="Solicitar"/></div></td> --}}
                       {{ Form::open() }}
                       <td>
                          <input name = "fecha" type="hidden" id="datepicker" class="form-control" value ="{{$fechaOmision}}">
                          <label for="lotes"> Lote</label>
                          <select id="lotes" name="lotes" maxlength="4" class="menor" onchange="this.form.submit()">
                          <option value="0">--Lote--</option>
                          @foreach($lotes as $flote)
                             @if ($lote == $flote->loteId)
                                <option value="{{ $flote->loteId }}" selected>{!! str_pad($flote->loteId, 2, '0', STR_PAD_LEFT).';&nbsp;&nbsp;'.$flote->lote !!}</option>
                             @else
                                <option value="{{ $flote->loteId }}" >{!! str_pad($flote->loteId, 2, '0', STR_PAD_LEFT).';&nbsp;&nbsp;'.$flote->lote !!}</option>
                             @endif
                          @endforeach
                          </select>
                       </td>

                       <td>
                          <div align="center">
                             <label for="foja"> Foja:</label>
                             <select id="foja" name="foja" maxlength="3" class="menor" onchange="this.form.submit()">
                              <option value="0">--Foja--</option>
                              @foreach($fojas as $fo)
                                 @if ($foja==$fo->foja)
                                    <option value="{{ $fo->foja }}" selected>{!! $fo->foja !!}</option>
                                 @else
                                    <option value="{{ $fo->foja }}"         >{!! $fo->foja !!}</option>
                                 @endif
                              @endforeach
                             </select>
                          </div>
                       </td>
                       {{ Form::close()}}
                       {{-- <td><div align="right"><input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar" value="Solicitar"/></div></td> --}}
                    </tr>
            </table>
         </div><br>
         @include('errors/flash-message')
             {!! csrf_field() !!}
             {!! $acordeon !!}
             <div id="resultado"></div>
         </form>
      @else
         <br><br>
         <div class="alert alert-danger alert-block detalles_info">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>No hay solicitudes enviadas.</strong>
         </div>
      @endif
</div>
<br><br>
@endsection
@section('animaciones')
   <script  src="{{ asset('js/datepickerBlock.js') }}" ></script>
@endsection
