@extends('layouts.app')
@section('title', 'CONDOC | RE por Alumno')

@section('content')
<div id="is" class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Revisión de Estudios por Alumno </div>

                <div class="panel-body">

                	@if ($errors->any())
                	    <div id="error" class="alert alert-danger">
                	        <ul>
                	            @foreach ($errors->all() as $error)
                	                <li>{{ $error }}</li>
                	            @endforeach
                	        </ul>
                	    </div>
                	@endif

                	<form class="form-group" method="POST" action="{{ url('FacEsc/consulta_re') }}">
                		{!! csrf_field() !!}

	                	<label id="general" for="no_cuenta" class="col-md-4 control-label"> N° de cuenta: </label>

	                		<div class="col-md-6">
	                			<input id="num_cuenta" type="text" class="form-control" name="num_cuenta" value="" maxlength="9"
                                style="width: 100%; position:relative; left: 10%;"
                                required autofocus>
	                		</div>

		                	<div class="form-group">
		                        <div class="col-md-8 col-md-offset-4">
		                        	<button id="general" type="submit" class="btn btn-primary">
		                            	Consultar
		                        	</button>
		                        </div>
		                    </div>
					</form>
                </div>

			</div>
        </div>
    </div>
</div>
@endsection
