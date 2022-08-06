<!--heading-->
<div class="x-heading p-t-10"><i class="mdi mdi-file-document-box"></i>{{ cleanLang(__('lang.organisation')) }}</div>



<!--Form Data-->
<div class="card-show-form-data " id="card-lead-organisation">

    <!--company name-->
    <div class="form-data-row">

        <span class="x-data-title">{{ cleanLang(__('lang.company_name')) }}:</span>
        <span class="x-data-content">{{ $lead->lead_company_name ?? '' }}</span>

    </div>


    <!--job title-->
    <div class="form-data-row">
        <span class="x-data-title">{{ cleanLang(__('lang.job_title')) }}:</span>
        <span class="x-data-content">{{ $lead->lead_job_position ?? '' }}</span>
    </div>


    <!--street-->
    <div class="form-data-row">
        <span class="x-data-title">{{ cleanLang(__('lang.street')) }}:</span>
        <span class="x-data-content">{{ $lead->lead_street ?? '' }}</span>
    </div>


    <!--city-->
    <div class="form-data-row">
        <span class="x-data-title">{{ cleanLang(__('lang.city')) }}:</span>
        <span class="x-data-content">{{ $lead->lead_city ?? '' }}</span>
    </div>


    <!--state-->
    <div class="form-data-row">
        <span class="x-data-title">{{ cleanLang(__('lang.state')) }}:</span>
        <span class="x-data-content">{{ $lead->lead_state ?? '' }}</span>
    </div>


    <!--zip-->
    <div class="form-data-row">
        <span class="x-data-title">{{ cleanLang(__('lang.zipcode')) }}:</span>
        <span class="x-data-content">{{ $lead->lead_zip ?? '' }}</span>
    </div>

    <!--country-->
    <div class="form-data-row">
        <span class="x-data-title">{{ cleanLang(__('lang.country')) }}:</span>
        <span class="x-data-content">{{ $lead->lead_country ?? '' }}</span>
    </div>


    <!--website-->
    <div class="form-data-row">
        <span class="x-data-title">{{ cleanLang(__('lang.website')) }}:</span>
        <span class="x-data-content">{{ $lead->lead_website ?? '' }}</span>
    </div>


    <!--edit button-->
    @if(config('visibility.lead_editing_buttons'))
    <div class="form-data-row-buttons">
        <button type="button" class="btn waves-effect waves-light btn-xs btn-info ajax-request"
            data-url="{{ url('leads/content/'.$lead->lead_id.'/edit-organisation') }}"
            data-loading-class="loading-before-centre" data-loading-target="card-leads-left-panel">@lang('lang.edit')</button>
    </div>
    @endif

</div>
<!--address and organisation-->