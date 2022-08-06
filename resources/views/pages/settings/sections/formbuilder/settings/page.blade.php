@extends('pages.settings.ajaxwrapper')
@section('settings-page')

<!--tabs menu-->
@include('pages.settings.sections.formbuilder.misc.tabs')

<div id="webform-builder-wraper" class="p-t-40">

    <!--settings-->
    <form class="form" id="webform-builder-settings">


        <!--title-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label">@lang('lang.form_name')</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="webform_title" name="webform_title"
                    value="{{ $webform->webform_title ?? '' }}">
            </div>
        </div>


        <!--thank you message-->
        <div class="form-group row">
            <label class="col-12 text-left control-label col-form-label">@lang('lang.thank_you_message')</label>
            <div class="col-12">
                <textarea class="form-control form-control-sm tinymce-textarea-extended" rows="5"
                    name="webform_thankyou_message" id="webform_thankyou_message">
                    {!! $webform->webform_thankyou_message ?? '' !!}
                </textarea>
            </div>
        </div>


        <!--notify admin-->
        <div class="form-group row">
            <label
                class="col-12 text-left control-label col-form-label">@lang('lang.send_admin_email_notification')</label>
            <div class="col-12">
                <select class="select2-basic form-control form-control-sm select2-preselected" id="webform_notify_admin"
                    name="webform_notify_admin" data-preselected="{{ $webform->webform_notify_admin ?? ''}}">
                    <option value="no">@lang('lang.no')</option>
                    <option value="yes">@lang('lang.yes')</option>
                </select>
            </div>
        </div>

        <!--lead title-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label">@lang('lang.lead_title')</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="webform_lead_title"
                    name="webform_lead_title" value="{{ $webform->webform_lead_title ?? '' }}">
            </div>
        </div>


        <!--submit button text-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label">@lang('lang.submit_button_text')</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="webform_submit_button_text"
                    name="webform_submit_button_text" value="{{ $webform->webform_submit_button_text ?? '' }}">
            </div>
        </div>


        <!--buttons-->
        <div class="text-right" id="webform-builder-settings-buttons">
            <button type="submit" id="commonModalSubmitButton"
                class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request"
                data-url="{{ url('settings/formbuilder/'.$webform->webform_id.'/settings') }}" data-type="form"
                data-form-id="webform-builder-settings" data-ajax-type="post"
                data-loading-target="webform-builder-settings-buttons">{{ cleanLang(__('lang.save_changes')) }}</button>
        </div>
    </form>

</div>

@endsection