<div class="p-40">
    <h3>{{ cleanLang(__('lang.convert_to_a_customer')) }}</h3>
    <div class="line p-b-25"></div>

    <!--first name-->
    <div class="form-group row">
        <label
            class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.first_name')) }}*</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="convert_lead_firstname" name="first_name"
                placeholder="" value="{{ $lead->lead_firstname ?? '' }}">
        </div>
    </div>
    <!--last name-->
    <div class="form-group row">
        <label
            class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.last_name')) }}*</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="convert_lead_lastname" name="last_name"
                placeholder="" value="{{ $lead->lead_lastname ?? '' }}">
        </div>
    </div>

    <!--company name-->
    <div class="form-group row">
        <label
            class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.company_name')) }}*</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="convert_lead_company_name"
                name="client_company_name" placeholder="" value="{{ $lead->lead_company_name ?? '' }}">
        </div>
    </div>

    <!--email-->
    <div class="form-group row">
        <label
            class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.email_address')) }}*</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="convert_lead_email" name="email" placeholder=""
                value="{{ $lead->lead_email ?? '' }}">
        </div>
    </div>

    <div class="line"></div>

    <!--telephone-->
    <div class="form-group row">
        <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.telephone')) }}</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="convert_lead_phone" name="phone" placeholder=""
                value="{{ $lead->lead_phone ?? '' }}">
        </div>
    </div>

    <!--street-->
    <div class="form-group row">
        <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.street')) }}</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="convert_lead_street" name="street"
                placeholder="" value="{{ $lead->lead_street ?? '' }}">
        </div>
    </div>
    <!--city-->
    <div class="form-group row">
        <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.city')) }}</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="convert_lead_city" name="city" placeholder=""
                value="{{ $lead->lead_city ?? '' }}">
        </div>
    </div>
    <!--state-->
    <div class="form-group row">
        <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.state')) }}</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="convert_lead_state" name="state" placeholder=""
                value="{{ $lead->lead_state ?? '' }}">
        </div>
    </div>
    <!--zip-->
    <div class="form-group row">
        <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.zipcode')) }}</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="convert_lead_zip" name="zip" placeholder=""
                value="{{ $lead->lead_zip ?? '' }}">
        </div>
    </div>
    <!--country-->
    <div class="form-group row">
        <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.country')) }}</label>
        <div class="col-sm-12 col-lg-9">
            <select class="select2-basic form-control form-control-sm" id="convert_lead_country" name="country">
                <option></option>
                @include('misc.country-list')
            </select>
        </div>
    </div>
    <!--website-->
    <div class="form-group row">
        <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.website')) }}</label>
        <div class="col-sm-12 col-lg-9">
            <input type="text" class="form-control form-control-sm" id="convert_lead_website" name="lead_website"
                placeholder="" value="{{ $lead->lead_website ?? '' }}">
        </div>
    </div>

    <!--require-->
    <div class="row">
        <div class="col-12">
            <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
        </div>
    </div>

    <div class="line"></div>
    <div class="form-group form-group-checkbox row">
        <div class="col-12 text-left p-t-5">
            <input type="checkbox" id="send_welcome_email" name="send_welcome_email"
                class="filled-in chk-col-light-blue" checked="checked">
            <label for="send_welcome_email">{{ cleanLang(__('lang.send_a_welcome_email')) }}</label>
        </div>
    </div>
    <div class="form-group form-group-checkbox row">
        <div class="col-12 text-left p-t-5">
            <input type="checkbox" id="delete_lead" name="delete_lead" class="filled-in chk-col-light-blue">
            <label for="delete_lead">{{ cleanLang(__('lang.delete_lead')) }}</label>
        </div>
    </div>
</div>