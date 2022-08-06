@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->


        <!-- action buttons -->
        @include('pages.leads.components.misc.list-page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->

    <!-- page content -->
    <div class="row kanban-wrapper">
        <div class="col-12" id="leads-layout-wrapper">
            @if(auth()->user()->pref_view_leads_layout == 'kanban')
            @include('pages.leads.components.kanban.wrapper')
            @else
            <!--leads table-->
            @include('pages.leads.components.table.wrapper')
            <!--leads table-->
            @endif

            <!--filter-->
            @include('pages.leads.components.misc.filter-leads')
            <!--filter-->
        </div>
    </div>
    <!--page content -->

</div>
<!--main content -->
@include('pages.lead.modal')


<!--dynamic load lead lead (dynamic_trigger_dom)-->
@if(config('visibility.dynamic_load_modal'))
<a href="javascript:void(0)" id="dynamic-lead-content"
    class="show-modal-button reset-card-modal-form js-ajax-ux-request js-ajax-ux-request" data-toggle="modal"
    data-target="#cardModal" data-url="{{ url('/leads/'.request()->route('lead').'?ref=list') }}"
    data-loading-target="main-top-nav-bar"></a>
@endif

@endsection