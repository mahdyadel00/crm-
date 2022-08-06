@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        @include('pages.templates.project.components.misc.crumbs')

        @include('pages.templates.project.components.misc.actions')

    </div>
    <!--page heading-->

    <!--topnav-->
    @include('pages.templates.project.components.misc.topnav')
    <!--topnav-->

    <!-- page content -->
    <div class="row m-t-10" id="projects-tab-single-screen">
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


<!--dynamically load comments-->
<span class="hidden" id="dynamic-project-content" data-loading-target="embed-content-container"
    data-url="{{ $page['dynamic_url'] ?? '' }}">placeholder</span>
@endsection