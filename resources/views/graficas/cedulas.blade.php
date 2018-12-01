@extends('layouts.app')
@section('title', 'CEDULAS | '.$title)
{{-- @section('location')
    <div>
    	<p id="navegacion">
            <a href="{{ route('home') }}"><i class="fa fa-home" style="font-size:28px"></i></a>
            <span> >> </span>
        	<a> Reportes</a>
            <span> >> </span>
    		<a href="#"> {{$title}} </a> </p>
    </div>
@endsection --}}
@section('estilos')
    <link href="{{ asset('css/listados.css') }}" rel="stylesheet">
    <link href="{{ asset('css/graficasTable.css') }}" rel="stylesheet">
@endsection
@section('content')
   <h2 id="titulo">{{$title}}</h2>
   @if (count($data)==0)
      <br><br>
      <div class="alert alert-danger alert-block detalles_info center" >
         <button type="button" class="close" data-dismiss="alert">×</button>
         <strong>Sin información.</strong>
      </div>
   @else
   <div class="capsule graficas">
      <div class="filtros">
         {!! Form::open(['class'=>'form','method'=>'GET','id'=>'Sol_Cit', 'route'=>'registroTitulos/cedulasG']) !!}
            <div class="fil anio">
                  <label class="inline" for="filtro">Año:</label>
                  {!! Form::select('anio_id',$a,$aSel, ['class' => 'anio']) !!}
            </div>
            <div class="fil mes">
               <label class="inline" for="filtro">Mes:</label>
               {{-- {!! Form::select('mes_id',$m,$mSel) !!} --}}
               {!! $mesHtml !!}
            </div>
         {!! Form::close() !!}
      </div>
      <div class="graficos">
         <div class="graf-left">
            {!! $chart1->render() !!}
         </div>
      </div>
      <div class="resumen">

         <div class="divTable">
            <div class="divTableBody">
               <div class="divTableRow">
                  <div class="divTableCell"><strong>Día</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{$key}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>Total</strong></div>
               </div>
               <div class="divTableRow">
                  <div class="divTableCell"><strong>Títulos</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell">{{$value['Titulos']}}</div>
                  @endforeach
                  <div class="divTableCell">{{$totales['Titulos']}}</div>
               </div>
               {{-- Si no existen pendientes en todo el mes, no se despliega el rubro --}}
               @if ($pendientesHTML!='')
                  <a class= 'a-row' data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                  <div class="divTableRow">
                     <div class="divTableCell">
                        {{-- <a class= 'a-row' data-toggle="collapse" data-parent="#accordion" href="#collapse1"> --}}
                           <strong>
                              Pendientes
                           </strong>

                     </div>
                        @foreach ($data as $key => $value)
                           <div class="divTableCell">{{$value['Pendientes']}}</div>
                        @endforeach
                        <div class="divTableCell">{{$totales['Pendientes']}}</div>
                  </div>
                  {!! $pendientesHTML !!}
                  </a>
               @endif
               <a class= 'a-row' data-toggle="collapse" data-parent="#accordion" href="#collapse1">
               <div class="divTableRow">
                  <div class="divTableCell">
                     {{-- <a class= 'a-row' data-toggle="collapse" data-parent="#accordion" href="#collapse1"> --}}
                        <strong>
                           No Autorizadas C/Errores
                        </strong>

                  </div>
                     @foreach ($data as $key => $value)
                        <div class="divTableCell">{{$value['NoAutorConErr']}}</div>
                     @endforeach
                     <div class="divTableCell">{{$totales['NoAutorConErr']}}</div>
               </div>
               {!! $listaHtml !!}
               </a>
               <div class="divTableRow">
                  <div class="divTableCell"><strong>No Autorizadas S/Errores</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell">{{$value['NoAutorSinErr']}}</div>
                  @endforeach
                  <div class="divTableCell">{{$totales['NoAutorSinErr']}}</div>
               </div>
               <div class="divTableRow">
                  <div class="divTableCell"><strong>Autorizadas</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell">{{$value['Autorizadas']}}</div>
                  @endforeach
                  <div class="divTableCell">{{$totales['Autorizadas']}}</div>
               </div>
               <div class="divTableRow">
                  <div class="divTableCell"><strong>En Firma</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell">{{$value['EnFirma']}}</div>
                  @endforeach
                  <div class="divTableCell">{{$totales['EnFirma']}}</div>
               </div>
               <div class="divTableRow">
                  <div class="divTableCell"><strong>No enviadas</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell">{{$value['NoEnviadas']}}</div>
                  @endforeach
                  <div class="divTableCell">{{$totales['NoEnviadas']}}</div>
               </div>
               <div class="divTableRow">
                  <div class="divTableCell"><strong>Enviadas</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell">{{$value['Enviadas']}}</div>
                  @endforeach
                  <div class="divTableCell">{{$totales['Enviadas']}}</div>
               </div>
            </div>
         </div>
      </div>
   </div>

   @endif
@endsection
@section('animaciones')
    <script type="text/JavaScript" src="{{ asset('js/block.js') }}" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link href="{{asset('css/select2.css')}}" rel="stylesheet" />
    <script src="{{asset('js/select2.js')}}"></script>
    {{-- <script src="{{asset('js/graf_height.js')}}"></script> --}}
    <script type="text/javascript">
        $(document).ready(function(){
          $('select').select2();
          $('#Sol_Cit select').change(function(){
            $('#Sol_Cit').submit();
          });
        });
    </script>
@endsection
