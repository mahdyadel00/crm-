@extends('layout.wrapperplain') @section('content')
<!--signup-->
<div class="login-logo m-t-30 p-b-5">
    <a href="javascript:void(0)" class="text-center db">
        <img src="{{ runtimeLogoLarge() }}" alt="Home">
    </a>
</div>

<div class="login-box m-t-20">
    <form class="form-horizontal form-material" id="loginForm">
        <div class="title">
            <h4 class="box-title m-t-10 text-center">{{ cleanLang(__('lang.sign_in_to_your_account')) }}</h4>
            <div class="text-center  m-b-20 ">
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12">
                <input class="form-control" type="text" name="email" id="email" placeholder="{{ cleanLang(__('lang.email')) }}">
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12">
                <input class="form-control" type="password" name="password" id="password" placeholder="{{ cleanLang(__('lang.password')) }}">
            </div>
        </div>
        <div class="form-group">
            <label class="custom-control custom-checkbox cursor-pointer">
                <input type="checkbox" class="custom-control-input" name="remember_me" checked="checked">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">{{ cleanLang(__('lang.remember_me')) }}</span>
            </label>
        </div>
        <div class="form-group row p-t-10 p-b-10">
            <div class="col-md-12">
                <a href="{{ url('forgotpassword') }}" id="to-recover" class="text-dark pull-right js-toggle-login-forms"
                    data-target="login-forms-forgot">
                    <i class="fa fa-lock m-r-5"></i> {{ cleanLang(__('lang.forgot_password')) }}</a>
            </div>
        </div>
        <div class="form-group text-center p-b-10">
            <div class="col-xs-12">
                <button class="btn btn-info btn-lg btn-block" id="loginSubmitButton"
                    data-button-loading-annimation="yes" data-button-disable-on-click="yes"
                    data-url="{{ url('login?action=initial') }}" data-ajax-type="POST" type="submit">{{ cleanLang(__('lang.continue')) }}</button>
            </div>
        </div>
        @if(config('system.settings_clients_registration') == 'enabled')
        <div class="form-group m-b-0">
            <div class="col-sm-12 text-center">
                {{ cleanLang(__('lang.dont_have_an_account')) }}
                <a href="{{ url('signup') }}" class="text-info m-l-5 js-toggle-login-forms"
                    data-target="login-forms-signup">
                    <b>{{ cleanLang(__('lang.sign_up')) }}</b>
                </a>
                </p>
            </div>
        </div>
        @endif
    </form>
</div>

<div class="login-background">
    <div class="x-left">
        <img src="{{ url('/') }}/public/images/login-1.png"  class="login-images" />
    </div>
    <div class="x-right hidden">
        <img src="{{ url('/') }}/public/images/login-2.png" alt="404 - Not found" />
    </div>
</div>
<!--signup-->
@endsection