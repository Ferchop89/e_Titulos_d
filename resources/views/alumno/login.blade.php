@extends('layouts.app')
@section('estilos')
   <link href="{{ asset('css/alumnos.css') }}" rel="stylesheet">
@endsection
@section('content')
{{-- <div class="container"> --}}
    <div id="is" class="container">
      <div class="contenedor-login">
          {{-- <div class="col-md-8 col-md-offset-2"> --}}
              <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4>
                      Acceso a Estudiantes
                    </h4>
                  </div>

                  <div class="panel-body">
                      <form class="" id="loginForm" method="POST" action="{{ route('alumno.login') }}">
                          {{ csrf_field() }}

                          <div class="login-form form-group{{ $errors->has('num_cta') ? ' has-error' : '' }}">
                              {{-- <label for="num_cta" class="col-md-4 control-label">Número de Cuenta</label> --}}
                              <div class="input-group input-group-custom">
                						<div class="input-group-addon input-group-addon-custom">
                                    <div class="icon-uno user"></div>
                						</div>
                						<input id="num_cta" type="num_cta" class="form-control" name="num_cta" value="{{ old('num_cta') }}" placeholder="Número de Cuenta" maxlength="9" required autofocus>
                				   </div>
                              @if ($errors->has('num_cta'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('num_cta') }}</strong>
                                  </span>
                              @endif
                          </div>

                          <div class="login-form form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                              {{-- <label for="password" class="col-md-4 control-label">Contraseña</label> --}}
                              <div class="input-group input-group-custom">
                							<div class="input-group-addon input-group-addon-custom">
                                      <div class="icon-dos lock" aria-hidden="true"></div>
                							</div>
                                  <input id="password" type="password" class="form-control" name="password" placeholder="Contraseña ddmmaaaa" maxlength="8" required autofocus>
                				   </div>
                              @if ($errors->has('password'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('password') }}</strong>
                                  </span>
                              @endif
                          </div>
                           @if (Session::has('message'))
                              <div class="alert alert-danger">{{ Session::get('message') }}</div>
                           @endif
                          <div class="form-group">
                              <div class="center">
                                  <button type="submit" class="btn btn-primary">
                                      Acceder
                                  </button>

                                  {{-- <a class="btn btn-link" href="{{ route('alumno.forgot') }}">
                                      Haz olvidado tu contraseña?
                                  </a> --}}
                              </div>
                          </div>
                      </form>
                  </div>
              </div>
          {{-- </div> --}}
      </div>
    </div>
{{-- </div> --}}
@endsection
