@extends('layouts.app')

@section('estilos')
    <link href="{{ asset('css/MenuHome.css') }}" rel="stylesheet">
@endsection

@section('content')
<div id="is" class="container capsule home">
  <div class="panel panel-default">
    <div class="padre">
      <div class="hijo">
        <span>Bienvenid@ al sistema CONDOC</span>
      </div>
    </div>
  </div>
</div>
@endsection
@section('animaciones')
    <script type="text/javascript" src="{{ asset('js/MenuHome.js') }}"></script>
@endsection
