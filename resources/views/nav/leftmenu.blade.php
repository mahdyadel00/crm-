@if(auth()->user()->is_team)
@include('nav.leftmenu-team')
@endif

@if(auth()->user()->is_client)
@include('nav.leftmenu-client')
@endif