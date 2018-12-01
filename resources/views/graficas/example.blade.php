@extends('layouts.app')
@section('title', 'CONDOC | '.$title)
@section('location')
    <div>
    	<p id="navegacion">
            <a href="{{ route('home') }}"><i class="fa fa-home" style="font-size:28px"></i></a>
            <span> >> </span>
        	<a> Reportes</a>
            <span> >> </span>
    		<a href="#"> {{$title}} </a> </p>
    </div>
@endsection
@section('estilos')
    <link href="{{ asset('css/listados.css') }}" rel="stylesheet">
@endsection
@section('content')
    <div class="capsule graficas">
        <h2 id="titulo">{{$title}}</h2>
        <h2>{{$procedencia}}</h2>
        <h3>{{$mes}}: {{$anio}}</h3>

        {{-- <div style="width:50%;">
            {!! $chart1->render() !!} {!! $chart2->render() !!}
        </div> --}}


        <div style="float: left; width: 48%">
            {!! $chart2->render() !!}
        </div>
        <div style="float: left; width: 48%">
            {!! $chart1->render() !!}
        </div>
    </div>
@endsection
@section('animaciones')
    <script type="text/JavaScript" src="{{ asset('js/block.js') }}" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js" type="text/javascript"></script>
@endsection
