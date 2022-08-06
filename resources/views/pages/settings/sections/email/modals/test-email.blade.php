<div id="test-email-form">
    @if($show == 'form')
    <div class="splash-image">
        <img src="{{ url('/') }}/public/images/send-email.svg" alt="{{ cleanLang(__('lang.send_test_email')) }}" />
    </div>
    <div class="splash-text">
        <h4>{{ cleanLang(__('lang.send_test_email')) }}</h4>
    </div>

    <div class="form-group row">
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" id="email" name="email"
                placeholder="{{ cleanLang(__('lang.email_address')) }}">
        </div>
    </div>
    @else
    <div class="splash-image">
        <img src="{{ url('/') }}/public/images/general-error.png" alt="{{ cleanLang(__('lang.error')) }}" />
    </div>
    <div class="splash-text">
        <div class="alert alert-danger">{{ cleanLang(__('lang.cronjob_and_emails')) }}</div>
    </div>
    <div class="splash-text p-b-40">
        <a href="https://growcrm.io/documentation/cron-job-settings/" target="_blank"
            class="btn btn-sm btn-info  help-documentation"><i class="ti-info-alt"></i>
            {{ cleanLang(__('lang.see_documentation_for_details')) }}</a>
    </div>
    @endif
</div>