@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        @include('pages.subscription.components.misc.crumbs')

        @include('pages.subscription.components.misc.actions')

    </div>

    <div class="row">
        <div class="col-sm-12 col-lg-3">
            @include('pages.subscription.summary')
        </div>
        <div class="col-sm-12 col-lg-9">
            @include('pages.subscription.details')
        </div>
    </div>

</div>
@endsection