@extends('layouts.app')
@section('title')
    CONDOC | @yield('esp')
@endsection
@section('location')
    <div>
        <p id="navegacion">
            <a href="{{ route('home') }}"><i class="fa fa-home" style="font-size:28px"></i></a>
            <a href="#"><span> >> </span>
            <span> </span> Licenciatura </a> >>
            {{-- <a href="#"> {{$title}} </a> </p> --}}
    </div>
@endsection
@section('estilos')
    @yield('sub-estilos')
@endsection
@section('content')
   {{-- {{dd($user)}} --}}
   {{-- <h2 id="titulo">{{$title}}</h2> --}}
   <div id="is" class="container">
      <div class="panel panel-default">
          {{-- <div class="panel-heading">@yield('esp')</div> --}}
         <div class="panel-body">
            {{-- <pre> --}}
               <label for="">Cadena Enviada:</label>
               {{$datos}}
            {{-- </pre> --}}
            <br /><label for="">CURP:</label>
            {{$curp}}
            <br /><label for="">URL:</label>
            {{$url}}
            <form name="firma" id="firma" target="frameFEA" action="https://enigma.unam.mx/componentefirma/initSigningProcess" method="POST">
               <input type="hidden" name="datos" value="{{$datos}}">
               <input type="hidden" name="URL" value="{{$url}}">
               <input type="hidden" name="curp" value="{{$curp}}">
               <button type="submit" id="btnFirma" class="btn btn-primary">Firmar</button>
            </form>
          </div>
      </div>
   </div>
   @yield('errores')
   <div class="capsule informacion-alumno">
      @yield('identidadAlumno')
   </div>
   <div class="solicitudes">
      @yield('info-alumno')
   </div>
@endsection
