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
      <h2 id="titulo">{{$title.": ".$total}}</h2>
      </div>
      @if(!empty($lotes))
         <form class="form-group solicitud_f" method="POST" action="{{ route( 'registroTitulos/firmas_busqueda/seleccion' ) }}">
             {!! csrf_field() !!}
              <div class="filtros">
                <table style="width:100%">
                    <tr class="filtros">
                      {{ Form::open() }}
                       {{-- <td>
                          <div align="center">
                             <label for="fecha"> Emisión de título: </label>
                             <input id="fecha" type="date" name="fecha" style="width:40%;">
                             <!-- <span>
                                <input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar_fecha" value="Solicitar"/>
                             </span> -->
                          </div>
                       </td> --}}
                       <td class="td-datepicker">
                          <div class="form-group">
                             <label for="datepicker" class="datepicker">Emisión de título:</label>
                             <div class="input-group idatepicker">
                                {{-- <input name = "fecha" type="text" id="datepicker" class="form-control" value =""> --}}
                                <input name = "fecha" type="text" id="datepicker" class="form-control" value ="{{$fechaO}}">
                                <span class="input-group-addon">
                                   <i class="fa fa-search"></i>
                                </span>
                             </div>
                             {{-- {{ Form::text('datepicker','',array('id'=>'datepicker','readonly', 'class' => '')) }} --}}
                          </div>
                       </td>
                       <td>
                          <div align="center">
                             <h3>
                                <a>Libro: {{substr($fechaO,6,4)}}</a>
                             </h3>
                             <!-- <span>
                                <input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar_libro" value="Solicitar"/>
                             </span> -->
                          </div>
                       </td>
                       <td>
                          <label for="lotes"> Lote</label>
                          <select id="lotes" name="lotes" maxlength="4" class="menor" onchange="this.form.submit()">
                          @if ($lote=='')
                             <option value="0" selected>--Lote--</option>
                          @else
                             <option value="0">--Lote--</option>
                          @endif
                          @foreach($lotes as $flote)
                             @if ($lote == $flote)
                                <option value="{{ $flote->fecha_lote }}" selected>{!! $flote->fecha_lote !!}</option>
                             @else
                                <option value="{{ $flote->fecha_lote }}" >{!! $flote->fecha_lote !!}</option>
                             @endif
                          @endforeach
                          </select>
                       </td>

                       <td>
                          <div align="center">
                             <label for="foja"> Foja: </label>
                             <select id="foja" name="foja" maxlength="3" class="menor">
                              @if ($foja=='')
                                 <option value="0" selected>--Foja--</option>
                              @else
                                 <option value="0">--Foja--</option>
                              @endif
                              @foreach($fojas as $fo)
                                 @if ($foja==$fo)
                                    <option value="{{ $fo->foja }}" selected>{!! $fo->foja !!}</option>
                                 @else
                                    <option value="{{ $fo->foja }}">{!! $fo->foja !!}</option>
                                 @endif
                              @endforeach
                             </select>
                             <!-- <span>
                                <input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar_foja" value="Solicitar"/>
                             </span> -->
                          </div>
                       </td>

                       {{ Form::close()}}

                       {{-- <td>
                          <div align="center">
                             <label for="lote_s"> Lote: </label>
                             <input id="lote_s" type="date" name="lote_s" style="width: 50%;">
                             <!-- <span>
                                <input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar_lote" value="Solicitar"/>
                             </span> -->
                          </div>
                       </td> --}}
                       <td><div align="right"><input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar" value="Solicitar"/></div></td>
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
   <!-- Según la fecha seleccionada, será el libro a elegir -->
   <script>
    var options="";
    $("#fecha").on('change',function(){
        var date=$(this).val();
        var value=date.substr(0,4);
        //alert(value);
        if(value!=""){
          options="<option value='"+value+"'>"+value+"</option>";
          $("#libro").html(options);
        }
        else{
          var options_def="";
          var libros=<?php echo json_encode($libros); ?>; //Libros
          options_def += "<option value='0'>--Libro--</option>";
          libros.forEach(function(element) {
            options_def += "<option value='"+element.libro+"'>"+element.libro+"</option>";
          });
          $("#libro").html(options_def);
        }
    });
  </script>
  <!-- Según el libro seleccionad, se podrá elegir la foja -->
  <script>
   var options="";
   $("#libro").on('change',function(){
       var value=$(this).val();
       if(value!="0"){
         var fojas_sel=<?php echo json_encode($fojas_sel); ?>; //Fojas por libro [OPTIMIZAR]
         options_def += "<option value='0'>--Foja--</option>";
         fojas_sel.forEach(function(element) {
           if(element.substr(0,4) == value){
            options_def += "<option value='"+element.substr(5)+"'>"+element.substr(5)+"</option>";
           }
         });
         $("#foja").html(options_def);
       }
       else{
         var options_def="";
         var fojas=<?php echo json_encode($fojas); ?>; //Fojas
         options_def += "<option value='0'>--Foja--</option>";
         fojas.forEach(function(element) {
           options_def += "<option value='"+element.foja+"'>"+element.foja+"</option>";
         });
         $("#foja").html(options_def);
       }
   });
 </script>
@endsection
