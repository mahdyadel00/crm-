@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid {{ $page['mode'] ?? '' }}" id="invoice-container">

    <!--HEADER SECTION-->
    <div class="row page-titles">

        <!--BREAD CRUMBS & TITLE-->
        <div class="col-md-12 col-lg-7 align-self-center {{ $page['crumbs_special_class'] ?? '' }}" id="breadcrumbs">
            <!--attached to project-->
            <a id="InvoiceTitleAttached"
                class="{{ runtimeInvoiceAttachedProject('project-title', $bill->bill_projectid) }}"
                href="{{ _url('projects/'.$bill->bill_projectid) }}">
                <h3 class="text-themecolor" id="InvoiceTitleProject">{{ $page['heading'] ?? '' }}</h3>
            </a>
            <!--not attached to project-->
            <h4 id="InvoiceTitleNotAttached"
                class="muted {{ runtimeInvoiceAttachedProject('alternative-title', $bill->bill_projectid) }}">{{ cleanLang(__('lang.not_attached_to_project')) }}</h4>
            <!--crumbs-->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">{{ cleanLang(__('lang.app')) }}</li>
                @if(isset($page['crumbs']))
                @foreach ($page['crumbs'] as $title)
                <li class="breadcrumb-item @if ($loop->last) active active-bread-crumb @endif">{{ $title ?? '' }}</li>
                @endforeach
                @endif
            </ol>
            <!--crumbs-->
        </div>

        <!--ACTIONS-->
        @if($bill->bill_type == 'invoice')
        @include('pages.bill.components.misc.invoice.actions')
        @endif
        @if($bill->bill_type == 'estimate')
        @include('pages.bill.components.misc.estimate.actions')
        @endif

    </div>
    <!--/#HEADER SECTION-->

    <!--BILL CONTENT-->
    <div class="row">
        <div class="col-md-12 p-t-30">
            @include('pages.bill.bill-web')
        </div>
    </div>
</div>
<!--main content -->

@endsection