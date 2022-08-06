@extends('pages.import.wrapper')
<!--SECOND STEP FORM-->
@section('second-step-form')
<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.status')) }}*</label>
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

<!--assigned [roles]-->
@if(config('visibility.lead_modal_assign_fields'))
<div class="form-group row">
    <label
        class="col-sm-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.assigned')) }}*</label>
    <div class="col-sm-12">
        <select name="assigned" id="assigned"
            class="form-control form-control-sm select2-basic select2-multiple select2-tags select2-hidden-accessible"
            multiple="multiple" tabindex="-1" aria-hidden="true">
            <!--users list-->
            @foreach(config('system.team_members') as $user)
            <option value="{{ $user->id }}" {{ runtimePreselectedInArray($user->id ?? '', $assigned ?? []) }}>{{
                                        $user->full_name }}</option>
            @endforeach
            <!--/#users list-->
        </select>
    </div>
</div>
@endif
@endsection