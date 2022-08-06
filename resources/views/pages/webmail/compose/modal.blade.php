<!--client email-->
<div class="form-group row">
    <label class="col-12 text-left control-label col-form-label required">@lang('lang.to')</label>
    <!--specified [standard user]-->
    @if($recipients['specified'])
    <div class="col-12">
        <select class="select2-basic form-control form-control-sm select2-preselected" id="email_to" name="email_to">
            <option></option>
            @foreach($recipients['users'] as $user)
            <option value="{{ $user['email'] }}">{{ $user['name'] }} ({{ $user['email'] }})</option>
            @endforeach
        </select>
    </div>
    @endif
    <!--no user specified-->
    @if(!$recipients['specified'])
    <div class="col-sm-12 col-lg-9">
        <input type="text" class="form-control form-control-sm" id="email_to" name="email_to">
    </div>
    @endif
</div>

<!--subject-->
<div class="form-group row">
    <label class="col-12 text-left control-label col-form-label">@lang('lang.subject')</label>
    <div class="col-12">
        <input type="text" class="form-control form-control-sm" id="email_subject" name="email_subject" placeholder="">
    </div>
</div>

<!--template-->
<div class="form-group row">
    <label class="col-12 text-left control-label col-form-label">@lang('lang.use_a_template')</label>
    <div class="col-12">
        <select class="select2-basic form-control form-control-sm select2-preselected" id="email_template_selector"
            name="email_template_selector">
            <option></option>
            @foreach($templates as $template)
            <option value="{{ $template->webmail_template_id }}"
                data-url="{{ url('appwebmail/prefill?id='.$template->webmail_template_id) }}">{{ $template->webmail_template_name }}</option>
            @endforeach
        </select>
    </div>
</div>


<!--text area-->
<div class="form-group row">
    <div class="col-12">
        <textarea class="form-control form-control-sm tinymce-textarea" rows="5" name="email_body"
            id="email_body"></textarea>
    </div>
</div>

<!--fileupload-->
<div class="form-group row">
    <div class="col-12">
        <div class="dropzone dz-clickable" id="email_files">
            <div class="dz-default dz-message">
                <i class="icon-Upload-toCloud"></i>
                <span>@lang('lang.drag_drop_file')</span>
            </div>
        </div>
    </div>
</div>
<!--#fileupload-->


<!--from email-->
<div class="form-group row">
    <label class="col-12 text-left control-label col-form-label required">@lang('lang.from')</label>
    <div class="col-12">
        <select class="select2-basic form-control form-control-sm select2-preselected" id="email_from"
            name="email_from">
            <option value="system_email">{{ config('system.settings_email_from_name') }}
                ({{ config('system.settings_email_from_address') }})</option>
            <option value="users_email">{{ auth()->user()->full_name }} ({{ auth()->user()->email }})</option>
        </select>
    </div>
</div>

<!--warning-->
<div class="alert alert-warning">@lang('lang.email_address_warning')</div>