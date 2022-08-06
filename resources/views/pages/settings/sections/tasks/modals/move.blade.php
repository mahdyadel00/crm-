<div class="form-group row">
    <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.move_to_this_stage')) }}</label>
    <div class="col-sm-12">
        <select class="select2-basic form-control form-control-sm" id="tasks_status" name="tasks_status">
            @foreach($statuses as $status)
            <option value="{{ $status->taskstatus_id }}">{{ $status->taskstatus_title }}</option>
            @endforeach
        </select>
    </div>
</div>