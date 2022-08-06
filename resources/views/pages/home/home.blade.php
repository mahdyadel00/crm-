@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--admin dashboard-->
    @if(auth()->user()->is_team)
    @if(auth()->user()->is_admin)
    @include('pages.home.admin.wrapper')
    @else
    @include('pages.home.team.wrapper')
    @endif
    @endif

    @if(auth()->user()->is_client)
    @include('pages.home.client.wrapper')
    @endif



</div>
<!--main content -->
@endsection