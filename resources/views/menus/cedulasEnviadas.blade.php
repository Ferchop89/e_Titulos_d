@extends('layouts.app')
@section('title','CONDOC | '.$title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-end mb-3">
      <br>
        <h2 id="titulo">{{$title." : ".count($lotesDgp) }}</h2>
        <div class="loader"></div>
    </div>
    @include('errors/flash-message')
    @if($lotesDgp->isNotEmpty())
    <table class="table table-hover">
        <thead class="thead-dark">
            <tr>
                <th class="center" scope="col">Lote</th>
                <th class="center" scope="col">Fecha de Envio</th>
                <th class="center" scope="col">Contiene</th>
                <th class="center" scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lotesDgp as $key => $loteDgp)
                <tr>
                    <td class="center" >{{$loteDgp->lote_dgp}}</td>
                    <td class="center" >{{ $loteDgp->fecha_carga }}</td>
                    <td class="center" >{{ $contenido[$loteDgp->lote_dgp] }}</td>
                    {{-- <td>{{ $loteDgp->email }}</td> --}}
                    <td class="center" >
                        <form action="{{ route('home') }}" method="POST">
                            {{ csrf_field() }}
                            {{ method_field('DELETE')}}
                            {{-- <a href="{{ route('ver_usuario',[  ]) }}"><i class="fa fa-eye" style="font-size:24px;color:#c5911f; padding: 0px 10px 0px 0px;"></i></a> --}}
                            <a class="center" href="{{ route('descargaXls', $loteDgp->lote_dgp) }}"><i class="fa fa-download" style="font-size:24px;color:#c5911f; padding: 0px 10px 0px 0px;"></i></a>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


@else
   <br><br>
   <div class="alert alert-danger alert-block detalles_info center">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>No hay registros pendientes.</strong>
   </div>
@endif
</div>
<div class="paginador">
    {{ $lotesDgp->links()}}
</div>
@endsection
