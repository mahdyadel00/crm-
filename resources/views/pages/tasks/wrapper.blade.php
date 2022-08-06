@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->


        <!-- action buttons -->
        @include('pages.tasks.components.misc.list-page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->

    <!--stats panel-->
    @if(auth()->user()->is_team)
    <div class="stats-wrapper " id="tasks-stats-wrapper">
        @include('pages.tasks.components.misc.list-pages-stats')
    </div>
    @endif
    <!--stats panel-->


    <!-- page content -->
    <div class="row kanban-wrapper">
        <div class="col-12" id="tasks-layout-wrapper">
            @if(auth()->user()->pref_view_tasks_layout == 'kanban')
            @include('pages.tasks.components.kanban.wrapper')
            @else
            <!--tasks table-->
            @include('pages.tasks.components.table.wrapper')
            <!--tasks table-->
            @endif
            <!--filter-->
            @if(auth()->user()->is_team)
            @include('pages.tasks.components.misc.filter-tasks')
            @endif
            <!--filter-->
        </div>
    </div>
    <!--page content -->

</div>
<!--main content -->

<!--task modal-->
@include('pages.task.modal')

<!--dynamic load task task (dynamic_trigger_dom)-->
@if(config('visibility.dynamic_load_modal'))
<a href="javascript:void(0)" id="dynamic-task-content"
    class="show-modal-button reset-card-modal-form js-ajax-ux-request js-ajax-ux-request" data-toggle="modal"
    data-target="#cardModal" data-url="{{ url('/tasks/'.request()->route('task').'?ref=list') }}"
    data-loading-target="main-top-nav-bar"></a>
@endif

@endsection