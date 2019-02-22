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
                              <div class="input-group input-group-custom">
                						<div class="input-group-addon input-group-addon-custom">
                                    <div class="icon-uno user"></div>
                						</div>
                						<input id="num_cta" type="num_cta" style="width:90%;" class="form-control" name="num_cta" value="{{ old('num_cta') }}" placeholder="Número de Cuenta" maxlength="9" required autofocus>
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
                              <input id="password" type="password" style="width:90%;" class="form-control" name="password" placeholder="Contraseña" maxlength="10" required autofocus>
                              <div class="tooltip">
                                <span> <i class="fa fa-question-circle"></i> </span>
                                <!-- <span class="tooltiptext">Corresponde a la contraseña que usas para SIAE.
                                    <br><br>
                                    En caso de ser alumno de una escuela incorporada, corresponderá a tu fecha de nacimiento con formato <i>ddmmaaaa.</i>
                                </span> -->
                              </div>
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
                              <br>
                              <button type="submit" class="btn btn-primary">
                                Acceder
                              </button>
                            </div>
                          </div>
                      </form>
                  </div>
              </div>
              <div class="aclaracion">
                <br><br>
                <i class="fa fa-question-circle"></i><b>:</b>
                <span> <b>Corresponde a la contraseña que usas para SIAE, o bien, para Posgrado.</b>
                  En caso de ser alumno de una escuela incorporada, corresponderá a tu fecha de nacimiento con formato <i>ddmmaaaa.</i>
                </span>
              </div>
          {{-- </div> --}}
      </div>
    </div>
{{-- </div> --}}
@endsection
@section('animaciones')
@endsection
