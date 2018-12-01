@extends('layouts.app')
@section('title')
    CONDOC | @yield('esp')
@endsection
@section('location')
    {{-- <div>
        <p id="navegacion">
            <a href="{{ route('home') }}"><i class="fa fa-home" style="font-size:28px"></i></a>
            <a href="#"><span> >> </span>
            <span> </span>  </a> >>
            <a href="#"> {{$title}} </a> </p>
    </div> --}}
@endsection
@section('estilos')
   @section('estilos')
   	<link href="{{ asset('css/loading.css') }}" rel="stylesheet">
      <link href="{{ asset('css/solicitudesPendientes.css') }}" rel="stylesheet">
      <link href="{{ asset('css/colordate.css') }}" rel="stylesheet">
   @endsection
@yield('sub-estilos')
@endsection
@section('content')
    <br>
    <h2 id="titulo">{{$title}}</h2>
    <div class="loader"></div>
    <div id="is" class="container viewFecha">
            <div class="panel panel-default">
                <div class="panel-body">

                    @yield('ruta')
                        {!! csrf_field() !!}
                        {{-- <label for="fecha"> Seleccione una fecha: </label> --}}
                        @if(isset($fecha))
                           <input id="fecha" type="date" name="fecha" value="{{$fecha}}">
                           {{-- <div class="td-datepicker">
                              <div class="form-group">
                                 <label for="datepicker" class="datepicker">Emisión de título:</label>
                                 <div class="input-group idatepicker">
                                    <input name = "datepicker" type="text" id="datepicker" class="form-control" value ={{$fecha_d}}>
                                    <span class="input-group-addon">
                                       <i class="fa fa-search"></i>
                                    </span>
                                 </div>
                              </div>
                           </div> --}}
                        @else
                            {{-- <input id="num_cta" type="text" name="num_cta" maxlength="9" /> --}}
                            {{-- <input id="fecha" type="date" name="fecha"> --}}
                            <div class="div-datepicker">
                               <div class="form-group">
                                  <label for="datepicker" class="datepicker">Seleccione una fecha:</label>
                                  <div class="input-group idatepicker">
                                     <input name = "datepicker" type="text" id="datepicker" class="form-control">
                                     {{-- <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                     </span> --}}
                                  </div>
                               </div>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div id="error" class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="btn-derecha">
                            <button type="submit" class="btn btn-primary waves-effect waves-light" name="submit" value="consultar">
                                Solicitar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
</div>
<div class="detalles_info">@include('errors/flash-message')</div>
@endsection
@section('animaciones')
   <script  src="{{ asset('js/datepickerBlockDouble.js') }}" ></script>
   <script src="{{asset('js/loading.js')}}"></script>
@endsection
