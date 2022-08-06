<!--heading-->
<div class="x-heading p-t-10"><i class="mdi mdi-file-document-box"></i>{{ cleanLang(__('lang.custom_fields')) }}</div>



<!--Form Data-->
<div class="card-show-form-data" id="card-lead-organisation">
@if(count($fields) > 0) 
@foreach($fields as $field) <div class="form-data-row">

        <span class="x-data-title">{{ $field->customfields_title }}:</span>
        <span class="x-data-content {{ $field->customfields_datatype }}">{!!
            customFieldValueDisplay($field->customfields_name, $lead, $field->customfields_datatype) !!}</span>

</div>
@endforeach

@if(config('app.application_demo_mode'))
<!--DEMO INFO-->
<div class="alert alert-info">
    <h5 class="text-info"><i class="sl-icon-info"></i> Demo Info</h5>  
    These are custom fields. You can change them or <a href="{{ url('app/settings/customfields/projects') }}">create your own.</a>
</div>
@endif


<!--edit button-->
@if(config('visibility.lead_editing_buttons'))
<div class="form-data-row-buttons">
    <button type="button" class="btn waves-effect waves-light btn-xs btn-info ajax-request"
        data-url="{{ url('leads/content/'.$lead->lead_id.'/edit-customfields') }}"
        data-loading-class="loading-before-centre"
        data-loading-target="card-leads-left-panel">@lang('lang.edit')</button>
</div>
@endif

@else

<div class="x-no-result">
    <img src="{{ url('/') }}/public/images/no-download-avialble.png" alt="404 - Not found" /> 
    <div class="p-t-20"><h4>{{ cleanLang(__('lang.you_do_not_have_custom_fields')) }}</h4></div>
    <div class="p-t-10">
        <a href="{{ url('app/settings/customfields/leads') }}" class="btn btn-info btn-sm">@lang('lang.create_custom_fields')</a>
    </div>
</div>
@endif
</div>