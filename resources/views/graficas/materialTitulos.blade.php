@extends('layouts.app')
@section('title', 'Resumen | '.$title)
@section('estilos')
    <link href="{{ asset('css/listados.css') }}" rel="stylesheet">
    <link href="{{ asset('css/graficasTable.css') }}" rel="stylesheet">
@endsection
@section('content')
   @if ($resumenHTML=='')
      <br><br>
      <div class="alert alert-danger alert-block detalles_info center" >
         <button type="button" class="close" data-dismiss="alert">×</button>
         <strong>Sin Información en este periodo</strong>
      </div>
   @else
      <h2>RESUMEN TITULOS Y MATERIALES DE ELABORACIÓN</h2>
      @if ($errors->any())
          <div class="alert alert-danger">
             <h5>Información faltante</h5>
             <ul>
                  @foreach ($errors->all() as $error)
                      <li> {{ $error}}  </li>
                  @endforeach
             </ul>
          </div>
      @endif
      {!! Form::open(['class'=>'form','method'=>'POST','id'=>'materiales', 'route'=>'/materialesInforme']) !!}
            <div>

            </div>

            <table class="table">
              <thead>
                <tr>
                  <th scope="col">Periodo</th>
                  <th scope="col">Orden</th>
                  <th scope="col">Consulta</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                     <strong>inicio: </strong><input id="inicio" type="date" name="inicio" value="{{$inicio}}">
                     <strong>fin: </strong><input id="fin" type="date" name="fin" value="{{$fin}}">
                  </td>
                  <td>
                     {!! $ordenHTML !!}
                  </td>
                  <td>
                     <button  class="btn btn-primary waves-effect waves-light" type="submit" form="materiales" value="Submit">Consultar</button></button>
                  </td>
                </tr>
              </tbody>
            </table>


      {!! Form::close() !!}
      <pre>
      {!! $resumenHTML !!}
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

    <script type="text/javascript">
        $(document).ready(function(){

          $('select').select2();
          $('#Sol_Cit select').change(function(){
            $('#Sol_Cit').submit();
          });
          // $('#example').DataTable();
        } );

    </script>
@endsection
