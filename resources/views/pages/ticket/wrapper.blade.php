@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid ticket" id="ticket">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        @include('pages.ticket.components.misc.actions')

    </div>
    <!--page heading-->


    <!-- page content -->
    <div class="row">
        <div class="col-12" id="tickets-table-wrapper">
            <!--ticket-->
            @include('pages.ticket.components.body')
            <!--ticket-->
        </div>
    </div>
    <!--page content -->
</div>
<!--main content -->
@endsection