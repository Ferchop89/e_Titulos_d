@extends('layouts.app')

@section('title',"Crear un nuevo usuario.")

@section('content')

    <div class="container">
        <h1 class="pb-1">
            Crear usuario
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
            <form method="POST" action="{{ url('usuarios') }}">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Nombre de usuario" value="{{ old('name')}}"/>
                </div>

                <div class="form-group">
                    <label for="username">Alias</label>
                    <input type="text" class="form-control" name="username" id="username" placeholder="mayor a seis caracteres" value="{{ old('username')}}"/>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Usuario@net.com" value="{{ old('email')}}"/>
                </div>



                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="mayor a seis caracteres"/>
                </div>

                <div>
                    <label>Roles en el Sistema</label>
                </div>

                <table class="table table-bordered">
                    <thead class="table table-sm">
                        <tr>
                            @foreach ($roles as $role)
                                <th scope="col">{{ $role->descripcion}}</th>
                            @endforeach
                        </tr>
                    </thead>

                    <td>
                        <div class="form-check">
                            <input type="checkbox" {{ old('Admin') ? 'Checked' : '' }} class="filled-in form-check-input" name="Admin" id="Admin" value="1">
                            <label class="form-check-label" for="Admin">Admin</label>
                        </div>
                    </td>

                    <td>
                        <div class="form-check">
                            <input type="checkbox" {{ old('FacEsc') ? 'Checked' : '' }} class="filled-in form-check-input" name="FacEsc" id="FacEsc" value="2">
                            <label class="form-check-label" for="FacEsc">FacEsc</label>
                        </div>
                    </td>

                    <td>
                        <div class="form-check">
                            <input type="checkbox" {{ old('AgUnam') ? 'Checked' : '' }} class="form-check-input" name="AgUnam" id="AgUnam" value="3">
                            <label class="form-check-label" for="AgUnam">AgUnam</label>
                        </div>
                    </td>

                    <td>
                        <div class="form-check">
                            <input type="checkbox" {{ old('Jud') ? 'Checked' : '' }} class="form-check-input" name="Jud" id="Jud" value="4">
                            <label class="form-check-label" for="Jud">Jud</label>
                        </div>
                    </td>

                    <td>
                        <div class="form-check">
                            <input type="checkbox" {{ old('Sria') ? 'Checked' : '' }} class="filled-in form-check-input" name="Sria" id="Sria" value="5">
                            <label class="form-check-label" for="Sria">Sria</label>
                        </div>
                    </td>

                    <td>
                        <div class="form-check">
                            <input type="checkbox" {{ old('JSecc') ? 'Checked' : '' }} class="form-check-input" name="JSecc" id="JSecc" value="6">
                            <label class="form-check-label" for="JSecc">JSecc</label>
                        </div>
                    </td>

                    <td>
                        <div class="form-check">
                            <input type="checkbox" {{ old('JArea') ? 'Checked' : '' }} class="form-check-input" name="JArea" id="JArea" value="7">
                            <label class="form-check-label" for="JArea">JArea</label>
                        </div>
                    </td>

                    <td>
                        <div class="form-check">
                            <input type="checkbox" {{ old('Ofisi') ? 'Checked' : '' }} class="filled-in form-check-input" name="Ofisi" id="Ofisi" value="8">
                            <label class="form-check-label" for="Ofisi">Ofisi</label>
                        </div>
                    </td>

                    <td>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="Invit" id="Invit" value="9" checked="checked" disabled>
                            <label class="form-check-label" for="Invit">Invit</label>
                        </div>
                    </td>
                </table>

                <div id="xproc">
                  <div class="form-group">
                      <label for="procedencia">Procedencia</label>
                          {{  Form::select('procedencia_id',
                          App\Models\Procedencia::pluck('procedencia','id'),
                          null,['class'=>'form-group','placeholder'=>'Elige'])}}
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Crear Usuario</button>
                <a href="{{ route('users') }}" class="btn btn-link">Regresar al listado de usuarios</a>
            </form>
        </div>
    </div>
@endsection
