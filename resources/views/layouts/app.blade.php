<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="UNAM, Direccion, General, Administracion, Escolar, Servicios, Escolares, Concursos, Ingreso, estudiantes, académicos, egresados, alumnos, publicacion, resultados, dgae, admisión, licenciatura,posgrado, maestría,bachillerato,educación,a,distancia,abierta">
    <meta name="description" content="UNAM, Direccion General de Administracion Escolar, Servicios Escolares, Concursos de Ingreso a la UNAM, Administracion Escolar">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1C3D6C">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Sección: Title del Sitio -->
    <title>@yield('title')</title>
    <!-- /Sección: Title del Sitio -->
    <!-- Sección: Links -->

    <link href="{{ asset('images/favicon.ico') }}" rel="shortcut icon" type="image/x-icon">
    <link href="{{ asset('images/custom_icon.png') }}" rel="apple-touch-icon">
    <link href="{{ asset('images/custom_icon.png') }}" sizes="150x150" rel="icon">
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive_parallax_navbar.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset('icss/mdb.css') }}" rel="stylesheet">
    <link href="{{ asset('css/estilo_dgae.css') }}" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">

    <link href="{{ asset('css/MenuDinamico.css') }}" rel="stylesheet">

    <style media="screen">
    .event1 a {
        background-color: #1a3e11!important;
        background-image :none !important;
        color: #ffffff !important;
    }
    .event2 a {
        background-color: #235316 !important;
        background-image :none !important;
        color: #ffffff !important;
    }
    .event3 a {
        background-color: #2c681c !important;
        background-image :none !important;
        color: #ffffff !important;
    }
    .event4 a {
        background-color: #347c22 !important;
        background-image :none !important;
        color: #ffffff !important;
    }
    .event5 a {
        background-color: #3d9127 !important;
        background-image :none !important;
        color: #ffffff !important;
    }
    .event6 a {
        background-color: #46a62d !important;
        background-image :none !important;
        color: #ffffff !important;
    }
    .event7 a {
        background-color: #4fbb33 !important;
        background-image :none !important;
        color: #000000 !important;
    }
    .event8 a {
        background-color: #58d039 !important;
        background-image :none !important;
        color: #000000 !important;
    }
    .event9 a {
        background-color: #68d44c !important;
        background-image :none !important;
        color: #000000 !important;
    }
    .event10 a {
        background-color: #79d960 !important;
        background-image :none !important;
        color: #000000 !important;
    }
    .event a {
        background-color: #000000 !important;
        background-image :none !important;
        color: #000000 !important;
    }
    .highlight
    {
      background: yellow;
      font-weight: bold;
    }
    </style>

    <!-- /Sección: Links -->
</head id="inicio">
    <body id="inicio">



    <div id="skiptocontent"><a href="#maincontent">Saltarse al contenido</a></div>
    <!-- Navegacion -->
    <nav role="navigation">
        <!-- Fixed navbar -->
        <div class="navbar navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse" aria-expanded="false">
                        <i class="fa fa-bars" style="color: white;"></i>
                    </button>
                    <div class="small-logo-container">
                        <a class="small-logo" href="https://www.dgae.unam.mx/" tabindex="-1">
                        UNAM - DGAE
                    </a>
                    </div>
                </div>

                <!-- Sección: Navegación -->
                <div class="collapse navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        @if (auth()->user())
                          <div class="container-fluid">
                                <div class="collapse navbar-collapse">
                                  {{-- <ul class="nav navbar-nav"> --}}
                                    <ul class="nav navbar-nav menu">
                                        @if (count($items_role)>0)

                                            @foreach ($menus as $key => $item)
                                                @if ($item['parent'] != 0)
                                                    @break
                                                @endif
                                                @include('partials.menu-item', ['item' => $item])
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @guest
                            <li><a id="btn" href="{{ route('login') }}">Iniciar sesión</a></li>
                        @else
                            <li class="dropdown">
                                <a id="btn" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Cerrar sesión
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: block; color: black;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
                <!-- /Sección: Navegación -->
            </div>
        </div>
        <div class=" big-logo-row">
            <div class="container">
                <div class="col-lg-12 col-md-12 big-logo-container">
                    <div class="big-logo">
                        <div class="pull-left logo_grande logo_der">
                            <a href="https://www.unam.mx/" title="UNAM" tabindex="-1">
                                <img src="{{ asset('images/escudo_unam_completow.svg') }}">
                            </a>
                        </div>
                        <div class="pull-left logo_chico logo_der">
                            <a href="https://www.unam.mx/" title="UNAM" tabindex="-1">
                                <img src="{{ asset('images/escudo_unam_solow.svg') }}">
                            </a>
                        </div>
                        <div class="pull-right logo_grande logo_izq">
                            <a href="https://www.dgae.unam.mx" title="DGAE" tabindex="-1">
                                <img src="{{ asset('images/escudo_dgae_completow.svg') }}">
                            </a>
                        </div>
                        <div class="pull-right logo_chico logo_izq">
                            <a href="https://www.dgae.unam.mx" title="DGAE" tabindex="-1">
                                <img src="{{ asset('images/escudo_dgae_solow.svg') }}">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @yield('content')
    </nav>

    <!--Principio del Footer -->
    <footer class="main-footer">
        <div class="list-info">
            <div class="container">
                <div class="col-sm-3">
                    <h5 tabindex="0">Contacto</h5>
                    <p class="pmenor" tabindex="0"><i class="fa fa-phone"></i> &nbsp;Atención por Teléfono
                        <br> <a href="tel:56221524" class="link_footer_tel">5622 - 1524</a>
                        <br> <a href="tel:56221525" class="link_footer_tel">5622 - 1525</a></p>
                        <p class="pmenor" tabindex="0"><i class="fa fa-clock-o"></i> &nbsp; De 9:00 a 15:00 hrs.<br>y de 17:00 a 19:30 hrs.</p>
                </div>
                <div class="col-sm-6">
                    <p class="pmenor" tabindex="0">Se brinda información de:
                        </p><ul tabindex="0">
                            <li>Convocatoria para los concursos de selección</li>
                            <li>Examen COMIPEMS</li>
                            <li>Ingreso a Iniciación Universitaria</li>
                            <li>Ingreso a Licenciatura por Pase Reglamentado</li>
                            <li>Resultados de los concursos de selección</li>
                            <li>Trámites y Servicios Escolares en general</li>
                            <li>Ubicación de dependencias de la UNAM</li>
                            <li>Venta de Guías y Planes de Estudio</li>
                        </ul>
                    <p></p>
                </div>
                <div class="col-sm-3">
                    <h5><i class="fa fa-sitemap"></i> &nbsp;<a href="https://www.dgae.unam.mx/mapasitio.html" class="link_footer" tabindex="0">Mapa de sitio</a></h5>
                    <br>
                    <h5><i class="fa fa-assistive-listening-systems"></i> &nbsp;<a href="https://www.dgae.unam.mx/herramientas.html" class="link_footer" tabindex="0">Herramientas de Accesibilidad</a></h5>
                    <br>
                    <br>
                </div>
            </div>
        </div>
        <div class="row" id="fondo">
            <div class="col-sm-12">
                <p class="pmenor" tabindex="0">
                    Hecho en México, Universidad Nacional Autónoma de México (UNAM), todos los derechos reservados 2009 - 2014.
                    <br>Esta página puede ser reproducida con fines no lucrativos, siempre y cuando no se mutile, se cite la fuente completa y su dirección electrónica. De otra forma, requiere permiso previo por escrito de la institución
                    <br>
                    </p><div style="float: center;">
                        <img src="https://www.dgae.unam.mx/assets/images/logo_responsivo.png" alt="Sitio Responsivo" height="42" width="42" style="margin-top:-24px;"> &nbsp;
                        <span class="fa fa-universal-access" style="font-size:42px;"></span>
                    </div>
                    <br>Sitio web administrado por: Dirección General de Administración Escolar<p></p>
            </div>
        </div>
    </footer>

    <!-- Sección: Scripts -->
    <script type="text/javascript" src="{{ asset('js/jquery.js') }}"></script>
    <!-- Bootstrap Core JavaScript -->
    <script type="text/javascript" src="{{ asset('js/bootstrap.js') }}"></script>
    <!-- Material Design Bootstrap -->
    <script type="text/javascript" src="{{ asset('js/mdb.js') }}"></script>
    <!-- Analytics -->
    <script type="text/javascript" src="{{ asset('js/analytics.js') }}"></script>
    <!-- barra de navegación-->
    <script type="text/javascript" src="{{ asset('js/navbar.js') }}"></script>
    <!-- /Sección: Scripts -->

    {{-- Para el uso del datepicker --}}
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <link href="{{asset('css/select2.css')}}" rel="stylesheet" />
    <script src="{{asset('js/select2.js')}}"></script>
    <div class="hiddendiv common"></div>
    <script type="text/JavaScript">
    $(document).ready(function(){

      // Json que nos arroja el último corte registrado en Revisiones.
      $.get('fechaCorte', null, function(data)
      {
          //Si no existen listas, la fecha inicial será la del dia actual...

          if (data!="") {
            var pattern = /(\d{2})\.(\d{2})\.(\d{4})/;
            var dt = new Date(data.replace(pattern,'$2-$1-$3'));
          } else {
            var dt = new Date();
          }

          // var dt = new Date(data.replace(pattern,'$2-$1-$3'));

          // Si no se ha especificado una fecha (como en la primera carga) la establecemos.
          if ($('#datepicker').val()=="") {
              $('#datepicker').datepicker('setDate', dt);
          }
      });

      var date = new Date();
      var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

      $.datepicker.regional['es'] =
      {
          closeText: 'Cerrar',
          prevText: '< Ant',
          nextText: 'Sig >',
          currentText: 'Hoy',
          monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
          monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
          dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
          dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
          dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
          weekHeader: 'Sm',
          dateFormat: 'dd/mm/yy',
          defaultDate: new Date('07/01/2018'),
          firstDay: 1,
          isRTL: false,
          showMonthAfterYear: false,
          yearSuffix: ''
        };
      $.datepicker.setDefaults($.datepicker.regional['es']);
      $('select:not(.normal)').each(function () {
          $(this).select2({
              dropdownParent: $(this).parent()
          });
      });

      // datepicker
      var fechas = new Array();
      var rango = new Array();

      $.get('grupoListas', null, function( data)
      {
        $.each( data, function( key, val) {
            fechas.push({ fecha: val.corte, total: [val.listas,val.cuenta] });
         });
         // alert(fechas[0].total[0] + " ** " + fechas[0].fecha);
         limites(fechas);
      });

      function inArray(item, arreglo)
      {
          var length = arreglo.length;
          for(var i = 0; i < length; i++) {
              if(arreglo[i].fecha == item)
                  return arreglo[i].total[1]+'-'+arreglo[i].total[0];
          }
          return '';
      }
      function fechaStr(date)
      {
        var dd = date.getDate(); dd = dd < 10 ? '0' + dd : dd;
        var mm = date.getMonth()+1; mm = mm < 10 ? '0' + mm : mm;
        var yyyy = date.getFullYear();
        var fecha = dd + '.' + mm + '.' + yyyy;
        return fecha;
      }
      function limites(arreglo)
      {
        var length = arreglo.length;
        var inferior = superior = arreglo[0].total[1];
        for(var i = 0; i < length; i++) {
          inferior = inferior <= arreglo[i].total[1] ?  inferior : arreglo[i].total[1];
          superior = superior <= arreglo[i].total[1] ?  arreglo[i].total[1] : superior;
        }
        rango.push(inferior);
        rango.push(superior);
      }
      $('#datepicker').datepicker(
      {
          beforeShowDay: function( date )
          {
                // var xvalor = new Array();
                xvalor = inArray(fechaStr(date), fechas).split('-');
                    if ( xvalor[0] != '' ) {
                      // Se aplica el logaritmo para matizar las diferencias
                      rango100 = 100*( Math.log(xvalor[0]) - Math.log(rango[0]) ) /
                                     ( Math.log( rango[1]) - Math.log(rango[0]) );
                      // si son mas de un listado, se especifica, sino, solo las solicitudes
                      if (xvalor[1] > 1) {
                          estatis = xvalor[0] + ' solicitudes; ' + xvalor[1] + ' listados.';
                      } else {
                          estatis = xvalor[0] + ' solicitudes;';
                      }

                      switch(true) {
                          case (rango100>=90 && rango100<=100):
                              return [true, "event1", estatis];
                              break;
                          case (rango100>=80 && rango100<90):
                              return [true, "event2", estatis];
                              break;
                          case (rango100>=70 && rango100<80):
                                  return [true, "event3", estatis];
                                  break;
                          case (rango100>=60 && rango100<70):
                                return [true, "event4", estatis];
                                break;
                          case (rango100>=50 && rango100<60):
                                  return [true, "event5", estatis];
                                  break;
                          case (rango100>=40 && rango100<50):
                                  return [true, "event6", estatis];
                                  break;
                          case (rango100>=30 && rango100<40):
                                  return [true, "event7", estatis];
                                  break;
                          case (rango100>=20 && rango100<30):
                                  return [true, "event8", estatis];
                                  break;
                          case (rango100>=10 && rango100<20):
                                  return [true, "event9", estatis];
                                  break;
                          case (rango100>=0 && rango100<10):
                                  return [true, "event10", estatis];
                                  break;
                          default:
                              return [true, "event", estatis];
                          }
                      } else {
                        return [true, '', ''];  }
           }
      });
      $('#search').keyup(function()
      {
          // Procedimiento para buscar  y resaltar en numero de cuenta
          textocta = $(this).val();
          cuentas = $( "p.matriculas" ).toArray();
          if (textocta.length!=0)
          {
            for (var i = 0; i < cuentas.length; i++)
            {
              contenido = $(cuentas[i]).text();
              searchExp = new RegExp(textocta, "ig");
              matches = contenido.match(searchExp);
              if (matches) {
                $(cuentas[i]).show();
                $(cuentas[i]).html(contenido.replace(searchExp,function(match){
                  return "<span class='highlight'>" + match + "</span>";
                  }));
              } else {
                $(cuentas[i]).html(contenido);$(cuentas[i]).hide();
              }
            }
          } else {
            for (var i = 0; i < cuentas.length; i++)
              {
                contenido = $(cuentas[i]).text();
                $(cuentas[i]).html(contenido);$(cuentas[i]).show();
              }
          }
        });
      if ($('#FacEsc').is(':checked') != true) {
            $('#xproc').fadeOut('slow');
          }
      $('#FacEsc').change(function(){
          if (this.checked) {
              $('#xproc').fadeIn('slow');
          }
          else {
              $('#xproc').fadeOut('slow');
          }
        });
      // $( "#datepicker" ).datepicker( "hide" );

      // Fijamos la fecha inicial de corte en el DatePicker
      // $('#datepicker').datepicker('setDate', today)
     // $( ".selector" ).datepicker( "refresh" );
  });

  </script>
  </body></html>
