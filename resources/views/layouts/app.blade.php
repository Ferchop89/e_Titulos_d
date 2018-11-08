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
        <!-- Sección: Links -->
        <link href="{{ asset('images/favicon.ico') }}" rel="shortcut icon" type="image/x-icon">
        <link href="{{ asset('images/custom_icon.png') }}" rel="apple-touch-icon">
        <link href="{{ asset('images/custom_icon.png') }}" sizes="150x150" rel="icon">
        <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
        <link href="{{ asset('css/responsive_parallax_navbar.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        {{-- <link href="{{ asset('icss/mdb.css') }}" rel="stylesheet"> --}}
        <link href="{{ asset('css/estilo_dgae.css') }}" rel="stylesheet">
        <link href="{{ asset('css/login.css') }}" rel="stylesheet">
        <link href="{{ asset('css/MenuDinamico.css') }}" rel="stylesheet">
        {{-- <link href="{{ asset('css/rev_estudios.css') }}" rel="stylesheet"> --}}
        <!-- Sección: estilos -->
        @yield('estilos')
        <!-- /Sección: Links -->
    </head id="inicio">
    <body id="inicio">
        <header>
            {{-- <div id="skiptocontent"><a href="#maincontent">Saltarse al contenido</a></div> --}}
            <!-- Navegacion -->
            <nav role="navigation">
            <!-- Fixed navbar -->
            <div class="navbar navbar-fixed-top" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse" aria-expanded="false">
                            <i class="fa fa-bars" style="color: white;"></i></button>
                        <div class="small-logo-container">
                            <a class="small-logo" href="https://www.dgae.unam.mx/" tabindex="-1">UNAM - DGAE</a>
                        </div>
                    </div>

                    <!-- Sección: Navegación -->
                    <div class="collapse navbar-collapse">
                        <!-- Left Side Of Navbar -->
                        <ul class="nav navbar-nav">
                              <div class="container-fluid">
                                    <div class="collapse navbar-collapse">
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
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <!-- Authentication Links -->
                            @if(Auth::guard('alumno')->user())
                                <li class="dropdown">
                                    <a id="btn" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                        {{ Auth::guard('alumno')->user()->nombres }} <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('alumno.logout') }}"
                                                onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                                Cerrar sesión
                                            </a>

                                            <form id="logout-form" action="{{ route('alumno.logout') }}" method="POST" style="display: block; color: black;">
                                                {{ csrf_field() }}
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @elseif(Auth::guard('web')->user())
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
                            @else
                              <li><a id="btn" href="{{ route('alumno.login') }}">Iniciar sesión</a></li>
                            @endif


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
                                <a href="https://www.unam.mx/" title="CONDOC" tabindex="-1">
                                    <img src="{{ asset('images/escudo_unam_solow.svg') }}">
                                </a>
                            </div>
                            {{-- <div class="pull-center logo_grande logo_centro">
                                <a href="#" title="CONDOC" tabindex="-1">
                                    CONDOC
                                    <img src="#">

                                </a>
                            </div>
                            <div class="pull-center logo_chico logo_centro">
                                <a href="#" title="UNAM" tabindex="-1">
                                    <img src="#">
                                    CONDOC
                                </a>
                            </div> --}}
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
            {{--
            @yield('content') --}}
            </nav>
        </header>
        <main role="main">
            <div class="ubicacion">
                @yield('location')
            </div>
            <div class="container">
                @yield('content')
            </div>

        </main>
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
                                <li>Revisiones de Estudio</li>
                                <li>Estatus de Revisiones de Estudio</li>
                                <li></li>
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
        {{-- <script type="text/javascript" src="{{ asset('js/mdb.js') }}"></script> --}}
        <!-- Analytics -->
        {{-- <script type="text/javascript" src="{{ asset('js/analytics.js') }}"></script> --}}
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
        @yield('animaciones')
    </body>
</html>
