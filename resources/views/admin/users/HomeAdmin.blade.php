@extends('layouts.app')

@section('content')
<div id="is" class="container">
    {{-- <div class="row"> --}}
        {{-- <div class="col-md-8 col-md-offset-2"> --}}
            <div class="panel panel-default">
                <div class="panel-heading">Panel de Administración</div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="padre">
                        <div class="hijo">
                            <span>Bienvenid@ al sistema CONDOC</span>
                        </div>
                    </div>
                </div>
            </div>
        {{-- </div> --}}
    {{-- </div> --}}
</div>
@endsection
