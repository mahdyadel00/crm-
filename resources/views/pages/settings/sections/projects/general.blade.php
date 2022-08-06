@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form" id="settingsFormProjects">
    <!--form text tem-->
    <div class="form-group row">
        <label class="col-4 control-label col-form-label">{{ cleanLang(__('lang.default_hourly_rate')) }}</label>
        <div class="col-3">
            <input type="number" class="form-control form-control-sm" id="settings_projects_default_hourly_rate"
                name="settings_projects_default_hourly_rate"
                value="{{ $settings->settings_projects_default_hourly_rate ?? '' }}">
        </div>
    </div>

    <!--settings_projects_cover_images-->
    <div class="form-group row">
        <label class="col-4 control-label col-form-label">@lang('lang.project_cover_images_feature')</label>
        <div class="col-3">
            <select class="select2-basic form-control form-control-sm select2-preselected"
                id="settings_projects_cover_images" name="settings_projects_cover_images"
                data-preselected="{{ $settings->settings_projects_cover_images ?? ''}}">
                <option value="enabled">@lang('lang.enabled')</option>
                <option value="disabled">@lang('lang.disabled')</option>
            </select>
        </div>
    </div>


    <!--show project categories in main menu-->
    <div class="form-group row">
        <label class="col-4 control-label col-form-label">@lang('lang.show_project_categories_main_menu')</label>
        <div class="col-3">
            <select class="select2-basic form-control form-control-sm select2-preselected"
                id="settings_projects_categories_main_menu" name="settings_projects_categories_main_menu"
                data-preselected="{{ $settings->settings_projects_categories_main_menu ?? ''}}">
                <option value="yes">@lang('lang.yes')</option>
                <option value="no">@lang('lang.no')</option>
            </select>
        </div>
    </div>



    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton" class="btn btn-rounded-x btn-danger waves-effect text-left"
            data-url="/settings/projects/general" data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>


    <div>
        <!--settings documentation help-->
        <a href="https://growcrm.io/documentation/project-settings/" target="_blank"
            class="btn btn-sm btn-info help-documentation"><i class="ti-info-alt"></i>
            {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>

</form>
@endsection