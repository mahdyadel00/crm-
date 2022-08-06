@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form">

    <div class="form-group row">
        <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.order_articles_by')) }}</label>
        <div class="col-12">
            <select class="select2-basic form-control form-control-sm" id="settings_knowledgebase_article_ordering"
                name="settings_knowledgebase_article_ordering">
                <option value="name-asc"
                    {{ runtimePreselected('name-asc', $settings->settings_knowledgebase_article_ordering ?? '') }}>
                    {{ cleanLang(__('lang.article_title')) }} - ({{ cleanLang(__('lang.ascending_order')) }})</option>
                <option value="name-desc"
                    {{ runtimePreselected('name-desc', $settings->settings_knowledgebase_article_ordering ?? '') }}>
                    {{ cleanLang(__('lang.article_title')) }} - ({{ cleanLang(__('lang.descending_order')) }})</option>
                <option value="date-asc"
                    {{ runtimePreselected('date-asc', $settings->settings_knowledgebase_article_ordering ?? '') }}>
                    {{ cleanLang(__('lang.date_added')) }} - ({{ cleanLang(__('lang.ascending_order')) }})</option>
                    <option value="date-desc"
                    {{ runtimePreselected('date-desc', $settings->settings_knowledgebase_article_ordering ?? '') }}>
                    {{ cleanLang(__('lang.date_added')) }} - ({{ cleanLang(__('lang.descending_order')) }})</option>
            </select>
        </div>
    </div>

    <div>
        <!--settings documentation help-->
        <a href="https://growcrm.io/documentation/22-knowledgebase-settings/" target="_blank"
            class="btn btn-sm btn-info  help-documentation"><i class="ti-info-alt"></i> {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>
    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request"
            data-url="/settings/knowledgebase/settings" data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection