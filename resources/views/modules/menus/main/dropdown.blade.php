<!--[dropdown menu] - dyamic menu markup for [MODULES]-->

<li data-modular-id="main_menu_team_billing" class="sidenav-menu-item">
    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0);" aria-expanded="false">
        <i class="{{ $icon }}"></i>
        <span class="hide-menu">{{ __($name) }}
        </span>
    </a>
    <ul aria-expanded="false" class="collapse">
        @foreach($items as $payload)
        <li class="sidenav-submenu {{ $payload['classes_li'] }}" id="submenu_invoices">
            <a href="{{ $payload['href'] }}" class="{{ $payload['classes_a'] }}">{{ __($payload['name']) }}</a>
        </li>
        @endforeach
    </ul>
</li>