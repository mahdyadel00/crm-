<div class="x-heading"><i class="mdi mdi-file-document-box"></i>{{ cleanLang(__('lang.organisation')) }}</div>

<!--address and organisation-->
<div class="card-lead-orginisation-edit" id="card-lead-organisation">

    <!--company name-->
    <div class="form-group row">
        <label class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.company_name')) }}</label>
        <div class="col-sm-12">
            <input type="text" class="form-control form-control-sm" id="lead_company_name" name="lead_company_name"
                placeholder="" value="{{ $lead->lead_company_name ?? '' }}">
        </div>
    </div>
    <!--job title-->
    <div class="form-group row">
        <label class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.job_title')) }}</label>
        <div class="col-sm-12">
            <input type="text" class="form-control form-control-sm" id="lead_job_position" name="lead_job_position"
                placeholder="" value="{{ $lead->lead_job_position ?? '' }}">
        </div>
    </div>
    <!--street-->
    <div class="form-group row">
        <label class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.street')) }}</label>
        <div class="col-sm-12">
            <input type="text" class="form-control form-control-sm" id="lead_street" name="lead_street" placeholder=""
                value="{{ $lead->lead_street ?? '' }}">
        </div>
    </div>
    <!--city-->
    <div class="form-group row">
        <label class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.city')) }}</label>
        <div class="col-sm-12">
            <input type="text" class="form-control form-control-sm" id="lead_city" name="lead_city" placeholder=""
                value="{{ $lead->lead_city ?? '' }}">
        </div>
    </div>
    <!--state-->
    <div class="form-group row">
        <label class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.state')) }}</label>
        <div class="col-sm-12">
            <input type="text" class="form-control form-control-sm" id="lead_state" name="lead_state" placeholder=""
                value="{{ $lead->lead_state ?? '' }}">
        </div>
    </div>
    <!--zip-->
    <div class="form-group row">
        <label class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.zipcode')) }}</label>
        <div class="col-sm-12">
            <input type="text" class="form-control form-control-sm" id="lead_zip" name="lead_zip" placeholder=""
                value="{{ $lead->lead_zip ?? '' }}">
        </div>
    </div>
    <!--country-->
    <div class="form-group row">
        <label class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.country')) }}
            {{ $lead->lead_country }}</label>
        <div class="col-sm-12">
            @php $selected_country = $lead->lead_country; @endphp
            <select class="select2-basic form-control form-control-sm" id="lead_country" name="lead_country">
                <option></option>
                @include('misc.country-list')
            </select>
        </div>
    </div>
    <!--website-->
    <div class="form-group row" id="card-save-organisation-loading">
        <label class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.website')) }}</label>
        <div class="col-sm-12">
            <input type="text" class="form-control form-control-sm" id="lead_website" name="lead_website" placeholder=""
                value="{{ $lead->lead_website ?? '' }}">
        </div>
    </div>
    <div class="form-group text-right">
        <button type="button" class="btn waves-effect waves-light btn-xs btn-default ajax-request"
        data-url="{{ url('leads/content/'.$lead->lead_id.'/show-organisation') }}"
        data-loading-class="loading-before-centre" data-loading-target="card-leads-left-panel">@lang('lang.cancel')</button>
        <button type="button" class="btn btn-danger btn-xs ajax-request"
            data-loading-target="card-leads-left-panel"
            data-loading-class="loading-before-centre"
            data-url="{{ url('/leads/'.$lead->lead_id.'/update-organisation') }}" data-type="form" data-ajax-type="post"
            data-form-id="card-lead-organisation">
            {{ cleanLang(__('lang.update')) }}
        </button>
    </div>
</div>
<!--address and organisation-->