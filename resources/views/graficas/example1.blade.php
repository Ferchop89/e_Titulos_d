<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js" type="text/javascript"></script>
</head>
<body>

<h1>RECEPCION DE SOLICITUDES Y CITATORIOS</h1>
<h2>{{$procedencia}}</h2>
<h3>{{$mes}}: {{$anio}}</h3>

{{-- <div style="width:50%;">
    {!! $chart1->render() !!} {!! $chart2->render() !!}
</div> --}}


<div style="float: left; width: 50%">
    {!! $chart2->render() !!}
</div>
<div style="float: left; width: 50%">
    {!! $chart1->render() !!}
</div>




</body>
</html>
