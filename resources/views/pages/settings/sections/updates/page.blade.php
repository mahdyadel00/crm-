@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<div class="p-t-40 p-b-40" id="updates-container">


    <!--no updates avialable-->
    <div class="updates-card m-t-10" id="updates-checking" data-url="{{ url('settings/updates/check') }}"
        data-type="form" data-form-id="updates-checking" data-ajax-type="post">
        <input type="hidden" name="licence_key" value="{{ config('system.settings_purchase_code') }}">
        <input type="hidden" name="ip_address" value="{{ request()->ip() }}">
        <input type="hidden" name="url" value="{{ url()->current() }}">
        <input type="hidden" name="current_version" value="{{ config('system.settings_version') }}">
        <input type="hidden" name="email" value="{{ auth()->user()->email }}">
        <input type="hidden" name="name" value="{{ auth()->user()->first_name.' '. auth()->user()->first_name }}">
        <div class="loading p-b-30 p-t-30"></div>
        <div class="x-message">
            <h2>{{ cleanLang(__('lang.checking_for_updates')) }}. {{ cleanLang(__('lang.please_wait')) }}</h2>
        </div>
    </div>


    <!--server error-->
    <div class="updates-card m-t-10 hidden" id="updates-server-error">
        <img src="{{ url('/') }}/public/images/server-communication-error.png"
            alt="{{ cleanLang(__('lang.error_communicating_updates_server')) }}" />
        <div class="x-message">
            <h3>{{ cleanLang(__('lang.error_communicating_updates_server')) }}</h3>
            <h4>{{ cleanLang(__('lang.try_again_later')) }}</h4>
            <h6>{{ cleanLang(__('lang.check_logs_for_details')) }}</h6>
        </div>
    </div>


    <!-- product code-->
    <div class="updates-card alert alert-warning hidden" id="updates-invalid-purchase-code">
        <h3 class="text-warning"><i class="fa fa-exclamation-triangle"></i> {{ cleanLang(__('lang.warning')) }}</h3>
        {{ cleanLang(__('lang.purchase_code_could_not_be_confirmed')) }}
        <div>
            <a href="{{ url('app/settings/general') }}">{{ cleanLang(__('lang.enter_product_code')) }}</a>
        </div>
    </div>


    <!--app version error-->
    <div class="updates-card m-t-10 hidden" id="updates-app-version-error">
        <img src="{{ url('/') }}/public/images/error-app-version.png"
            alt="{{ cleanLang(__('lang.error_communicating_updates_server')) }}" />
        <div class="x-message">
            <h3>{{ cleanLang(__('lang.app_version_could_not_be_veried')) }}</h3>
            <h4>{{ cleanLang(__('lang.please_contact_support')) }}</h4>
        </div>
    </div>




    <!--no updates avialable-->
    <div class="updates-card m-t-10 hidden" id="updates-none-available">
        <img src="{{ url('/') }}/public/images/no-download-avialble.png" alt="No updates available" />
        <div class="x-message m-t-5">
            <h3>{{ cleanLang(__('lang.no_updates_available')) }}</h3>
        </div>
        <div class="m-t-5">
            <h5>{{ cleanLang(__('lang.your_version')) }}: <span
                    class="label label-rounded label-info">v{{ config('system.settings_version') }}</span></h5>
        </div>
    </div>



    <!--updates avialable-->
    <div class="updates-card m-t-10 hidden" id="updates-available">
        <img src="{{ url('/') }}/public/images/download-available.png" alt="updates available" />
        <div class="m-t-20">
            <h3>{{ cleanLang(__('lang.new_updates_available')) }}</h3>
        </div>
        <div class="m-t-10">
            <h5>{{ cleanLang(__('lang.your_version')) }}: <span
                    class="label label-rounded label-info">v{{ config('system.settings_version') }}</span> ---- {{ cleanLang(__('lang.new_version')) }}: <span class="label label-rounded label-success"
                    id="updated-current-version">x</span></h5>
        </div>
        <div class="m-t-20">
            <a class="btn waves-effect waves-light btn-rounded-x btn-danger" href="javascript:void(0)"
            id="updated-download-link">{{ cleanLang(__('lang.download_updates')) }}</a>
        </div>

        <div class="p-t-30">
            <!--settings documentation help-->
            <a href="https://growcrm.io/documentation/3-installing-updates/"  target="_blank" class="btn btn-sm btn-info"><i class="ti-info-alt"></i>
                {{ cleanLang(__('lang.how_to_install_updates')) }}</a>
        </div>
    </div>
</div>
@endsection