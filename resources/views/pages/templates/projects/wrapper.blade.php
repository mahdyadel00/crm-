@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->


        <!-- action buttons -->
        @include('pages.templates.projects.components.misc.list-page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->

    <!-- page content -->
    <div class="row">
        <div class="col-12" id="foos-table-wrapper">
            <!--foos table-->
            @include('pages.templates.projects.components.table.wrapper')
            <!--foos table-->
        </div>
    </div>
    <!--page content -->
</div>
<!--main content -->
@endsection