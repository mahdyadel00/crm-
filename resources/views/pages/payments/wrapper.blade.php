@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->


        <!-- action buttons -->
        @include('pages.payments.components.misc.list-page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->

    <!--stats panel-->
    @if(auth()->user()->is_team)
    <div class="stats-wrapper" id="payments-stats-wrapper">
    @include('misc.list-pages-stats')
    </div>
    @endif
    <!--stats panel-->

    <!-- page content -->
    <div class="row">
        <div class="col-12">
            <!--payments table-->
            @include('pages.payments.components.table.wrapper')
            <!--payments table-->
        </div>
    </div>
    <!--page content -->

</div>
<!--main content -->
<!--dynamic load payment payment (dynamic_trigger_dom)-->
@if(config('visibility.dynamic_load_modal'))
<a href="javascript:void(0)" id="dynamic-payment-content"
    class="show-modal-button edit-add-modal-button js-ajax-ux-request reset-target-modal-form" data-toggle="modal" data-modal-title="{{ cleanLang(__('lang.payment')) }}"
    data-target="#plainModal" data-url="{{ url('/payments/'.request()->route('payment').'?ref=list') }}"
    data-loading-target="plainModalBody"></a>
@endif
@endsection