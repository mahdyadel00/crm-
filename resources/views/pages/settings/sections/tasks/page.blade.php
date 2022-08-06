@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form">

    <h5>{{ cleanLang(__('lang.kanban_board_settings')) }}</h5>
    <div class="line"></div>
    <div class="p-b-20">{{ cleanLang(__('lang.kanban_card_front_settings_info')) }}.</div>


    <!--show project title-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.project_title')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_project_title" name="settings_tasks_kanban_project_title"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_tasks_kanban_project_title'] ?? '') }}>
            <label for="settings_tasks_kanban_project_title"></label>
        </div>
    </div>

    <!--show client name-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.client_name')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_client_name" name="settings_tasks_kanban_client_name"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_tasks_kanban_client_name'] ?? '') }}>
            <label for="settings_tasks_kanban_client_name"></label>
        </div>
    </div>

    <!--show date created-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.date_created')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_date_created" name="settings_tasks_kanban_date_created"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_tasks_kanban_date_created'] ?? '') }}>
            <label for="settings_tasks_kanban_date_created"></label>
        </div>
    </div>

    <!--show due date-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.due_date')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_date_due" name="settings_tasks_kanban_date_due"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_tasks_kanban_date_due'] ?? '') }}>
            <label for="settings_tasks_kanban_date_due"></label>
        </div>
    </div>
    <!--show start date-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.start_date')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_date_start" name="settings_tasks_kanban_date_start"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_tasks_kanban_date_start'] ?? '') }}>
            <label for="settings_tasks_kanban_date_start"></label>
        </div>
    </div>
    <!--show priority-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.task_priority')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_priority" name="settings_tasks_kanban_priority"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_tasks_kanban_priority'] ?? '') }}>
            <label for="settings_tasks_kanban_priority"></label>
        </div>
    </div>
    <!--show client visibility-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.client_visibility')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_client_visibility"
                name="settings_tasks_kanban_client_visibility" class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_tasks_kanban_client_visibility'] ?? '') }}>
            <label for="settings_tasks_kanban_client_visibility"></label>
        </div>
    </div>

    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request" data-url="/settings/tasks"
            data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection