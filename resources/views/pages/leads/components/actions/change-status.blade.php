<div class="form-group row">
    <label for="example-month-input" class="col-12 col-form-label text-left">Status</label>
    <div class="col-sm-12">
        <select class="select2-basic form-control form-control-sm" id="lead_status" name="lead_status">
            @foreach($statuses as $status)
            <option value="{{ $status->leadstatus_id }}"
                {{ runtimePreselected($lead->lead_status ?? '', $status->leadstatus_id) }}>{{
                    runtimeLang($status->leadstatus_title) }}</option>
            @endforeach
        </select>
    </div>
</div>