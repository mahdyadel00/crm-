<!--project-->

<div class="form-group row m-t--30 m-b-45">
    <label class="col-sm-12  col-lg-3 text-left control-label col-form-label required">@lang('lang.title')*</label>
    <div class="col-sm-12 col-lg-9">
        <input type="text" class="form-control form-control-sm" id="task_title" name="task_title"
            value="{{ $task->task_title }}">
    </div>
</div>



<div class="modal-selector p-t-30 p-b-1 m-b-30">
    <!--project-->
    <div class="form-group row">
        <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.project')*</label>
        <div class="col-sm-12 col-lg-9">
            <select
                class="projects_and_milestones_toggle form-control form-control-sm js-select2-basic-search-modal select2-hidden-accessible"
                id="project_id" name="project_id" data-milestones-dropdown="task_milestoneid"
                data-ajax--url="{{ url('/') }}/feed/clone-task-projects">
                <option value="{{ $task->project_id }}">{{ $task->project_title }}</option>
            </select>
        </div>
    </div>

    <!--milestone-->
    <div class="form-group row">
        <label
            class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.milestone')*</label>
        <div class="col-sm-12 col-lg-9">
            <select class="select2-basic form-control form-control-sm" id="task_milestoneid" name="task_milestoneid">
                <option value="{{ $task->milestone_id }}">{{ $task->milestone_title }}</option>
            </select>
        </div>
    </div>


    <!--task status-->
    <div class="form-group row">
        <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.status')*</label>
        <div class="col-sm-12 col-lg-9">
            <select class="select2-basic form-control form-control-sm select2-preselected" id="task_status"
                name="task_status" data-preselected="{{ $task->task_status }}">
                @foreach(config('task_statuses') as $task_status)
                <option value="{{ $task_status->taskstatus_id }}">{{ runtimeLang($task_status->taskstatus_title) }}</option>
                @endforeach
            </select>
        </div>
    </div>

</div>

<!--more information - toggle-->
<div class="spacer row">
    <div class="col-sm-12 col-lg-8">
        <span class="title">@lang('lang.more_information')</span>
    </div>
    <div class="col-sm-12 col-lg-4">
        <div class="switch  text-right">
            <label>
                <input type="checkbox" name="more_information" id="more_information"
                    class="js-switch-toggle-hidden-content" data-target="toogle_more_information">
                <span class="lever switch-col-light-blue"></span>
            </label>
        </div>
    </div>
</div>
<!--more information-->
<div class="hidden" id="toogle_more_information">

    <div class="line"></div>

    <!--copy checklists-->
    <div class="form-group form-group-checkbox row">
        <label class="col-10 col-form-label text-left">@lang('lang.copy_checklists')</label>
        <div class="col-2 text-right" style="padding-top:5px;">
            <input type="checkbox" id="copy_checklist" name="copy_checklist" class="filled-in chk-col-light-blue"
                checked>
            <label class="p-l-30" for="copy_checklist"></label>
        </div>
    </div>


    <!--copy files-->
    <div class="form-group form-group-checkbox row">
        <label class="col-10 col-form-label text-left">@lang('lang.copy_files')</label>
        <div class="col-2 text-right" style="padding-top:5px;">
            <input type="checkbox" id="copy_files" name="copy_files" class="filled-in chk-col-light-blue" checked>
            <label class="p-l-30" for="copy_files"></label>
        </div>
    </div>

    <div class="line"></div>

</div>

<!--notes-->
<div class="row">
    <div class="col-12">
        <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
    </div>
</div>