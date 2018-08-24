</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Example 2</title>
    <link rel="stylesheet" href="css/pdf.css">
    {{-- {!! Html::style('assets/css/pdf.css') !!} --}}
  <style>
      .page-break {
          page-break-after: always;
      }
  </style>
  </head>
  <body>
  @for ($i=0; $i <= count($data)/count($data); $i++)
        <div id="details" class="clearfix">
            <div id="logo">
              <img src="images/escudo_unam_solow.svg" alt="">
            </div>
          <div id="invoice">
            <h1>CORTE {{ $corte }}</h1>
            <div class="date">Listado: {{ $lista }}</div>
          </div>
        </div>
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                  <tr>
                      <th scope="col">#</th>
                      <th scope="col"><strong>Escuela o Facultad</strong></th>
                      <th scope="col"><strong>No. Cta</strong></th>
                      <th scope="col"><strong>Nombre</strong></th>
                      <th scope="col"><strong>Fecha; Hora</strong></th>
                  </tr>
                </thead>
                  <tbody>
                  @for ($x=0; $x < count($data); $x++)
                    <tr>
                      <th scope="row">{{($x+1)}}</th>
                      <td>{{$data[$x]->procedencia}}</td>
                      <td>{{$data[$x]->cuenta}}</td>
                      <td>{{$data[$x]->nombre}}</td>
                      <td>{{explode('-',explode(' ',$data[$x]->created_at)[0])[2].'-'
                           .explode('-',explode(' ',$data[$x]->created_at)[0])[1].'-'
                           .explode('-',explode(' ',$data[$x]->created_at)[0])[0].'; '
                           .explode(' ',$data[$x]->created_at)[1]}}</td>
                    </tr>
                  @endfor
                  </tbody>
                </table>
            </div> 
            <footer>Hoja x</footer>
            <div class="page-break"></div>
         @endfor

  </body>
</html>
