@if ($item['submenu'] != [])
    @if ($item['is_structure'] && $item['parent'] != 0)
        <li class="divider"></li>
        <li class="dropdown-header">
            {{ $item['name'] }}
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
        </li>
    @else
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                {{ $item['name'] }}
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
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
@endif
