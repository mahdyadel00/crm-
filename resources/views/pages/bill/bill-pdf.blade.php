<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" id="meta-csrf" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ config('system.settings_company_name') }}</title>


    <!--
        web preview example
        http://example.com/invoices/29/pdf?view=preview
        {{ BASE_DIR.'/' }}
    -->

    @if(request('view') == 'preview')
    <base href="{{ url('/') }}" target="_self">
    <link href="public/vendor/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    @else
    <base href="" target="_self">
    <link href="{{ BASE_DIR }}/public/vendor/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    @endif

    <!-- [DYNAMIC] style sets dynamic paths to font files-->
    <style>
        @font-face {
            font-family: 'DejaVuSans';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path("app/DejaVuSans.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DejaVuSans';
            font-style: normal;
            font-weight: 400;
            src: url('{{ storage_path("app/DejaVuSans.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DejaVuSans';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path("app/DejaVuSans-Bold.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DejaVuSans';
            font-style: normal;
            font-weight: 600;
            src: url('{{ storage_path("app/DejaVuSans-Bold.ttf") }}') format("truetype");
        }
    </style>



@if(request('view') == 'preview')
<link href="{{ config('theme.selected_theme_pdf_css') }}" rel="stylesheet">
@else
<link href="{{ BASE_DIR }}/{{ config('theme.selected_theme_pdf_css') }}" rel="stylesheet">
@endif

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="public/images/favicon.png">
</head>

<body class="pdf-page">

    <div class="bill-pdf {{ config('css.bill_mode') }} {{ @page['bill_mode'] }}">

        <!--HEADER-->
        <div class="bill-header">
            <!--INVOICE HEADER-->
            @if($bill->bill_type =='invoice')
            <table>
                <tbody>
                    <tr>
                        <td class="x-left">
                            <div class="x-logo">
                                <img
                                    src="{{ BASE_DIR }}/storage/logos/app/{{ config('system.settings_system_logo_large_name') }}">
                            </div>
                        </td>
                        <td class="x-right">
                            <div class="x-bill-type">
                                <!--draft-->
                                <span
                                    class="js-invoice-statuses {{ runtimeInvoiceStatus('draft', $bill->bill_status) }}"
                                    id="invoice-status-draft">
                                    <h2
                                        class="text-uppercase {{ runtimeInvoiceStatusColors($bill->bill_status, 'text') }} muted">
                                        {{ cleanLang(__('lang.draft')) }}</h2>
                                </span>
                                <!--due-->
                                <span class="js-invoice-statuses {{ runtimeInvoiceStatus('due', $bill->bill_status) }}"
                                    id="invoice-status-due">
                                    <h2
                                        class="text-uppercase {{ runtimeInvoiceStatusColors($bill->bill_status, 'text') }}">
                                        {{ cleanLang(__('lang.due')) }}</h2>
                                </span>
                                <!--overdue-->
                                <span
                                    class="js-invoice-statuses {{ runtimeInvoiceStatus('overdue', $bill->bill_status) }}"
                                    id="invoice-status-overdue">
                                    <h2
                                        class="text-uppercase {{ runtimeInvoiceStatusColors($bill->bill_status, 'text') }}">
                                        {{ cleanLang(__('lang.overdue')) }}</h2>
                                </span>
                                <!--paid-->
                                <span class="js-invoice-statuses {{ runtimeInvoiceStatus('paid', $bill->bill_status) }}"
                                    id="invoice-status-paid">
                                    <h2
                                        class="text-uppercase {{ runtimeInvoiceStatusColors($bill->bill_status, 'text') }}">
                                        {{ cleanLang(__('lang.paid')) }}</h2>
                                </span>
                            </div>
                            <div class="x-bill-type">
                                <h4><strong>{{ cleanLang(__('lang.invoice')) }}</strong></h4>
                                <h5>#{{ $bill->formatted_bill_invoiceid }}</h5>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            @endif
            <!--ESTIMATE HEADER-->
            @if($bill->bill_type =='estimate')
            <table>
                <tbody>
                    <tr>
                        <td class="x-left">
                            <div class="x-logo">
                                <img
                                    src="{{ BASE_DIR }}/storage/logos/app/{{ config('system.settings_system_logo_large_name') }}">
                            </div>
                        </td>
                        <td class="x-right">
                            <div class="x-bill-type">
                                <!--draft-->
                                <span
                                    class="js-estimate-statuses {{ runtimeEstimateStatus('draft', $bill->bill_status) }}"
                                    id="estimate-status-draft">
                                    <h2
                                        class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }} muted">
                                        {{ cleanLang(__('lang.draft')) }}</h2>
                                </span>
                                <!--new-->
                                <span
                                    class="js-estimate-statuses {{ runtimeEstimateStatus('new', $bill->bill_status) }}"
                                    id="estimate-status-new">
                                    <h2
                                        class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }}">
                                        {{ cleanLang(__('lang.new')) }}</h2>
                                </span>
                                <!--accepted-->
                                <span
                                    class="js-estimate-statuses {{ runtimeEstimateStatus('accepted', $bill->bill_status) }}"
                                    id="estimate-status-accpeted">
                                    <h2
                                        class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }}">
                                        {{ cleanLang(__('lang.accepted')) }}</h2>
                                </span>
                                <!--declined-->
                                <span
                                    class="js-estimate-statuses {{ runtimeEstimateStatus('declined', $bill->bill_status) }}"
                                    id="estimate-status-declined">
                                    <h2
                                        class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }}">
                                        {{ cleanLang(__('lang.declined')) }}</h2>
                                </span>
                                <!--revised-->
                                <span
                                    class="js-estimate-statuses {{ runtimeEstimateStatus('revised', $bill->bill_status) }}"
                                    id="estimate-status-revised">
                                    <h2
                                        class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }}">
                                        {{ cleanLang(__('lang.revised')) }}</h2>
                                </span>
                                <!--expired-->
                                <span
                                    class="js-estimate-statuses {{ runtimeEstimateStatus('expired', $bill->bill_status) }}"
                                    id="estimate-status-expired">
                                    <h2
                                        class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }}">
                                        {{ cleanLang(__('lang.expired')) }}</h2>
                                </span>
                            </div>
                            <div class="x-bill-type">
                                <h4><strong>{{ cleanLang(__('lang.estimate')) }}</strong></h4>
                                <h5>#{{ $bill->formatted_bill_estimateid }}</h5>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            @endif
        </div>

        <!--ADDRESSES & DATES-->
        <div class="bill-addresses">
            <table>
                <tbody>
                    <tr>
                        <!--company-->
                        <td class="x-left">
                            <div class="x-company-name">
                                <h5 class="p-b-0 m-b-0"><strong>{{ config('system.settings_company_name') }}</strong>
                                </h5>
                            </div>
                            @if(config('system.settings_company_address_line_1'))
                            <div class="x-line">{{ config('system.settings_company_address_line_1') }}
                            </div>
                            @endif
                            @if(config('system.settings_company_state'))
                            <div class="x-line">
                                {{ config('system.settings_company_state') }}
                            </div>
                            @endif
                            @if(config('system.settings_company_city'))
                            <div class="x-line">
                                {{ config('system.settings_company_city') }}
                            </div>
                            @endif
                            @if(config('system.settings_company_zipcode'))
                            <div class="x-line">
                                {{ config('system.settings_company_zipcode') }}
                            </div>
                            @endif
                            @if(config('system.settings_company_country'))
                            <div class="x-line">
                                {{ config('system.settings_company_country') }}
                            </div>
                            @endif

                            <!--custom company fields-->
                            @if(config('system.settings_company_customfield_1') != '')
                            <div class="x-line">
                                {{ config('system.settings_company_customfield_1') }}
                            </div>
                            @endif
                            @if(config('system.settings_company_customfield_2') != '')
                            <div class="x-line">
                                {{ config('system.settings_company_customfield_2') }}
                            </div>
                            @endif
                            @if(config('system.settings_company_customfield_3') != '')
                            <div class="x-line">
                                {{ config('system.settings_company_customfield_3') }}
                            </div>
                            @endif
                            @if(config('system.settings_company_customfield_4') != '')
                            <div class="x-line">
                                {{ config('system.settings_company_customfield_4') }}
                            </div>
                            @endif
                        </td>
                        <td></td>
                        <!--customer-->
                        <td class="x-right">
                            <div class="x-company-name">
                                <h5 class="p-b-0 m-b-0"><strong>{{ $bill->client_company_name }}</strong></h5>
                            </div>
                            @if($bill->client_billing_street)
                            <div class="x-line">
                                {{ $bill->client_billing_street }}
                            </div>
                            @endif
                            @if($bill->client_billing_city)
                            <div class="x-line">
                                {{ $bill->client_billing_city }}
                            </div>
                            @endif
                            @if($bill->client_billing_state)
                            <div class="x-line">
                                {{ $bill->client_billing_state }}
                            </div>
                            @endif
                            @if($bill->client_billing_zip)
                            <div class="x-line">
                                {{ $bill->client_billing_zip }}
                            </div>
                            @endif
                            @if($bill->client_billing_country)
                            <div class="x-line">
                                {{ $bill->client_billing_country }}
                            </div>
                            @endif

                            <!--custom fields-->
                            @foreach($customfields as $field)
                            @if($field->customfields_show_invoice == 'yes' && $field->customfields_status == 'enabled')
                            @php $key = $field->customfields_name; @endphp
                            @php $customfield = $bill[$key] ?? ''; @endphp
                            @if($customfield != '')
                            <div class="x-line">
                                {{ $field->customfields_title }}: {{ $customfield }}
                            </div>
                            @endif
                            @endif
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="bill-dates">
                <tbody>
                    <tr>
                        <td class="x-left">
                            @if($bill->bill_type == 'invoice')
                            @include('pages.bill.components.elements.invoice.dates')
                            @endif
                            @if($bill->bill_type == 'estimate')
                            @include('pages.bill.components.elements.estimate.dates')
                            @endif
                        </td>
                        <td class="x-right">
                            @if($bill->bill_type == 'invoice')
                            @include('pages.bill.components.elements.invoice.payments')
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <!--DATES & AMOUNT DUE-->




        <!--INVOICE TABLE-->
        <div class="bill-table-pdf">
            @include('pages.bill.components.elements.main-table')
        </div>

        <!-- TOTAL & SUMMARY -->
        <div class="bill-totals-table-pdf">
            @include('pages.bill.components.elements.totals-table')
        </div>

        <!--TERMS-->
        <div class="invoice-pdf-terms">
            <h6><strong>{{ cleanLang(__('lang.terms')) }}</strong></h6>
            {!! clean($bill->bill_terms) !!}
        </div>
    </div>
</body>

</html>