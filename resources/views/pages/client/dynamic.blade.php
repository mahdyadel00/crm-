@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        @include('pages.client.components.misc.crumbs')

        @include('pages.client.components.misc.actions')

    </div>
    <!--page heading-->

    <!--topnav-->
    @include('pages.client.components.misc.topnav')
    <!--topnav-->

    <!-- page content -->
    <div class="row m-t-10" id="clients-tab-single-screen">
        <!--dynamic ajax section-->
        <div class="col-lg-12">
            <div class="card" id="fx-client-dynamic-card">
                <div class="tab-content">
                    <div class="tab-pane active ext-ajax-container" id="clients_ajaxtab" role="tabpanel">
                        <div class="card-body tab-body tab-body-embedded" id="embed-content-container">
                            <!--dynamic content here-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--page content -->

</div>
<!--page content -->
</div>
<!--main content -->
<!--ajax tab loading-->


<!--dynamically load comments-->
<span class="hidden" id="dynamic-client-content" data-loading-target="embed-content-container"  data-url="{{ $page['dynamic_url'] ?? '' }}"></span>
@endsection