<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" id="meta-csrf" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title><?php echo e(config('system.settings_company_name')); ?> <?php echo e(clean($page['meta_title'] ?? '')); ?></title>

    <!--BASEURL-->
    <base href="<?php echo e(url('/')); ?>" target="_self">

    <!--JQUERY & OTHER HEADER JS-->
    <script src="public/vendor/js/vendor.header.js?v=<?php echo e(config('system.versioning')); ?>"></script>

    <!--BOOTSTRAP-->
    <link href="public/vendor/css/bootstrap/bootstrap.min.css" rel="stylesheet">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="public/vendor/js/html5shiv/html5shiv.js"></script>
    <script src="public/vendor/js/respond/respond.min.js"></script>
    <![endif]-->

    <!--GOOGLE FONTS-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" rel="stylesheet"
        type="text/css">

    <!--GOOGLE ROBOTO-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

    <!--VENDORS CSS-->
    <link rel="stylesheet" href="public/vendor/css/vendor.css?v=<?php echo e(config('system.versioning')); ?>">

    <!--ICONS-->
    <link rel="stylesheet" href="public/vendor/fonts/growcrm-icons/styles.css?v=<?php echo e(config('system.versioning')); ?>">

    <!--THEME STYLE-->
    <!--use the default theme for all external pages (e.g. proposals, cotracts etc) -->
    <?php if(config('visibility.external_view_use_default_theme')): ?>
    <link href="public/themes/default/css/style.css?v=<?php echo e(config('system.settings_system_javascript_versioning')); ?>"
        rel="stylesheet">
    <?php else: ?>
    <?php if(auth()->check()): ?>
    <link
        href="public/themes/<?php echo e(auth()->user()->pref_theme); ?>/css/style.css?v=<?php echo e(config('system.settings_system_javascript_versioning')); ?>"
        rel="stylesheet">
    <?php else: ?>
    <link href="<?php echo e(config('theme.selected_theme_css')); ?> " rel="stylesheet">
    <?php endif; ?>
    <?php endif; ?>

    <!--USERS CUSTON CSS FILE-->
    <link href="public/css/custom.css?v=<?php echo e(config('system.versioning')); ?>" rel="stylesheet">

    <!--PRINTING CSS-->
    <link href="public/css/print.css?v=<?php echo e(config('system.versioning')); ?>" rel="stylesheet">

    <!-- Favicon icon -->
    <link rel="apple-touch-icon" sizes="57x57" href="public/images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="public/images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="public/images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="public/images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="public/images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="public/images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="public/images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="public/images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="public/images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="public/images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/images/favicon/favicon-16x16.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="public/images/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">


    <!--SET DYNAMIC VARIABLE IN JAVASCRIPT-->
    <script type="text/javascript">
        //name space & settings
        NX = (typeof NX == 'undefined') ? {} : NX;
        NXJS = (typeof NXJS == 'undefined') ? {} : NXJS;
        NXLANG = (typeof NXLANG == 'undefined') ? {} : NXLANG;
        NXINVOICE = (typeof NXINVOICE == 'undefined') ? {} : NXINVOICE;
        NXDOC = (typeof NXDOC == 'undefined') ? {} : NXDOC;
        NX.data = (typeof NX.data == 'undefined') ? {} : NX.data;

        NXINVOICE.DATA = {};
        NXINVOICE.DOM = {};
        NXINVOICE.CALC = {};

        //variables
        NX.site_url = "<?php echo e(url('/')); ?>";
        NX.site_page_title = "<?php echo e(config('system.settings_company_name')); ?> <?php echo e(clean($page['meta_title'] ?? '')); ?>";
        NX.csrf_token = "<?php echo e(csrf_token()); ?>";
        NX.system_language = "<?php echo e(request('system_language')); ?>";
        NX.date_format = "<?php echo e(config('system.settings_system_date_format')); ?>";
        NX.date_picker_format = "<?php echo e(config('system.settings_system_datepicker_format')); ?>";
        NX.date_moment_format = "<?php echo e(runtimeMomentFormat(config('system.settings_system_date_format'))); ?>";
        NX.upload_maximum_file_size = "<?php echo e(config('system.settings_files_max_size_mb')); ?>";
        NX.settings_system_currency_symbol = "<?php echo e(config('system.settings_system_currency_symbol')); ?>";
        NX.settings_system_decimal_separator =
            "<?php echo e(runtimeCurrrencySeperators(config('system.settings_system_decimal_separator'))); ?>";
        NX.settings_system_thousand_separator =
            "<?php echo e(runtimeCurrrencySeperators(config('system.settings_system_thousand_separator'))); ?>";
        NX.settings_system_currency_position = "<?php echo e(config('system.settings_system_currency_position')); ?>";
        NX.show_action_button_tooltips = "<?php echo e(config('settings.show_action_button_tooltips')); ?>";
        NX.notification_position = "<?php echo e(config('settings.notification_position')); ?>";
        NX.notification_error_duration = "<?php echo e(config('settings.notification_error_duration')); ?>";
        NX.notification_success_duration = "<?php echo e(config('settings.notification_success_duration')); ?>";
        NX.session_login_popup = "<?php echo e(config('system.settings_system_session_login_popup')); ?>";


        //javascript console debug modes
        NX.debug_javascript = "<?php echo e(config('app.debug_javascript')); ?>";

        //popover template
        NX.basic_popover_template = '<div class="popover card-popover" role="tooltip">' +
            '<span class="popover-close" onclick="$(this).closest(\'div.popover\').popover(\'hide\');" aria-hidden="true">' +
            '<i class="ti-close"></i></span>' +
            '<div class="popover-header"></div><div class="popover-body" id="popover-body"></div></div>';

        //lang - used in .js files
        NXLANG.delete_confirmation = "<?php echo e(cleanLang(__('lang.delete_confirmation'))); ?>";
        NXLANG.are_you_sure_delete = "<?php echo e(cleanLang(__('lang.are_you_sure_delete'))); ?>";
        NXLANG.cancel = "<?php echo e(cleanLang(__('lang.cancel'))); ?>";
        NXLANG.continue = "<?php echo e(cleanLang(__('lang.continue'))); ?>";
        NXLANG.file_too_big = "<?php echo e(cleanLang(__('lang.file_too_big'))); ?>";
        NXLANG.maximum = "<?php echo e(cleanLang(__('lang.maximum'))); ?>";
        NXLANG.generic_error = "<?php echo e(cleanLang(__('lang.error_request_could_not_be_completed'))); ?>";
        NXLANG.drag_drop_not_supported = "<?php echo e(cleanLang(__('lang.drag_drop_not_supported'))); ?>";
        NXLANG.use_the_button_to_upload = "<?php echo e(cleanLang(__('lang.use_the_button_to_upload'))); ?>";
        NXLANG.file_type_not_allowed = "<?php echo e(cleanLang(__('lang.file_type_not_allowed'))); ?>";
        NXLANG.cancel_upload = "<?php echo e(cleanLang(__('lang.cancel_upload'))); ?>";
        NXLANG.remove_file = "<?php echo e(cleanLang(__('lang.remove_file'))); ?>";
        NXLANG.maximum_upload_files_reached = "<?php echo e(cleanLang(__('lang.maximum_upload_files_reached'))); ?>";
        NXLANG.upload_maximum_file_size = "<?php echo e(cleanLang(__('lang.upload_maximum_file_size'))); ?>";
        NXLANG.upload_canceled = "<?php echo e(cleanLang(__('lang.upload_canceled'))); ?>";
        NXLANG.are_you_sure = "<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>";
        NXLANG.image_dimensions_not_allowed = "<?php echo e(cleanLang(__('lang.image_dimensions_not_allowed'))); ?>";
        NXLANG.ok = "<?php echo e(cleanLang(__('lang.ok'))); ?>";
        NXLANG.cancel = "<?php echo e(cleanLang(__('lang.cancel'))); ?>";
        NXLANG.close = "<?php echo e(cleanLang(__('lang.close'))); ?>";
        NXLANG.system_default_category_cannot_be_deleted =
            "<?php echo e(cleanLang(__('lang.system_default_category_cannot_be_deleted'))); ?>";
        NXLANG.default_category = "<?php echo e(cleanLang(__('lang.default_category'))); ?>";
        NXLANG.select_atleast_one_item = "<?php echo e(cleanLang(__('lang.select_atleast_one_item'))); ?>";
        NXLANG.invalid_discount = "<?php echo e(cleanLang(__('lang.invalid_discount'))); ?>";
        NXLANG.add_lineitem_items_first = "<?php echo e(cleanLang(__('lang.add_lineitem_items_first'))); ?>";
        NXLANG.fixed = "<?php echo e(cleanLang(__('lang.fixed'))); ?>";
        NXLANG.percentage = "<?php echo e(cleanLang(__('lang.percentage'))); ?>";
        NXLANG.action_not_completed_errors_found = "<?php echo e(cleanLang(__('lang.action_not_completed_errors_found'))); ?>";
        NXLANG.selected_expense_is_already_on_invoice =
            "<?php echo e(cleanLang(__('lang.selected_expense_is_already_on_invoice'))); ?>";
        NXLANG.please_wait = "<?php echo e(cleanLang(__('lang.please_wait'))); ?>";
        NXLANG.invoice_time_unit = "<?php echo e(cleanLang(__('lang.time'))); ?>";

        //arrays to use generically
        NX.array_1 = [];
        NX.array_2 = [];
        NX.array_3 = [];
        NX.array_4 = [];
    </script>

    <!--boot js-->
    <script src="public/js/core/head.js?v=<?php echo e(config('system.versioning')); ?>"></script>

    <!--stripe payments js-->
    <?php if(@config('visibility.stripe_js')): ?>
    <script src="https://js.stripe.com/v3/"></script>
    <?php endif; ?>

    <!--razorpay payments js-->
    <?php if(@config('visibility.razorpay_js')): ?>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <?php endif; ?>

    <!--[note: no sanitizing required] for this trusted content, which is added by the admin-->
    <?php echo config('system.settings_theme_head'); ?>

</head><?php /**PATH C:\xampp\htdocs\application\resources\views/layout/header.blade.php ENDPATH**/ ?>