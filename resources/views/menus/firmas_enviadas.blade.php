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
  <div class="d-flex justify-content-between align-items-end mb-3">
    <br>
      <h2 id="titulo">{{$title.": ".$total}}</h2>
    </div>
    <form class="form-group solicitud_f" method="POST" action="{{ route( 'registroTitulos/firmas_busqueda/seleccion' ) }}">
        {!! csrf_field() !!}
      <div class="filtros">
         <table style="width:100%">
            <tr class="filtros">
              {{ Form::open() }}
               <td>
                  <div align="center">
                     <label for="fecha"> Emisión de título: </label>
                     <input id="fecha" type="date" name="fecha" style="width:40%;">
                     <!-- <span>
                        <input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar_fecha" value="Solicitar"/>
                     </span> -->
                  </div>
               </td>
               <td>
                  <div align="center">
                     <label for="libro"> Libro: </label>
                     <select id="libro" name="libro" maxlength="4" class="menor">
                       <option value="0">--Libro--</option>
                       @foreach($libros as $lib)
						      				<option value="{{ $lib->libro }}">{!! $lib->libro !!}</option>
						      	   @endforeach
                     </select>
                     <!-- <span>
                        <input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar_libro" value="Solicitar"/>
                     </span> -->
                  </div>
               </td>
               {{ Form::close()}}
               <td>
                  <div align="center">
                     <label for="foja"> Foja: </label>
                     <select id="foja" name="foja" maxlength="3" class="menor">
                       <option value="0">--Foja--</option>
                       @foreach($fojas as $fo)
						      				<option value="{{ $fo->foja }}">{!! $fo->foja !!}</option>
						      	   @endforeach
                     </select>
                     <!-- <span>
                        <input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar_foja" value="Solicitar"/>
                     </span> -->
                  </div>
               </td>
               <td>
                  <div align="center">
                     <label for="lote_s"> Lote: </label>
                     <input id="lote_s" type="date" name="lote_s" style="width: 50%;">
                     <!-- <span>
                        <input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar_lote" value="Solicitar"/>
                     </span> -->
                  </div>
               </td>
               <td><div align="right"><input type="submit" class="btn btn-primary waves-effect waves-light" name="consultar" value="Solicitar"/></div></td>
            </tr>
    </table>
  </div><br>
  @include('errors/flash-message')
  @if(count($lote)>0)
 {!! csrf_field() !!}
 {!! $acordeon !!}
 <div id="resultado"></div>
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
