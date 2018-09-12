@extends('layouts.app')
@section('title','CONDOC | '.$title)
@section('location')
    <div>
    	<p id="navegacion">
            {{-- <a href="{{ route('admin_dashboard') }}"><i class="fa fa-home" style="font-size:28px"></i></a> --}}
    		<span> >> </span>
    		<a> Administración </a>
            <span> >> </span>
    		<a href="#"> {{$title}} </a> </p>
    </div>
@endsection
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 id="titulo">{{$title.": ".$lists->count()}}</h2>
        {{-- <p class="button">
            <a href="{{ route('admin/usuarios/nuevo') }}" class="btn btn-primary">Nuevo Usuario</a>
        </p> --}}
    </div>
    @if($lists->isNotEmpty())
    <table class="table table-hover">
        <thead class="thead-dark">
            <tr>
                <th class="center" scope="col"># Solicitud</th>
                <th class="center" scope="col">No. Cuenta</th>
                <th class="center" scope="col">Nombre</th>
                <th class="center" scope="col">Nivel</th>
                <th class="center" scope="col">Cve Carrera</th>
                <th class="center" scope="col">Estatus</th>
                <th class="center" scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lists as $list)
                <tr>
                    <th scope="row">{!! $list->id !!}</th>
                    <td>{!! $list->cuenta !!}</td>
                    <td>{!! $list->nombre_compl !!}</td>
                    <td>{!! $list->nivel !!}</td>
                    <td>{!! $list->cve_carrera !!}</td>
                    <td>
                        {{-- <form action="{{ route('eliminar_usuario',[ $user ]) }}" method="POST">
                            {{ csrf_field() }}
                            {{ method_field('DELETE')}}
                            <a href="{{ route('ver_usuario',[ $user ]) }}"><i class="fa fa-eye" style="font-size:24px;color:#c5911f; padding: 0px 10px 0px 0px;"></i></a>
                            <a href="{{ route('admin.users.editar_usuarios',[ $user ]) }}"><i class="fa fa-edit" style="font-size:24px;color:#c5911f; padding: 0px 10px 0px 0px;"></i></a>
                            <button type="submit"><i class="fa fa-trash" style="font-size:24px;color:#c5911f; padding: 0px 6px 0px 0px;"></i></button>
                        </form> --}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


    @else
        <p>
            No hay Solcitudes registrados.
        </p>
    @endif
</div>
<div class="paginador">
    {{ $lists->links()}}
</div>
@endsection
