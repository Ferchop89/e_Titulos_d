@extends('layouts.app')
@section('content')

@if ($solW_cta>0)
  <div class="container">
    <h4>REVISIONES PENDIENTES DE LISTADO</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <h5>Por favor corrige los siguientes debajo</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li> {{ $error}}  </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="corte" method="POST" action="{{ url("creaListas") }}">
        {{ method_field('PUT') }}
        {!! csrf_field() !!}

        <dir class="form-group">
          <div>
              <label for="facultad">Procedencia de Solicitud</label>
              {!! $esc_Html !!}
          </div>
        </dir>

        <div class="form-group">
          <div>
              <label for="cuenta">Número de Cuenta</label>
              <input type='text' name="cuenta" id='search' placeholder='Número de Cuenta'>
          </div>
        </div>

        <div class="form-group">
          <div>
              <label for="lista">Listados a generar</label>
              <input type='text' name="lista" value="{{old('lista')}}" id='lista' size="2">
          </div>
        </div>
        {{-- <a href="{{ route('cortes') }}" class="btn btn-primary">filtrar</a> --}}
        <button type="submit" class="btn btn-primary">Generar Listados</button>
        {!!$sol_Html!!}
    </form>
  </div>
@else
  <div class="form-group">
    <br><br>
    <h4 class="text-center">REVISIONES PENDIENTES DE LISTADO</h4>
    <br><br>
    <h3 class="text-center">No existen revisiones pendientes</h3>
    <br><br>
  </div>

@endif
@endsection
