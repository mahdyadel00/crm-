@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        @include('pages.project.components.misc.crumbs')

        @include('pages.project.components.misc.actions')

    </div>
    <!--page heading-->

    <!--topnav-->
    @include('pages.project.components.misc.topnav')
    <!--topnav-->

    <!-- page content -->
    <div class="row m-t-10" id="projects-tab-split-screen">

        <!--project details section-->
        @if(isset($page['section']) && $page['section'] == 'overview')
        <div class="col-md-12 col-lg-6 hide_tabbed_section">
            <div class="card min-h-300">
                <div class="tab-content">
                    <div class="tab-pane active">
                        <div class="card-body tab-body p-t-10">
                            @include('pages.project.components.misc.details')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!--/#project details section-->

        <!--dynamic ajax section-->
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="tab-content">
                    <div class="tab-pane active ext-ajax-container" id="projects_ajaxtab" role="tabpanel">
                        <div class="card-body tab-body tab-body-embedded project-timeline" id="embed-content-container">
                            <!--dynamic content here-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/#dynamic ajax section-->

    </div>
    <!--page content -->

    <!-- page content -->
    <div class="row m-t-10 hidden" id="projects-tab-single-screen">
        <!--dynamic ajax section-->
        <div class="col-lg-12">
            <div class="card min-h-300">
                <div class="tab-content">
                    <div class="tab-pane active ext-ajax-container" id="projects_ajaxtab" role="tabpanel">
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

<!--task modal-->
@include('pages.task.modal')


<!--ajax tab initial loading - timeline-->
<span id="dynamic-project-content" class="js-ajax-ux-request hidden" data-loading-target="embed-content-container"
    data-url="{{ $page['dynamic_url'] ?? '' }}">placeholder</span>
@endsection