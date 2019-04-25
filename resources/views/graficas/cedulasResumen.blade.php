@extends('layouts.app')
@section('title', 'Resumen | '.$title)
@section('estilos')
    <link href="{{ asset('css/listados.css') }}" rel="stylesheet">
    <link href="{{ asset('css/graficasTable.css') }}" rel="stylesheet">
@endsection
@section('content')
   @if (count($data)==0)
      <br><br>
      <div class="alert alert-danger alert-block detalles_info center" >
         <button type="button" class="close" data-dismiss="alert">×</button>
         <strong>Sin información.</strong>
      </div>
   @else

      <h1>RESUMEN PROCESAMIENTO Y ENVÍO DE CÉDULAS ELECTRÓNICAS</h1>
      <h1>PERIODO: {{substr($limites['inicio'],0,10)}} AL {{substr($limites['fin'],0,10)}}</h1>
      <hr style="border-top:3px solid #000">
      <h2>Titulos Emitidos: {{$data['Titulos']}}</h2>
      <h2>Autorizaciones ATI: {{$data['AutorizaAlumno']}}</h2>
      <h2 style="margin-left:2em">Autorizadas ATI S/Errores: {{$data['AutorizaAlumnoSE']}}</h2>
      <h2 style="margin-left:3em">Titulos Electrónicos Rechazados: {{$data['TER']}}</h2>
      <h2 style="margin-left:3em">Títulos Electrónicos Aprobados: {{$data['TEA']}}</h2>
      <h2 style="margin-left:2em">Autorizadas ATI C/Errores: {{$data['AutorizaAlumnoCE']}}</h2>
      <h2>Sin Autorización ATI: {{$data['NoAutorizaAlumno']}}</h2>
      <h2 style="margin-left:2em">Sin Autorización ATI S/Errores: {{$data['NoAutorizaAlumnoSE']}}</h2>
      <h2 style="margin-left:2em">Sin Autorización ATI C/Errores: {{$data['NoAutorizaAlumnoCE']}}</h2>
      <hr style="border-top:3px solid #000">
      <h2>Revisadas JUD Titulos: {{$data['RevisadasJtit']}}</h2>
      <h2>Autorizadas JUD Tïtulos: {{$data['AutorizadasJtit']}}</h2>
      <h2>Firmadas por la Dir. Gral.: {{$data['FirmadasDG']}}</h2>
      <h2>Títulos Enviados a la DGP: {{$data['EnviadasDGP']}}</h2>
      <h2>Títulos Descargados de la DGP: {{$data['DescargaDGP']}}</h2>
      <hr style="border-top:3px solid #000">
    ]

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
