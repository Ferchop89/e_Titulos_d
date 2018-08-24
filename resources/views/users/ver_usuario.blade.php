@extends('layouts.app')
@section('title',"Usuario {$user->id}")
@section('content')
<div class="container">
    {{-- <div class="card"> --}}
    <h1 class="pb-1">Perfil del usuario "{{ $user->username }}"</h1>
    <div class="card-body">
        <div class="form-group">
            <label for="name">Nombre del Usuario</label>
            <div class="form-control">{{ $user->name }}</div>
        </div>
        <div class="form-group">
            <label for="username">Alias</label>
            <div class="form-control">{{ $user->username }}</div>
        </div>
        <div class="form-group">
            <label for="correo">Correo Electrónico</label>
            <div class="form-control">{{ $user->email }}</div>
        </div>

        <div class="form-group">
            <input type="checkbox" {{ $user->is_active ? 'checked' : ''   }} name="is_active" OnClick="return false;" >
            <label for="correo">Usuario Activo</label>
        </div>

        <div>
            <label>Roles en el Sistema</label>
        </div>

        <div class="form-check form-check-inline">
            <input type="checkbox" {{ $user->roles()->where('nombre','Admin')->count()>0 ? 'Checked' : '' }} class="form-check-input" name="Admin" id="Admin" value="1" OnClick="return false;">
            <label class="form-check-label" for="Admin">Admin</label>
        </div>

        <div class="form-check form-check-inline">
            <input type="checkbox" {{ $user->roles()->where('nombre','FacEsc')->count()>0 ? 'Checked' : '' }} class="filled-in form-check-input" name="FacEsc" id="FacEsc" value="2" OnClick="return false;">
            <label class="form-check-label" for="FacEsc">FacEsc</label>
        </div>

        @if($user->procedencia_id != null)
            <td>{{ App\Models\Procedencia::where('id',$user->procedencia_id)->pluck('procedencia')[0] }}</td>
        @else
            <td>Sin procedencia</td>
        @endif

        <div class="form-check form-check-inline">
            <input type="checkbox" {{ $user->roles()->where('nombre','AgUnam')->count()>0 ? 'Checked' : '' }} class="form-check-input" name="AgUnam" id="AgUnam" value="3" OnClick="return false;">
            <label class="form-check-label" for="AgUnam">AgUnam</label>
        </div>

        <div class="form-check form-check-inline">
            <input type="checkbox" {{ $user->roles()->where('nombre','Jud')->count()>0 ? 'Checked' : '' }} class="form-check-input" name="Jud" id="Jud" value="4" OnClick="return false;" OnClick="return false;">
            <label class="form-check-label" for="Jud">Jud</label>
        </div>

        <div class="form-check form-check-inline">
            <input type="checkbox" {{ $user->roles()->where('nombre','Sria')->count()>0 ? 'Checked' : '' }} class="filled-in form-check-input" name="Sria" id="Sria" value="5" OnClick="return false;">
            <label class="form-check-label" for="Sria">Sria</label>
        </div>

        <div class="form-check form-check-inline">
            <input type="checkbox" {{ $user->roles()->where('nombre','JSecc')->count()>0 ? 'Checked' : '' }} class="form-check-input" name="JSecc" id="JSecc" value="6" OnClick="return false;">
            <label class="form-check-label" for="JSecc">JSecc</label>
        </div>

        <div class="form-check form-check-inline">
            <input type="checkbox" {{ $user->roles()->where('nombre','JArea')->count()>0 ? 'Checked' : '' }} class="form-check-input" name="JArea" id="JArea" value="7" OnClick="return false;">
            <label class="form-check-label" for="JArea">JArea</label>
        </div>

        <div class="form-check form-check-inline">
            <input type="checkbox" {{ $user->roles()->where('nombre','Ofisi')->count()>0 ? 'Checked' : '' }} class="filled-in form-check-input" name="Ofisi" id="Ofisi" value="8" OnClick="return false;">
            <label class="form-check-label" for="Ofisi">Ofisi</label>
        </div>

        <div class="form-check form-check-inline">
            <input type="checkbox" class="form-check-input" name="Invit" id="Invit" value="9" checked="checked"  OnClick="return false;">
            <label class="form-check-label" for="Invit">Invit</label>
        </div>

        <br><br>

        <a href="{{ route('users') }}" class="btn btn-link">Regresar a la lista de usuarios</a>
    </div>
    {{-- </div> --}}
</div>
@endsection
