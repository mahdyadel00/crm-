<div class="row">
    <div class="col-lg-12">

        <!--repeat period-->
        <div class="form-group row">
            <label for="example-month-input"
                class="col-sm-12 col-lg-4 col-form-label text-left">{{ cleanLang(__('lang.repeat_every')) }}</label>

            <div class="col-sm-12 col-lg-3">
                <input type="number" class="form-control form-control-sm" id="task_recurring_duration"
                    name="task_recurring_duration" value="{{ $task->task_recurring_duration ?? 1}}">
            </div>
            <div class="col-5">
                <select class="select2-basic form-control form-control-sm" id="task_recurring_period"
                    name="task_recurring_period">
                    <option value="month" {{ runtimePreselected($task->task_recurring_period ?? '', 'month') }}>
                        {{ cleanLang(__('lang.month_months')) }}</option>
                    <option value="day" {{ runtimePreselected($task->task_recurring_period ?? '', 'day') }}>
                        {{ cleanLang(__('lang.days')) }}
                    </option>
                    <option value="week" {{ runtimePreselected($task->task_recurring_period ?? '', 'week') }}>
                        {{ cleanLang(__('lang.week_weeks')) }}</option>
                    <option value="year" {{ runtimePreselected($task->task_recurring_period ?? '', 'year') }}>
                        {{ cleanLang(__('lang.year_years')) }}</option>
                </select>
            </div>

        </div>


        <!--repeat cycle-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-4 text-left control-label col-form-label">{{ cleanLang(__('lang.cycles')) }}</label>
            <div class="col-sm-12 col-lg-3">
                <input type="number" class="form-control form-control-sm" id="task_recurring_cycles"
                    name="task_recurring_cycles" value="{{ $task->task_recurring_cycles ?? 0}}">
            </div>
            <div class="col-sm-12 col-lg-3">
                <!--info tooltip-->
                <div class="fx-info-tool-tip">
                    <span class="align-middle text-themecontrast font-16" data-toggle="tooltip"
                        title="{{ cleanLang(__('lang.task_recurring_period_info')) }}" data-placement="top"><i
                            class="ti-info-alt"></i></span>
                </div>
            </div>
        </div>

        <!--next cycle date-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-4 text-left control-label col-form-label">{{ cleanLang(__('lang.first_task_date')) }}</label>
            <div class="col-sm-12 col-lg-3">
                @if(isset($task['task_recurring']) && $task['task_recurring'] == 'yes')
                <input type="text" class="form-control form-control-sm pickadate" name="task_recurring_next"
                    autocomplete="off" value="{{ runtimeDatepickerDate($task->task_recurring_next ?? '') }}">
                <input class="mysql-date" type="hidden" name="task_recurring_next" id="task_recurring_next"
                    value="{{ $task->task_recurring_next ?? '' }}">
                @else
                <input type="text" class="form-control form-control-sm pickadate" name="task_recurring_next"
                    autocomplete="off" value="">
                <input class="mysql-date" type="hidden" name="task_recurring_next" id="task_recurring_next" value="">
                @endif
            </div>
            <div class="col-sm-12 col-lg-3">
                <!--info tooltip-->
                <div class="fx-info-tool-tip">
                    <span class="align-middle text-themecontrast font-16" data-toggle="tooltip"
                        title="{{ cleanLang(__('lang.task_recurring_cycles_explanation')) }}" data-placement="top"><i
                            class="ti-info-alt"></i></span>
                </div>
            </div>
        </div>

        <div class="line"></div>

        <!--copy checklists-->
        <div class="form-group form-group-checkbox row">
            <label class="col-4 col-form-label text-left">@lang('lang.copy_checklists')</label>
            <div class="col-8 text-left" style="padding-top:5px;">
                <input type="checkbox" id="task_recurring_copy_checklists" name="task_recurring_copy_checklists"
                    class="filled-in chk-col-light-blue"
                    {{ runtimePreChecked2($task->task_recurring_copy_checklists ?? 'yes', 'yes') }}>
                <label class="p-l-30" for="task_recurring_copy_checklists"></label>
            </div>
        </div>


        <!--copy files-->
        <div class="form-group form-group-checkbox row">
            <label class="col-4 col-form-label text-left">@lang('lang.copy_files')</label>
            <div class="col-8 text-left" style="padding-top:5px;">
                <input type="checkbox" id="task_recurring_copy_files" name="task_recurring_copy_files"
                    class="filled-in chk-col-light-blue"
                    {{ runtimePreChecked2($task->task_recurring_copy_files ?? 'yes', 'yes') }}>
                <label class="p-l-30" for="task_recurring_copy_files"></label>
            </div>
        </div>

        <!--automatically assign-->
        <div class="form-group form-group-checkbox row">
            <label class="col-4 col-form-label text-left">@lang('lang.automatically_assign')</label>
            <div class="col-8 text-left" style="padding-top:5px;">
                <input type="checkbox" id="task_recurring_automatically_assign" name="task_recurring_automatically_assign"
                    class="filled-in chk-col-light-blue"
                    {{ runtimePreChecked2($task->task_recurring_automatically_assign ?? 'yes', 'yes') }}>
                <label class="p-l-30" for="task_recurring_automatically_assign"></label>
            </div>
        </div>


    </div>
</div>