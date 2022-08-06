<!--title<>-->
<div class="form-group row">
    <label
        class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.project_title')) }}*</label>
    <div class="col-sm-12 col-lg-9">
        <input type="text" class="form-control form-control-sm" id="project_title" name="project_title" placeholder=""
            value="{{ $project->project_title ?? '' }}">
    </div>
</div>
<!--/#title-->

<!--category-->
<div class="form-group row">
    <label for="example-month-input"
        class="col-sm-12 col-lg-3 col-form-label text-left required">{{ cleanLang(__('lang.category')) }}</label>
    <div class="col-sm-12 col-lg-9">
        <select class="select2-basic form-control form-control-sm" id="project_categoryid" name="project_categoryid">
            @foreach($categories as $category)
            <option value="{{ $category->category_id }}"
                {{ runtimePreselected($project->project_categoryid ?? '', $category->category_id) }}>{{
                                    runtimeLang($category->category_name) }}</option>
            @endforeach
        </select>
    </div>
</div>

<!--CUSTOMER FIELDS [expanded]-->
@if(config('system.settings_customfields_display_projects') == 'expanded')
@include('misc.customfields')
@endif
<!--/#CUSTOMER FIELDS [expanded]-->

<!--project manager<>-->
<div class="form-group row hidden">
    <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.manager')) }}
        <a class="align-middle font-14 toggle-collapse" href="#project_manager_info" role="button"><i
                class="ti-info-alt text-themecontrast"></i></a></label>
    <div class="col-sm-12 col-lg-9">
        <select name="manager" id="manager" class="select2-basic form-control form-control-sm">
            <!--array of assigned-->
            @if(isset($page['section']) && $page['section'] == 'edit' && isset($project->managers))
            @foreach($project->managers as $user)
            @php $manager[] = $user->id; @endphp
            @endforeach
            @endif
            <!--/#array of assigned-->
            <!--users list-->
            @foreach(config('system.team_members') as $user)
            <option></option>
            <option value="{{ $user->id }}" {{ runtimePreselectedInArray($user->id ?? '', $manager ?? []) }}>{{
                                $user->full_name }}</option>
            @endforeach
            <!--/#users list-->
        </select>
    </div>
</div>

<!--assigned team members<>-->
<div class="form-group row hidden">
    <label
        class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.assigned')) }}</label>
    <div class="col-sm-12 col-lg-9">
        <select name="assigned" id="assigned"
            class="form-control form-control-sm select2-basic select2-multiple select2-tags select2-hidden-accessible"
            multiple="multiple" tabindex="-1" aria-hidden="true">
            <!--array of assigned-->
            @if(isset($page['section']) && $page['section'] == 'edit' && isset($project->assigned))
            @foreach($project->assigned as $user)
            @php $assigned[] = $user->id; @endphp
            @endforeach
            @endif
            <!--/#array of assigned-->
            <!--users list-->
            @foreach(config('system.team_members') as $user)
            <option value="{{ $user->id }}" {{ runtimePreselectedInArray($user->id ?? '', $assigned ?? []) }}>{{
                        $user->full_name }}</option>
            @endforeach
            <!--/#users list-->
        </select>
    </div>
</div>
<!--/#assigned team members-->

<!--edit description- toggle<>-->
<div class="spacer row">
    <div class="col-sm-8">
        <span class="title">Project {{ cleanLang(__('lang.description')) }}</span class="title">
    </div>
    <div class="col-sm-12 col-lg-4">
        <div class="switch  text-right">
            <label>
                <input type="checkbox" class="js-switch-toggle-hidden-content"
                    data-target="edit_project_description_toggle">
                <span class="lever switch-col-light-blue"></span>
            </label>
        </div>
    </div>
</div>
<div class="hidden" id="edit_project_description_toggle">
    <textarea id="project_description" name="project_description"
        class="tinymce-textarea">{{ $project->project_description ?? '' }}</textarea>
    <div class="line m-t-30"></div>
</div>
<!--edit description- toggle-->




<!--spacer-->
<div class="spacer row">
    <div class="col-sm-8">
        <span class="title">{{ cleanLang(__('lang.assigned_user_permissions')) }}</span>
    </div>
    <div class="col-sm-4">
        <div class="switch  text-right">
            <label>
                <input type="checkbox" name="show_more_settings_projects" id="show_more_settings_projects"
                    class="js-switch-toggle-hidden-content" data-target="edit_project_assigned_permissions">
                <span class="lever switch-col-light-blue"></span>
            </label>
        </div>
    </div>
</div>
<!--spacer-->
<!--option toggle-->
<div class="hidden highlighted-panel" id="edit_project_assigned_permissions">
    <div class="form-group form-group-checkbox row m-b-0">
        <label class="col-5 col-form-label text-left">{{ cleanLang(__('lang.task_collaboration')) }} <a
                class="align-middle font-16 toggle-collapse" href="#info_task_collaboration" role="button"><i
                    class="ti-info-alt text-themecontrast"></i></a> </label>
        <div class="col-7 text-left p-t-5">
            <input type="checkbox" id="assignedperm_tasks_collaborate" name="assignedperm_tasks_collaborate"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($project['assignedperm_tasks_collaborate'] ?? '') }}>
            <label for="assignedperm_tasks_collaborate"></label>
        </div>
    </div>
    <!--info: taskcollaborations-->
    <div class="collapse" id="info_task_collaboration">
        <div class="alert alert-info">{{ cleanLang(__('lang.task_collaboration_info')) }}</div>
    </div>
</div>
<!--option toggle-->





<!--spacer-->
<div class="spacer row">
    <div class="col-sm-8">
        <span class="title">{{ cleanLang(__('lang.client_project_permissions')) }}</span class="title">
    </div>
    <div class="col-sm-4">
        <div class="switch text-right">
            <label>
                <input type="checkbox" name="show_more_settings_projects2" id="show_more_settings_projects2"
                    class="js-switch-toggle-hidden-content" data-target="edit_project_permissions_tasks">
                <span class="lever switch-col-light-blue"></span>
            </label>
        </div>
    </div>
</div>
<!--spacer-->
<!--permissions-->
<div id="edit_project_permissions_tasks" class="hidden highlighted-panel">
    <!--permission - view tasks-->
    <div class="form-group form-group-checkbox row">
        <label class="col-5 col-form-label text-left">{{ cleanLang(__('lang.view_tasks')) }}</label>
        <div class="col-7 text-left p-t-5">
            <input type="checkbox" id="clientperm_tasks_view" name="clientperm_tasks_view"
                class="filled-in chk-col-light-blue" {{ runtimePrechecked($project['clientperm_tasks_view'] ?? '') }}>
            <label for="clientperm_tasks_view"></label>
        </div>
    </div>
    <!--permission - task participation-->
    <div class="form-group form-group-checkbox row">
        <label class="col-5 col-form-label text-left required">{{ cleanLang(__('lang.task_participation')) }}**</label>
        <div class="col-7 text-left p-t-5">
            <input type="checkbox" id="clientperm_tasks_collaborate" name="clientperm_tasks_collaborate"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($project['clientperm_tasks_collaborate'] ?? '') }}>
            <label for="clientperm_tasks_collaborate"></label>
        </div>
    </div>
    <!--permission - create tasks-->
    <div class="form-group form-group-checkbox row">
        <label class="col-5 col-form-label text-left required">{{ cleanLang(__('lang.create_tasks')) }}**</label>
        <div class="col-7 text-left p-t-5">
            <input type="checkbox" id="clientperm_tasks_create" name="clientperm_tasks_create"
                class="filled-in chk-col-light-blue" {{ runtimePrechecked($project['clientperm_tasks_create'] ?? '') }}>
            <label for="clientperm_tasks_create"></label>
        </div>
    </div>
    <div class="line"></div>
    <!--permission - view timesheets-->
    <div class="form-group form-group-checkbox row">
        <label class="col-5 col-form-label text-left">{{ cleanLang(__('lang.view_time_sheets')) }}</label>
        <div class="col-7 text-left p-t-5">
            <input type="checkbox" id="clientperm_timesheets_view" name="clientperm_timesheets_view"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($project['clientperm_timesheets_view'] ?? '') }}>
            <label for="clientperm_timesheets_view"></label>
        </div>
    </div>
    <!--permission - view expenses-->
    <div class="form-group form-group-checkbox row">
        <label class="col-5 col-form-label text-left">{{ cleanLang(__('lang.view_expenses')) }}</label>
        <div class="col-7 text-left p-t-5">
            <input type="checkbox" id="clientperm_expenses_view" name="clientperm_expenses_view"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($project['clientperm_expenses_view'] ?? '') }}>
            <label for="clientperm_expenses_view"></label>
        </div>
    </div>

    <div><small class="text-bold">** {{ cleanLang(__('lang.if_items_selected_then_viewing_perm')) }}</small>
    </div>
</div>
<!--permissions-->



<!--project options-->
<div class="spacer row">
    <div class="col-sm-8">
        <span class="title">{{ cleanLang(__('lang.project_billing')) }}</span class="title">
    </div>
    <div class="col-sm-4 text-right">
        <div class="switch">
            <label>
                <input type="checkbox" class="js-switch-toggle-hidden-content" data-target="edit_project_billing">
                <span class="lever switch-col-light-blue"></span>
            </label>
        </div>
    </div>
</div>

<div class="hidden" id="edit_project_billing">
    <div class="highlighted-panel">
        <!--billing type-->
        <div class="form-group row">
            <label for="example-month-input"
                class="col-sm-12 col-lg-4 col-form-label text-left">{{ cleanLang(__('lang.billing')) }}</label>
            <div class="col-sm-12 col-lg-4">
                <input type="number" class="form-control form-control-sm" id="project_billing_rate"
                    name="project_billing_rate" placeholder="" value="{{ $project['project_billing_rate'] ?? '' }}">

            </div>
            <div class="col-sm-12 col-lg-4">
                <select class="select2-basic form-control form-control-sm" id="project_billing_type"
                    data-allow-clear="false" name="project_billing_type">
                    <option value="hourly" {{ runtimePreselected( 'hourly', $project['project_billing_type'] ?? '') }}>
                        {{ cleanLang(__('lang.hourly')) }}</option>
                    <option value="fixed" {{ runtimePreselected( 'fixed', $project['project_billing_type'] ?? '') }}>
                        {{ cleanLang(__('lang.fixed_fee')) }}</option>
                </select>
            </div>
        </div>
        <!--estimated hours-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-4 text-left control-label col-form-label">{{ cleanLang(__('lang.estimated_hours')) }}
                <a class="align-middle font-16 toggle-collapse" href="#project_estimated_hours_info" role="button"><i
                        class="ti-info-alt text-themecontrast"></i></a></label>
            <div class="col-sm-12 col-lg-4">
                <input type="number" class="form-control form-control-sm" id="project_billing_estimated_hours"
                    name="project_billing_estimated_hours" placeholder=""
                    value="{{ $project['project_billing_estimated_hours'] ?? '' }}">
            </div>
            <div class="collapse col-sm-12 m-t-15" id="project_estimated_hours_info">
                <div class="alert alert-info">{{ cleanLang(__('lang.project_estimated_hours_info')) }}</div>
            </div>
        </div>
        <!--cost estimate-->
        <div class="form-group row m-b-0">
            <label
                class="col-sm-12 col-lg-4 text-left control-label col-form-label">{{ cleanLang(__('lang.costs_estimate')) }}
                <a class="align-middle font-16 toggle-collapse" href="#project_cost_estimate_info" role="button"><i
                        class="ti-info-alt text-themecontrast"></i></a></label>
            <div class="col-sm-12 col-lg-4">
                <input type="number" class="form-control form-control-sm" id="project_billing_costs_estimate"
                    name="project_billing_costs_estimate" placeholder=""
                    value="{{ $project['project_billing_costs_estimate'] ?? '' }}">
            </div>
            <div class="collapse col-sm-12 m-t-15" id="project_cost_estimate_info">
                <div class="alert alert-info">{{ cleanLang(__('lang.project_cost_estimate_info')) }}</div>
            </div>
        </div>
    </div>
</div>


<!--CUSTOMER FIELDS [collapsed]-->
@if(config('system.settings_customfields_display_projects') == 'toggled')
<div class="spacer row">
    <div class="col-sm-12 col-lg-8">
        <span class="title">{{ cleanLang(__('lang.more_information')) }}</span class="title">
    </div>
    <div class="col-sm-12 col-lg-4">
        <div class="switch  text-right">
            <label>
                <input type="checkbox" name="add_client_option_other" id="add_client_option_other"
                    class="js-switch-toggle-hidden-content" data-target="projects_custom_fields_collaped">
                <span class="lever switch-col-light-blue"></span>
            </label>
        </div>
    </div>
</div>
<div id="projects_custom_fields_collaped" class="hidden">
    @if(config('app.application_demo_mode'))
    <!--DEMO INFO-->
    <div class="alert alert-info">
        <h5 class="text-info"><i class="sl-icon-info"></i> Demo Info</h5> 
        These are custom fields. You can change them or <a href="{{ url('app/settings/customfields/projects') }}">create your own.</a>
    </div>
    @endif
    
    @include('misc.customfields')
</div>
@endif
<!--/#CUSTOMER FIELDS [collapsed]-->

@if(config('visibility.project_show_project_option'))
<div class="form-group form-group-checkbox row">
    <div class="col-12 text-left p-t-5">
        <input type="checkbox" id="show_after_adding" name="show_after_adding" class="filled-in chk-col-light-blue"
            checked="checked">
        <label for="show_after_adding">{{ cleanLang(__('lang.show_project_after_its_created')) }}</label>
    </div>
</div>
@endif
<!--/#[billing details]-->