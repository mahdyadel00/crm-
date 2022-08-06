@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->


        <!-- action buttons -->
        @include('pages.knowledgebase.components.misc.list-page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->

    <!-- page content -->
    <div class="row">
        <div class="col-sm-12 col-lg-9" id="knowledgebase-table-wrapper">
            <!--knowledgebase article-->
            @include('pages.knowledgebase.article.page')
            <!--knowledgebase table-->
        </div>
        <div class="col-sm-12 col-lg-3" id="knowledgebase-table-wrapper">
            <!--knowledgebase table-->
            @include('pages.knowledgebase.components.table.sidepanel')
        </div>
    </div>
    <!--page content -->
</div>
<!--main content -->
@endsection