@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">
    <!--page heading-->
    <div class="row page-titles">
        <!-- Page Title & Bread Crumbs -->
        @include('misc.heading-crumbs')
        <!--Page Title & Bread Crumbs -->
    </div>
    <!--page heading-->

    <!-- thank you -->
    <div class="row">
        <div class="col-12">
            <div class="permision-denied">
                <img src="{{ url('/') }}/public/images/thank-you-payment.png" alt="Thank you" /> 
                <div class="x-message"><h1>{{ cleanLang(__('lang.thank_you')) }}</h1></div>
                <div class="x-sub-message p-t-10"><h4>{{ cleanLang(__('lang.your_payment_is_now_processing')) }}</h4></div>
            </div>
        </div>
    </div>
    <!--page content -->

</div>
<!--main content -->
@endsection