@extends('pages.settings.wrapper-settings') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->

        <!-- action buttons -->
        <div class="col-md-12 col-lg-7 align-self-center text-right parent-page-actions">
        </div>
        <!-- action buttons -->
    </div>
    <!--page heading-->


    <!-- main content -->
    <div class="card min-h-300">
            <div class="card-body tab-body tab-body-embedded" id="embed-content-container">
            @yield('settings-page')
        </div>
    </div>
    <!-- /#main content -->

</div>
<!--page content -->
</div>
<!--main content -->
<!--dynamically load comments-->
@endsection