@extends('layouts.layoutMateriales')
@section('title', 'Impresión de Listas')
@section('content')
   <h3>RESUMEN DE TÍTULOS Y MATERIALES DE ELABORACIÓN</h3>
   <h3>PERIODO: {{$inicio}} al {{$fin}}</h3>
{!! $vista !!}
@endsection
