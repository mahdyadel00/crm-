@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">


        @include('pages.client.components.misc.crumbs')

        @include('pages.client.components.misc.actions')

    </div>
    <!--page heading-->

    <!-- page content -->
    <div class="row">
        <!--left panel-->
        <div class="col-xl-3 d-none d-xl-block">
            @include('pages.client.components.misc.leftpanel')
        </div>
        <!--left panel-->
        <!-- Column -->
        <div class="col-xl-9 col-lg-12">
            <div class="card h-100">

                <!--top nav-->
                @include('pages.client.components.misc.topnav')

                <!-- main content -->
                <div class="tab-content">
                    <div class="tab-pane active ext-ajax-container" id="clients_ajaxtab" role="tabpanel">
                        <div class="card-body tab-body tab-body-embedded p-t-40" id="embed-content-container">
                            <!--dynamic content here-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
    <!--page content -->

</div>
<!--main content -->
<span class="hidden" id="dynamic-client-content" class="js-ajax-ux-request"  data-url="{{ $page['dynamic_url'] ?? '' }}" data-loading-target="embed-content-container">placeholder</span>
@endsection