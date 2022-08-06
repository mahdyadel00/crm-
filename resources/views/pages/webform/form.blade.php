<!DOCTYPE html>
<html lang="en" class="direct-view webform">

<!--LITE MINIMAL HEADER-->
@include('layout.header-lite')


<body class="{{ config('visibility.webform_view') }}">

    <!--preloader-->
    <div class="preloader">
        <div class="loader">
            <div class="loader-loading"></div>
        </div>
    </div>
    <!--preloader-->

    <!--logo-->
    <div class="webform-logo hidden">
        <img src="{{ runtimeLogoLarge() }}" alt="homepage" class="logo-large" />
    </div>

    <!--SHOW FORM-->
    @if(config('visibility.webform') == 'show')
    <div class="wrapper p-b-50 p-t-50">

        <!--the form-->
        <div id="webform">
            <!--form fields-->
            <div class="webform-fields-wrapper" id="webform-fields-wrapper">
                {!! $fields !!}
            </div>

            <!--form errors-->
            <div class="webform-errors-wrapper hidden" id="webform-errors-wrapper">
                <div class="alert alert-danger">
                    <h5 class="text-danger"><i class="sl-icon-info"></i> @lang('lang.fill_in_all_required_fields')
                    </h5>
                    <ul id="webform-errors">
                        <!--dynamic-->
                    </ul>
                </div>
            </div>

            <!--form buttons-->
            <div class="text-right p-t-30" id="webform-buttons-container">
                <button type="submit" id="submitButton" class="btn btn-info waves-effect text-left ajax-request"
                    data-url="{{ url('webform/submit/'.$webform->webform_id) }}" data-type="form" data-form-id="webform"
                    data-progress-bar="hidden" data-ajax-type="post"
                    data-loading-target="webform-buttons-container">{{ $webform->webform_submit_button_text ?? __('lang.submit') }}</button>

            </div>
        </div>

        <!--error message-->
        <div class="page-notification hidden" id="webform-system-error">
            <img class="m-b-30" src="{{ url('/') }}/public/images/404.png" alt="404 - Not found" />
            <h3 class="m-b-30 font-weight-200"> @lang('lang.application_error') </h3>
        </div>

        <!--error message-->
        <div class="p-t-10 hidden" id="webform-submit-success">

        </div>


    </div>
    @endif


    <!--SHOW ERROR-->
    @if(config('visibility.webform') == 'error')
    <div class="wrapper p-b-50">

    </div>
    @endif


</body>


<!--JS FOOTER - LITE-->
@include('layout.footerjs-lite')

<!--REMOVE LOADER-->
<script>
    $(window).bind("load", function () {
        $(".preloader").hide();
    });
</script>

</html>