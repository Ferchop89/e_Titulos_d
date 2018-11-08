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
@endsection
@section('content')
    <div class="capsule graficas">
        <h2 id="titulo">{{$title}}</h2>
        <div class="filtros">
            {!! Form::open(['class'=>'form','method'=>'GET','id'=>'Sol_Cit', 'route'=>'cedulasG']) !!}
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
            {{-- <div class="graf-right">
                {!! $chart2->render() !!}
            </div> --}}
        </div>


        <div class="resumen">
        <table class="table table-hover">
          <tr>
            <td class="row_table" nowrap>Día</td>
              @foreach ($data as $key => $value)
               <td class="tab_num"><strong>{{$key}}</strong></td>
              @endforeach
            <td class="row_table" nowrap>Total</td>
          </tr>
          <tr>
            <td class="row_table" nowrap>Títulos</td>
              @foreach ($data as $key => $value)
               <td class="tab_num">{{$value['Titulos']}}</td>
              @endforeach
              <td>{{$totales['Titulos']}}</td>
          </tr>
          <tr>
            <td class="row_table" nowrap>Pendientes</td>
              @foreach ($data as $key => $value)
               <td class="tab_num">{{$value['Pendientes']}}</td>
              @endforeach
              <td>{{$totales['Pendientes']}}</td>
          </tr>
          <tr>
            <td class="row_table" nowrap>No Autorizadas C/E</td>
              @foreach ($data as $key => $value)
               <td class="tab_num">{{$value['NoAutorConErr']}}</td>
              @endforeach
              <td>{{$totales['NoAutorConErr']}}</td>
          </tr>
          <tr>
            <td class="row_table" nowrap>No Autorizadas S/E</td>
              @foreach ($data as $key => $value)
               <td class="tab_num">{{$value['NoAutorSinErr']}}</td>
              @endforeach
              <td>{{$totales['NoAutorSinErr']}}</td>
          </tr>
          <tr>
            <td class="row_table" nowrap>Autorizadas</td>
              @foreach ($data as $key => $value)
               <td class="tab_num">{{$value['Autorizadas']}}</td>
              @endforeach
              <td>{{$totales['Autorizadas']}}</td>
          </tr>
          <tr>
            <td class="row_table" nowrap>En Firma</td>
              @foreach ($data as $key => $value)
               <td class="tab_num">{{$value['EnFirma']}}</td>
              @endforeach
              <td>{{$totales['EnFirma']}}</td>
          </tr>
          <tr>
            <td class="row_table" nowrap>NoEnviadas</td>
              @foreach ($data as $key => $value)
               <td class="tab_num">{{$value['NoEnviadas']}}</td>
              @endforeach
              <td>{{$totales['NoEnviadas']}}</td>
          </tr>
          <tr>
            <td class="row_table" nowrap>Enviadas</td>
              @foreach ($data as $key => $value)
               <td class="tab_num">{{$value['Enviadas']}}</td>
              @endforeach
              <td>{{$totales['Enviadas']}}</td>
          </tr>
        </table>

        </div>

        {{-- <div style="float: left; width: 48%">
            {!! $chart2->render() !!}
        </div>
        <div style="float: left; width: 48%">
            {!! $chart1->render() !!}
        </div> --}}
    </div>
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
