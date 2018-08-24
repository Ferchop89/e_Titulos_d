@extends('layout')

@section('content')

  <h1>Dynamic DropDowns</h1>
  {{ Form::open(['class' => 'form'])  }}
    {{-- {{ Field::select('make_id') }}
    {{ Field::select('makeyear_id') }}
    {{ Field::select('model_id')  }} --}}
    {{ Form::label('size', 'size') }}
    {{ Form::select('size', App\Models\Role::pluck('nombre','id')->prepend('Selecciona')) }}
  {{ Form::close() }}

@endsection
