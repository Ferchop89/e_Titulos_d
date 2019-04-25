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
                  <div class="divTableCell"><strong>Títulos Emitidos</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{$value['Titulos']}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>{{$totales['Titulos']}}</strong></div>
               </div>

               {{-- Si no existen pendientes en todo el mes, no se despliega el rubro --}}
               @if ($pendientesHTML!='')
                  <a class= 'a-row' data-toggle="collapse" data-parent="#accordion" href="#collapse3">
                  <div class="divTableRow">
                     <div class="divTableCell">
                        {{-- <a class= 'a-row' data-toggle="collapse" data-parent="#accordion" href="#collapse1"> --}}
                           <strong>
                              Pendientes
                           </strong>
                     </div>
                        @foreach ($data as $key => $value)
                           <div class="divTableCell"><strong>{{($value['Pendientes']==0)? "": $value['Pendientes']}}</strong></div>
                        @endforeach
                        <div class="divTableCell"><strong>{{$totales['Pendientes']}}</strong></div>
                  </div>
                  {!! $pendientesHTML !!}
                  </a>
               @endif

               <div class="divTableRow">
                  <div class="divTableCell">
                     <strong>
                        Autorizaciones ATI
                     </strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{($value['AutorizaAlumno']==0)? "": $value['AutorizaAlumno']}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>{{$totales['AutorizaAlumno']}}</strong></div>
               </div>

               <div class="divTableRow">
                  <div class="divTableCell">
                     <strong>
                        <p style="padding-left: 5%;">Autorizaciones Sin/Errores</p>
                     </strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{($value['AutorizaAlumnoSE']==0)? "": $value['AutorizaAlumnoSE']}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>{{$totales['AutorizaAlumnoSE']}}</strong></div>
               </div>

               @if ($listaHtml1!='')
                  <a class= 'a-row' data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                  <div class="divTableRow">
                     <div class="divTableCell">
                        {{-- <a class= 'a-row' data-toggle="collapse" data-parent="#accordion" href="#collapse1"> --}}
                           <strong>
                              <p style="padding-left: 5%;">Autorizaciones Con/Errores</p>
                           </strong>
                     </div>
                        @foreach ($data as $key => $value)
                           <div class="divTableCell"><strong>{{($value['AutorizaAlumnoCE']==0)? "": $value['AutorizaAlumnoCE']}}</strong></div>
                        @endforeach
                        <div class="divTableCell"><strong>{{$totales['AutorizaAlumnoCE']}}</strong></div>
                  </div>
                  {!! $listaHtml1 !!}
                  </a>
               @endif

               <div class="divTableRow">
                  <div class="divTableCell">
                     <strong>
                        No Autorizaciones ATI
                     </strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{($value['NoAutorizaAlumno']==0)? "": $value['NoAutorizaAlumno']}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>{{$totales['NoAutorizaAlumno']}}</strong></div>
               </div>

               <div class="divTableRow">
                  <div class="divTableCell">
                     <strong>
                        <p style="padding-left: 5%;">No Autorizaciones Sin/Error</p>
                     </strong>
                  </div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{($value['NoAutorizaAlumnoSE']==0)? "": $value['NoAutorizaAlumnoSE']}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>{{$totales['NoAutorizaAlumnoSE']}}</strong></div>
               </div>

               @if ($listaHtml!='')
                  <a class= 'a-row' data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                  <div class="divTableRow">
                     <div class="divTableCell">
                        {{-- <a class= 'a-row' data-toggle="collapse" data-parent="#accordion" href="#collapse1"> --}}
                           <strong>
                              <p style="padding-left: 5%;">No Autorizaciones Con/Error</p>
                           </strong>
                     </div>
                        @foreach ($data as $key => $value)
                           <div class="divTableCell"><strong>{{($value['NoAutorizaAlumnoCE']==0)? "": $value['NoAutorizaAlumnoCE']}}</strong></div>
                        @endforeach
                        <div class="divTableCell"><strong>{{$totales['NoAutorizaAlumnoCE']}}</strong></div>
                  </div>
                  {!! $listaHtml !!}
                  </a>
               @endif

               <div class="divTableRow">
                  <div class="divTableCell"><strong>Revisados por JUD</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{($value['RevisadasJtit']==0)? "": $value['RevisadasJtit']}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>{{$totales['RevisadasJtit']}}</strong></div>
               </div>

               <div class="divTableRow">
                  <div class="divTableCell"><strong>Autorizadas por JUD</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{($value['AutorizadasJtit']==0)? "": $value['AutorizadasJtit']}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>{{$totales['AutorizadasJtit']}}</strong></div>
               </div>

               <div class="divTableRow">
                  <div class="divTableCell"><strong>Firmadas Dir.Gral. </strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{($value['FirmadasDG']==0)? "": $value['FirmadasDG']}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>{{$totales['FirmadasDG']}}</strong></div>
               </div>

               <div class="divTableRow">
                  <div class="divTableCell"><strong>Enviados a DGP</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{($value['EnviadasDGP']==0)? "": $value['EnviadasDGP']}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>{{$totales['EnviadasDGP']}}</strong></div>
               </div>

               <div class="divTableRow">
                  <div class="divTableCell"><strong>Títulos Electrónicos Rechazados</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{($value['TER']==0)? "": $value['TER']}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>{{$totales['TER']}}</strong></div>
               </div>

               <div class="divTableRow">
                  <div class="divTableCell"><strong>Títulos Electrónicos Aprobados</strong></div>
                  @foreach ($data as $key => $value)
                     <div class="divTableCell"><strong>{{($value['TEA']==0)? "": $value['TEA']}}</strong></div>
                  @endforeach
                  <div class="divTableCell"><strong>{{$totales['TEA']}}</strong></div>
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
