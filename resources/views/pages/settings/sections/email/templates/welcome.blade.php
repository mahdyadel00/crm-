@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form">


    <!--welcome-->
    <div class="row">
        <div class="col-12">
            <div class="page-notification-imaged">
                <img src="{{ url('/') }}/public/images/email.png" alt="Application Settings" />
                <div class="message">
                    <h4>{{ cleanLang(__('lang.select_email_template_from_dropdown')) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!--select dropdown-->
    <div id="list-page-actions" class="hidden pull-right w-px-400 select-email-template-dropdown"
        id="fx-settings-emailtemplates-dropdown">
        <form id="fix-form-email-templates">
            <select class="select2-basic form-control form-control-sm text-left" data-url="" id="selectEmailTemplate"
                name="selectEmailTemplate">
                <option value="0">@lang('lang.select_a_template')</option>
                <!--contract [hidden]--
                <optgroup class="hidden" label="[ {{ cleanLang(__('lang.contracts')) }} ]">
                    @foreach($contracts as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
            -->
                <!--estimates-->
                <optgroup label="[ {{ cleanLang(__('lang.estimates')) }} ]">
                    @foreach($estimates as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
                <!--financial-->
                <optgroup label="[ {{ cleanLang(__('lang.financial')) }} ]">
                    @foreach($billing as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
                <!--leads-->
                <optgroup label="[ {{ cleanLang(__('lang.leads')) }} ]">
                    @foreach($leads as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
                <!--projects-->
                <optgroup label="[ {{ cleanLang(__('lang.projects')) }} ]">
                    @foreach($projects as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
                <!--propsoal-->
                <optgroup label="[ {{ cleanLang(__('lang.proposals')) }} ]">
                    @foreach($proposals as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
                <!--subscriptions-->
                <optgroup label="[ {{ cleanLang(__('lang.subscriptions')) }} ]">
                    @foreach($subscriptions as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
                <!--system-->
                <optgroup label="[ {{ cleanLang(__('lang.system')) }} ]">
                    @foreach($system as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
                <!--tasks-->
                <optgroup label="[ {{ cleanLang(__('lang.tasks')) }} ]">
                    @foreach($tasks as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
                <!--tickets-->
                <optgroup label="[ {{ cleanLang(__('lang.tickets')) }} ]">
                    @foreach($tickets as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
                <!--users-->
                <optgroup label="[ {{ cleanLang(__('lang.users')) }} ]">
                    @foreach($users as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
                <!--other-->
                <optgroup label="[ {{ cleanLang(__('lang.other')) }} ]">
                    @foreach($other as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLang($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
                <!--modules-->
                <optgroup label="[ {{ cleanLang(__('lang.modules')) }} ]">
                    @foreach($modules as $template)
                    <option value="{{ url('settings/email/templates/'.$template->emailtemplate_id) }}">
                        {{ runtimeLangModules($template->emailtemplate_lang) }}
                        {{ runtimeEmailTemplates($template->emailtemplate_type) }}
                    </option>
                    @endforeach
                </optgroup>
            </select>
        </form>
    </div>
</form>
@endsection