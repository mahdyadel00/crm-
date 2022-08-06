<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" id="meta-csrf" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ config('system.settings_company_name') }} {{ clean($page['meta_title'] ?? '') }}</title>

    <!--BASEURL-->
    <base href="{{ url('/') }}" target="_self">

    <!--JQUERY & OTHER LITE JS-->
    <script src="public/vendor/js/vendor-lite.header.js?v={{ config('system.versioning') }}"></script>

    <!--BOOTSTRAP-->
    <link href="public/vendor/css/bootstrap/bootstrap.min.css" rel="stylesheet">

    <!--GOOGLE FONTS-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" rel="stylesheet"
        type="text/css">


    <!--VENDORS CSS-->
    <link rel="stylesheet" href="public/vendor/css/vendor-lite.css?v={{ config('system.versioning') }}">

    <!--THEME STYLE-->
    <link href="{{ config('theme.selected_theme_css') }} " rel="stylesheet">

    <!--USERS CUSTON CSS FILE-->
    <link href="public/css/custom.css?v={{ config('system.versioning') }}" rel="stylesheet">

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="public/images/favicon/favicon-16x16.png">
    <meta name="theme-color" content="#ffffff">

    <!--SET DYNAMIC VARIABLE IN JAVASCRIPT-->
    <script type="text/javascript">
        //name space & settings
        NX = (typeof NX == 'undefined') ? {} : NX;
        NXJS = (typeof NXJS == 'undefined') ? {} : NXJS;
        NXLANG = (typeof NXLANG == 'undefined') ? {} : NXLANG;
        NXINVOICE = (typeof NXINVOICE == 'undefined') ? {} : NXINVOICE;
        NX.data = (typeof NX.data == 'undefined') ? {} : NX.data;

        NXINVOICE.DATA = {};
        NXINVOICE.DOM = {};
        NXINVOICE.CALC = {};

        //variables
        NX.site_url = "{{ url('/') }}";
        NX.csrf_token = "{{ csrf_token() }}";
        NX.system_language = "{{ request('system_language') }}";
        NX.date_format = "{{ config('system.settings_system_date_format') }}";
        NX.date_picker_format = "{{ config('system.settings_system_datepicker_format') }}";
        NX.date_moment_format = "{{ runtimeMomentFormat(config('system.settings_system_date_format')) }}";
        NX.upload_maximum_file_size = "{{ config('system.settings_files_max_size_mb') }}";
        NX.settings_system_currency_symbol = "{{ config('system.settings_system_currency_symbol') }}";
        NX.settings_system_decimal_separator =
            "{{ runtimeCurrrencySeperators(config('system.settings_system_decimal_separator')) }}";
        NX.settings_system_thousand_separator =
            "{{ runtimeCurrrencySeperators(config('system.settings_system_thousand_separator')) }}";
        NX.settings_system_currency_position = "{{ config('system.settings_system_currency_position') }}";
        NX.show_action_button_tooltips = "{{ config('settings.show_action_button_tooltips') }}";
        NX.notification_position = "{{ config('settings.notification_position') }}";
        NX.notification_error_duration = "{{ config('settings.notification_error_duration') }}";
        NX.notification_success_duration = "{{ config('settings.notification_success_duration') }}";
        NX.session_login_popup = "{{ config('system.settings_system_session_login_popup') }}";


        //javascript console debug modes
        NX.debug_javascript = "{{ config('app.debug_javascript') }}";

        //popover template
        NX.basic_popover_template = '<div class="popover card-popover" role="tooltip">' +
            '<span class="popover-close" onclick="$(this).closest(\'div.popover\').popover(\'hide\');" aria-hidden="true">' +
            '<i class="ti-close"></i></span>' +
            '<div class="popover-header"></div><div class="popover-body" id="popover-body"></div></div>';

        //lang - used in .js files
        NXLANG.delete_confirmation = "{{ cleanLang(__('lang.delete_confirmation')) }}";
        NXLANG.are_you_sure_delete = "{{ cleanLang(__('lang.are_you_sure_delete')) }}";
        NXLANG.cancel = "{{ cleanLang(__('lang.cancel')) }}";
        NXLANG.continue = "{{ cleanLang(__('lang.continue')) }}";
        NXLANG.file_too_big = "{{ cleanLang(__('lang.file_too_big')) }}";
        NXLANG.maximum = "{{ cleanLang(__('lang.maximum')) }}";
        NXLANG.generic_error = "{{ cleanLang(__('lang.error_request_could_not_be_completed')) }}";
        NXLANG.drag_drop_not_supported = "{{ cleanLang(__('lang.drag_drop_not_supported')) }}";
        NXLANG.use_the_button_to_upload = "{{ cleanLang(__('lang.use_the_button_to_upload')) }}";
        NXLANG.file_type_not_allowed = "{{ cleanLang(__('lang.file_type_not_allowed')) }}";
        NXLANG.cancel_upload = "{{ cleanLang(__('lang.cancel_upload')) }}";
        NXLANG.remove_file = "{{ cleanLang(__('lang.remove_file')) }}";
        NXLANG.maximum_upload_files_reached = "{{ cleanLang(__('lang.maximum_upload_files_reached')) }}";
        NXLANG.upload_maximum_file_size = "{{ cleanLang(__('lang.upload_maximum_file_size')) }}";
        NXLANG.upload_canceled = "{{ cleanLang(__('lang.upload_canceled')) }}";
        NXLANG.are_you_sure = "{{ cleanLang(__('lang.are_you_sure')) }}";
        NXLANG.image_dimensions_not_allowed = "{{ cleanLang(__('lang.image_dimensions_not_allowed')) }}";
        NXLANG.ok = "{{ cleanLang(__('lang.ok')) }}";
        NXLANG.cancel = "{{ cleanLang(__('lang.cancel')) }}";
        NXLANG.close = "{{ cleanLang(__('lang.close')) }}";
        NXLANG.system_default_category_cannot_be_deleted =
            "{{ cleanLang(__('lang.system_default_category_cannot_be_deleted')) }}";
        NXLANG.default_category = "{{ cleanLang(__('lang.default_category')) }}";
        NXLANG.select_atleast_one_item = "{{ cleanLang(__('lang.select_atleast_one_item')) }}";
        NXLANG.invalid_discount = "{{ cleanLang(__('lang.invalid_discount')) }}";
        NXLANG.add_lineitem_items_first = "{{ cleanLang(__('lang.add_lineitem_items_first')) }}";
        NXLANG.fixed = "{{ cleanLang(__('lang.fixed')) }}";
        NXLANG.percentage = "{{ cleanLang(__('lang.percentage')) }}";
        NXLANG.action_not_completed_errors_found = "{{ cleanLang(__('lang.action_not_completed_errors_found')) }}";
        NXLANG.selected_expense_is_already_on_invoice =
            "{{ cleanLang(__('lang.selected_expense_is_already_on_invoice')) }}";
        NXLANG.please_wait = "{{ cleanLang(__('lang.please_wait')) }}";
        NXLANG.invoice_time_unit = "{{ cleanLang(__('lang.time')) }}";

        //arrays to use generically
        NX.array_1 = [];
        NX.array_2 = [];
        NX.array_3 = [];
        NX.array_4 = [];
    </script>

</head>