@if ($item['submenu'] != [])

    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $item['name'] }} <span class="caret"></span></a>
        <ul class="dropdown-menu">
        {{-- <a href="#"  role="button" aria-haspopup="true" aria-expanded="false">{{ $item['name'] }}<span class="caret"></span></a>
        <ul class="x_ul"> --}}
            @foreach ($item['submenu'] as $submenu)
                @if ($submenu['submenu'] == [])
                    <li><a  href="{{ url($submenu['ruta']) }}">
                            {{ $submenu['name']}}
                        </a>
                    </li>
                @else
                    @include('partials.menu-item', [ 'item' => $submenu ])
                @endif
            @endforeach
        </ul>
    </li>
@endif
