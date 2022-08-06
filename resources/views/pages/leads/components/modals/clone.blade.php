<!--first name-->
<div class="form-group row m-t--30 m-b-45">
    <label class="col-sm-12  col-lg-3 text-left control-label col-form-label required">@lang('lang.title')*</label>
    <div class="col-sm-12 col-lg-9">
        <input type="text" class="form-control form-control-sm" id="lead_title" name="lead_title"
            value="{{ $lead->lead_title }}">
    </div>
</div>

<div class="modal-selector p-t-30 p-b-1 m-b-30">

    <!--first name-->
    <div class="form-group row">
        <label
            class="col-sm-12  col-lg-3 text-left control-label col-form-label required">@lang('lang.first_name')*</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="lead_firstname" name="lead_firstname"
                value="{{ $lead->lead_firstname }}">
        </div>
    </div>

    <!--last name-->
    <div class="form-group row">
        <label
            class="col-sm-12  col-lg-3 text-left control-label col-form-label required">@lang('lang.last_name')*</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="lead_lastname" name="lead_lastname"
                value="{{ $lead->lead_lastname }}">
        </div>
    </div>


    <!--statuses-->
    <div class="form-group row">
        <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.status')*</label>
        <div class="col-sm-12 col-lg-9">
            <select class="select2-basic form-control form-control-sm" id="lead_status" name="lead_status">
                @foreach($statuses as $status)
                <option value="{{ $status->leadstatus_id }}"
                    {{ runtimePreselected($lead->lead_status ?? '', $status->leadstatus_id) }}>{{
                                                runtimeLang($status->leadstatus_title) }}</option>
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
<div class="hidden p-t-10" id="toogle_more_information">


    <!--value-->
    <div class="form-group row">
        <label class="col-sm-12  col-lg-3 text-left control-label col-form-label">@lang('lang.value')</label>
        <div class="col-sm-12 col-lg-9">
            <input type="number" class="form-control form-control-sm" id="lead_value" name="lead_value"
                value="{{ $lead->lead_value }}">
        </div>
    </div>

    <!--company-->
    <div class="form-group row">
        <label class="col-sm-12  col-lg-3 text-left control-label col-form-label">@lang('lang.company')</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="lead_company_name" name="lead_company_name"
                value="{{ $lead->lead_company_name }}">
        </div>
    </div>


    <!--email address-->
    <div class="form-group row">
        <label class="col-sm-12  col-lg-3 text-left control-label col-form-label">@lang('lang.email')</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="lead_email" name="lead_email"
                value="{{ $lead->lead_email }}">
        </div>
    </div>

    <!--phone-->
    <div class="form-group row">
        <label class="col-sm-12  col-lg-3 text-left control-label col-form-label">@lang('lang.telephone')</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="lead_phone" name="lead_phone"
                value="{{ $lead->lead_phone }}">
        </div>
    </div>


    <!--lead_website-->
    <div class="form-group row">
        <label class="col-sm-12  col-lg-3 text-left control-label col-form-label">@lang('lang.website')</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="lead_website" name="lead_website"
                value="{{ $lead->lead_website }}">
        </div>
    </div>

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