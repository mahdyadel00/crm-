@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid" id="wrapper-tickets">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->

    </div>
    <!--page heading-->


    <!-- page content -->
    <div class="row">
        <div class="col-12" id="tickets-table-wrapper">
            <!--tickets table-->
            @include('pages.tickets.components.create.compose')
            <!--tickets table-->
        </div>
    </div>
    <!--page content -->
</div>
<!--main content -->
@endsection