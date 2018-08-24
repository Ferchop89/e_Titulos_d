@extends('layouts.app')

@section('title',"Editar usuario.")

@section('content')

<div class="container">
    <h1 class="pb-1">
        Editar Usuario
    </h1>
    <div class="card-body">
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

        <form method="POST" action="{{ url("usuarios/{$user->id}") }}">
            {{ method_field('PUT') }}
            {!! csrf_field() !!}

            <div class="form-group">
                <label for="name">Nombre:</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Nombre de usuario" value="{{ old('name',$user->name)}}"/>
            </div>
            <div class="form-group">
                <label for="username">Alias</label>
                <input type="text" class="form-control" name="username" id="usernamex" placeholder="mayor a seis caracteres" value="{{ old('username',$user->username)}}"/>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Usuario@net.com" value="{{ old('email',$user->email)}}"/>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="mayor a seis caracteres"/>
            </div>

            <div class="form-group">
                {{ Form::checkbox('is_active', null, $user->is_active ,['class' => 'form-control']) }}
                <label for="is_active">Usuario Activo</label>
            </div>

            {{-- $user->is_active ? 'Checked' : '' --}}

            <div>
                <label>Roles en el Sistema</label>
            </div>

            <div class="form-check form-check-inline">
                <input type="checkbox" {{ $user->roles()->where('nombre','Admin')->count()>0 ? 'Checked' : '' }} class="form-check-input" name="Admin" id="Admin" value="1">
                <label class="form-check-label" for="Admin">Admin</label>
            </div>

            <div class="form-check form-check-inline">
                <input type="checkbox" {{ $user->roles()->where('nombre','FacEsc')->count()>0 ? 'Checked' : '' }} class="filled-in form-check-input" name="FacEsc" id="FacEsc" value="2">
                <label class="form-check-label" for="FacEsc">FacEsc</label>
            </div>

            <div id = "xproc">
              @if ( $user->roles()->where('nombre','FacEsc')->count()>0 == 'Checked' )
                  {{  Form::select('procedencia_id',
                      App\Models\Procedencia::pluck('procedencia','id'),
                      ['id'=>$user->procedencia_id],['class'=>'form-group','placeholder'=>'Elige Procedencia'])}}
               @else
                   {{  Form::select('procedencia_id',
                       App\Models\Procedencia::pluck('procedencia','id'),
                       null,['class'=>'form-group','placeholder'=>'Elige Procedencia'])}}
               @endif
            </div>

            <div class="form-check form-check-inline">
                <input type="checkbox" {{ $user->roles()->where('nombre','AgUnam')->count()>0 ? 'Checked' : '' }} class="form-check-input" name="AgUnam" id="AgUnam" value="3">
                <label class="form-check-label" for="AgUnam">AgUnam</label>
            </div>

            <div class="form-check form-check-inline">
                <input type="checkbox" {{ $user->roles()->where('nombre','Jud')->count()>0 ? 'Checked' : '' }} class="form-check-input" name="Jud" id="Jud" value="4">
                <label class="form-check-label" for="Jud">Jud</label>
            </div>

            <div class="form-check form-check-inline">
                <input type="checkbox" {{ $user->roles()->where('nombre','Sria')->count()>0 ? 'Checked' : '' }} class="filled-in form-check-input" name="Sria" id="Sria" value="5">
                <label class="form-check-label" for="Sria">Sria</label>
            </div>

            <div class="form-check form-check-inline">
                <input type="checkbox" {{ $user->roles()->where('nombre','JSecc')->count()>0 ? 'Checked' : '' }} class="form-check-input" name="JSecc" id="JSecc" value="6">
                <label class="form-check-label" for="JSecc">JSecc</label>
            </div>

            <div class="form-check form-check-inline">
                <input type="checkbox" {{ $user->roles()->where('nombre','JArea')->count()>0 ? 'Checked' : '' }} class="form-check-input" name="JArea" id="JArea" value="7">
                <label class="form-check-label" for="JArea">JArea</label>
            </div>

            <div class="form-check form-check-inline">
                <input type="checkbox" {{ $user->roles()->where('nombre','Ofisi')->count()>0 ? 'Checked' : '' }} class="filled-in form-check-input" name="Ofisi" id="Ofisi" value="8">
                <label class="form-check-label" for="Ofisi">Ofisi</label>
            </div>

            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="Invit" id="Invit" value="9" checked="checked" disabled>
                <label class="form-check-label" for="Invit">Invit</label>
            </div>
            <br><br>

            <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
            <a href="{{ route('users') }}" class="btn btn-link">Regresar a la lista de usuarios</a>
        </form>
    </div>
</div>
@endsection
