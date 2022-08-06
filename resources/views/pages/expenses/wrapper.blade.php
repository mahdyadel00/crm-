@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->


        <!-- action buttons -->
        @include('pages.expenses.components.misc.list-page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->

    <!--stats panel-->
    @if(auth()->user()->is_team)
    <div class="stats-wrapper" id="expenses-stats-wrapper">
        @include('misc.list-pages-stats')
    </div>
    @endif
    <!--stats panel-->


    <!-- page content -->
    <div class="row">
        <div class="col-12">
            <!--expenses table-->
            @include('pages.expenses.components.table.wrapper')
            <!--expenses table-->
        </div>
    </div>
    <!--page content -->

</div>
<!--main content -->

<!--dynamic load expense expense (dynamic_trigger_dom) -->
@if(config('visibility.dynamic_load_modal'))
<a href="javascript:void(0)" id="dynamic-expense-content"
    class="show-modal-button edit-add-modal-button js-ajax-ux-request reset-target-modal-form" data-toggle="modal" data-modal-title="{{ cleanLang(__('lang.expense_records')) }}"
    data-target="#plainModal" data-url="{{ url('/expenses/'.request()->route('expense').'?ref=list') }}"
    data-loading-target="plainModalBody"></a>
@endif
@endsection