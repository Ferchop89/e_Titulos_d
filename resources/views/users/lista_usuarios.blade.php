@extends('layouts.app')
@section('title','Usuarios')
@section('estilos')
    <link href="{{ asset('css/solicitudesPendientes.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">{{ $title }}</h1>
        <p>
            <a href="{{ route('users.crear_usuario') }}" class="btn btn-primary">Nuevo Usuario</a>
        </p>
    </div>
    @if($users->isNotEmpty())
    <table class="table table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre</th>
                <th scope="col">Alias</th>
                <th scope="col">Correo</th>
                <th scope="col">Procede</th>
                <th scope="col">Activo</th>
                <th scope="col">Role</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <th scope="row">{{ $user->id}}</th>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>

                        @if($user->procedencia_id != null)
                            <td>{{ App\Models\Procedencia::where('id',$user->procedencia_id)->pluck('procedencia')[0] }}</td>
                        @else
                            <td>------------</td>
                        @endif

                    <td><input type="checkbox" {{ $user->is_active ? 'checked' : ''   }} name="activo" OnClick="return false;" ></td>
                    <td>
                        @foreach($user->roles()->where('user_id',$user->id)->orderBy('role_id', 'asc')->get() as $roles)
                            /{{ $roles->nombre }}
                        @endforeach
                    </td>
                    <td>
                        <form action="{{ route('users.destroy',[ $user ]) }}" method="POST">
                            {{ csrf_field() }}
                            {{ method_field('DELETE')}}
                            <a href="{{ route('users.ver_usuario',[ $user ]) }}"><i class="fa fa-eye" style="font-size:34px;color:#c5911f"></i></a>
                            <a href="{{ route('users.editar_usuarios',[ $user ]) }}"><i class="fa fa-edit" style="font-size:34px;color:#c5911f"></i></a>
                            <button type="submit"><i class="fa fa-trash" style="font-size:34px;color:#c5911f"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links()}}

    @else
        <p>
            No hay usuarios registrados.
        </p>
    @endif
</div>
@endsection

{{-- // barra lateral --}}
{{-- @section('barralateral')
  <h2>Barra lateral personalizada</h2>
  @parent
@endsection --}}
