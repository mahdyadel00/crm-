@extends('pages.settings.ajaxwrapper')
@section('settings-page')

<!--tabs menu-->
@include('pages.settings.sections.formbuilder.misc.tabs')

<div id="webform-builder-wraper" class="p-t-40">

    <!-- FORM BUILDER JAVASCRIPT-->
    <script src="public/vendor/js/formbuilder/form-builder.min.js?v={{ config('system.versioning') }}"></script>
    <script src="public/js/webforms/webforms.js?v={{ config('system.versioning') }}"></script>

    <div class="webform-builder-container" id="webform-builder-container">


    </div>

    <!--save button-->
    <div class="text-right hidden p-t-30" id="webform-builder-buttons-container">
        <input type="hidden" name="webform-builder-payload" id="webform-builder-payload">
        <button type="submit" id="webform-builder-save-button"
            class="btn btn-rounded-x btn-danger waves-effect text-left"
            data-url="{{ url('settings/formbuilder/'.$webform->webform_id.'/build') }}"
            data-loading-target="webform-builder-buttons-container" data-ajax-type="POST" data-type="form"
            data-form-id="webform-builder-buttons-container" data-button-loading-annimation="yes"
            data-button-disable-on-click="yes" data-on-start-submit-button="disable">@lang('lang.save_form')</button>
    </div>


    <!--DYNAMIC JAVASCRIPT-->
    <script>
        //builder
        var NXBUILDER = (typeof NXBUILDER == 'undefined') ? {} : NXBUILDER;

        //all custom fields
        NXBUILDER.custom_field = JSON.parse('{!! json_encode($custom_fields) !!}', true);

        //existing form fields
        NXBUILDER.current_field = JSON.parse('{!! json_encode($current_fields) !!}', true);
    </script>
</div>

@endsection