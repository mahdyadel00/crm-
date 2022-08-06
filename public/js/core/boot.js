"use strict";

function NXbootstrap($self, action) {

    //select2 defaults
    $.fn.select2.defaults.set("theme", "bootstrap");
    $.fn.select2.defaults.set("containerCssClass", ":all:");
    $.fn.select2.defaults.set("width", null);
    $.fn.select2.defaults.set("maximumSelectionSize", 6);
    $.fn.select2.defaults.set("allowClear", true);
    $.fn.select2.defaults.set("placeholder", ""); //we must have something to for allowClear to work


    //validator.js defaults (required particularly for select2, to add 'error' class)
    $.validator.setDefaults({
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                //radios and checkbox
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2-hidden-accessible')) {
                //select 2 dropdowns - add error class to rendered child element
                element.next('span').addClass('error').removeClass('valid');
            } else {
                //regular input field - add error class
                element.addClass('error').removeClass('valid');
            }
        }
    });



    //some default variables & data
    NX.varInitialProjectProgress = 0;

    //because we have added tinymce in common vendor.js - we must sent paths for it to use
    tinyMCE.baseURL = NX.site_url + "/public/vendor/js/tinymce";
    tinyMCE.suffix = '.min';


    //dropzonejs
    Dropzone.autoDiscover = false;
    Dropzone.prototype.defaultOptions.dictRemoveFile = '<i class="ti-close"></i>';
    Dropzone.prototype.defaultOptions.dictCancelUpload = '<i class="ti-close"></i>';
    Dropzone.prototype.defaultOptions.dictFileTooBig = NXLANG.file_too_big;
    Dropzone.prototype.defaultOptions.dictFallbackMessage = NXLANG.drag_drop_not_supported;
    Dropzone.prototype.defaultOptions.dictFallbackText = NXLANG.use_the_button_to_upload;
    Dropzone.prototype.defaultOptions.dictInvalidFileType = NXLANG.file_type_not_allowed;
    Dropzone.prototype.defaultOptions.dictResponseError = NXLANG.generic_error;
    Dropzone.prototype.defaultOptions.dictUploadCanceled = NXLANG.upload_canceled;
    Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = NXLANG.are_you_sure;
    Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = NXLANG.maximum_upload_files_reached;
    Dropzone.prototype.defaultOptions.maxFilesize = NX.upload_maximum_file_size; //in MB's
    Dropzone.prototype.defaultOptions.addRemoveLinks = true;
    Dropzone.prototype.defaultOptions.timeout = 6400000;

    $(document).ready(function () {

        //initialise tooltips
        $(function () {
            //fixed tool tip positioning
            $(document).on('mouseenter', '[data-toggle="tooltip"]:not([data-original-title])', function () {
                $(this).tooltip().tooltip('show').tooltip('hide').tooltip('show');
            });

            //fixed tool tip positioning
            $(document).on('mouseenter', '.data-toggle-tooltip:not([data-original-title])', function () {
                $(this).tooltip().tooltip('show').tooltip('hide').tooltip('show');
            });

            //specif tooltips for action buttons
            if (NX.show_action_button_tooltips) {
                $(document).on('mouseenter', '.data-toggle-action-tooltip:not([data-original-title])', function () {
                    $(this).tooltip().tooltip('show').tooltip('hide').tooltip('show');
                });
            }
        });


        //initialise opovers
        $(function () {
            $('[data-toggle="popover"]').popover();
            $('.data-toggle-popover').popover();
        });

        //hide tooltip when the button or link is clicked
        $(document).on('click', '[data-toggle="tooltip"]', function () {
            $('[data-toggle="tooltip"]').tooltip("hide");
            $(this).off('click');
        });
        $(document).on('click', '.data-toggle-tooltip', function () {
            $('[data-toggle="tooltip"]').tooltip("hide");
            $(this).off('click');
        });
        //close oll tooltips when body has been clicked
        $(document).on('click', '.data-toggle-action-tooltip, .data-toggle-tooltip', function (event) {
            $(this).tooltip('hide');
            $(this).off('click');
        });

        //close oll tooltips when modal window is closed
        $(document).on('click', '#commonModalCloseIcon', function (event) {
            $('.tooltip').tooltip('hide');
            $(this).off('click');
        });

        //default date pickers
        $('.pickadate').datepicker({
            format: NX.date_picker_format,
            language: "lang",
            autoclose: true,
            class: "datepicker-default",
            todayHighlight: true
        });

        //small date pickers
        $('.pickadate-lg').datepicker({
            format: NX.date_picker_format,
            language: "lang",
            autoclose: true,
            class: "datepicker-sm",
            todayHighlight: true
        });

        //default date pickers
        $('.pickadate-timer').datepicker({
            format: NX.date_picker_format,
            language: "lang",
            autoclose: false,
            class: "datepicker-default",
            todayHighlight: true
        });


        /**
         * [nextloop] [datepicker]
         * change the format of the date that is posted to backend (mysql format). This way, you can
         * display date to user in one format and send to backend in another format
         * place a hidden field with same name directly under the date field
         * [notes]
         * uses moment.js to manipulate the date
         * [example]
         * <input type="text" class="form-control form-control-sm pickadate" name="due_date">
         * <input class="mysql-date" type="hidden" name="due_date" value="">
         * */
        $('.pickadate, .pickadate-lg').on('changeDate', function (e) {
            var mysql_date = moment(e.date).format('YYYY-MM-DD');
            var id = $(this).attr('name');
            $("#" + id).val(mysql_date);
        });

        $('.pickadate, .pickadate-lg').on('change', function (e) {
            var id = $(this).attr('name');
            //reset for empty fields
            if ($(this).val() == '') {
                $("#" + id).val('');
                return;
            }
        });

        //destroy all select2 first

        //basic select2 - no search box
        $(".select2-basic").select2({
            minimumResultsForSearch: Infinity,
            allowClear: false, //use data-allow-clear="true" to change this in the html
        });


        //basic select2 - with search box
        $(".select2-basic-with-search").select2({
            minimumResultsForSearch: 1
        });

        //select2 simple ajax
        $(".js-select2-basic-search").select2({
            theme: "bootstrap",
            width: null,
            containerCssClass: ':all:',
            minimumInputLength: 1,
            minimumResultsForSearch: 1,
            ajax: {
                dataType: "json",
                type: "GET",
                data: function (params) {
                    var queryParameters = {
                        term: params.term
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.value,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });

        //select 2 multiple tags - with search
        $(".js-select2-tags-search").select2({
            theme: "bootstrap",
            width: null,
            containerCssClass: ':all:',
            tags: true,
            multiple: true,
            tokenSeparators: [',', ' '],
            minimumInputLength: 1,
            minimumResultsForSearch: 1,
            ajax: {
                dataType: "json",
                type: "GET",
                data: function (params) {
                    var queryParameters = {
                        term: params.term
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.value,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });

        /*
         * select2 simple ajax (copy of above, but for modals)
         * this is a fix for select2 dropdowns not working in bootstrap modal
         * added parent modal -- dropdownParent: $("#commonModal"),
         */
        $(".js-select2-basic-search-modal").select2({
            theme: "bootstrap",
            width: null,
            containerCssClass: ':all:',
            minimumInputLength: 1,
            minimumResultsForSearch: 1,
            ajax: {
                dataType: "json",
                type: "GET",
                data: function (params) {
                    var queryParameters = {
                        term: params.term
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.value,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });

        //select 2 tags
        $(".select2-tags").select2({
            theme: "bootstrap",
            width: null,
            containerCssClass: ':all:',
            tags: true,
            multiple: true,
            tokenSeparators: [',', ' '],
        });

        //select 2 tags (with spaces)
        $(".select2-tags-with-spaces").select2({
            theme: "bootstrap",
            width: null,
            containerCssClass: ':all:',
            tags: true,
            multiple: true,
            tokenSeparators: [','],
        });

        //select 2 tags
        $(".select2-new-options").select2({
            theme: "bootstrap",
            width: null,
            containerCssClass: ':all:',
            tags: true,
        });

        //select 2 (with text field to add on text)
        $(".select2-combo").select2({
            width: null,
            containerCssClass: ':all:',
            tags: true,
        });

        //select 2 (with text field to add on text)
        $(".select2-combo").select2({
            width: null,
            tags: true,
        });

        //select 2 (with text field to add on text)
        $(".select2-with-text-input").select2({
            tags: true
        });


        //refresh scroll bards scrollbar
        if (typeof nxEventsTopNavScroll !== 'undefined') {
            nxEventsTopNavScroll();
        }

        /** ----------------------------------------------------------------------
         *  money and number formatting
         *  Default settings
         * [source] http://openexchangerates.github.io/accounting.js/
         * [usage]
         *      accounting.formatMoney(1200); => $1,200.00
         *      accounting.formatNumber(1200); => 1,200.00
         * ----------------------------------------------------------------------*/
        //default settings
        accounting.settings = {
            currency: {
                symbol: NX.settings_system_currency_symbol,
                format: (NX.settings_system_currency_position == 'left') ? '%s%v' : '%v%s',
                decimal: NX.settings_system_decimal_separator,
                thousand: NX.settings_system_thousand_separator,
                precision: 2 // decimal places
            },
            number: {
                precision: 2, // decimal places
                thousand: NX.settings_system_thousand_separator,
                decimal: NX.settings_system_decimal_separator
            }
        }

    });


    /** ----------------------------------------------------------
     *  [jqphotoswipe]
     *  @source https://ergec.github.io/jQuery-for-PhotoSwipe/
     * -----------------------------------------------------------*/
    $(document).ready(function () {
        $(".fancybox").jqPhotoSwipe({
            galleryOpen: function (gallery) {
                gallery.toggleDesktopZoom();
            }
        });
        //This option forces plugin to create a single gallery and ignores `data-fancybox-group` attribute.
        $(".forcedgallery > a").jqPhotoSwipe({
            forceSingleGallery: true
        });
    });


    /** ----------------------------------------------------------
     *  preselect any select2 dropwnd
     * - the dropdown must have a class [select2-preselected]
     * - the dropdown must have an attr [data-preselected='foo']
     * ---------------------------------------------------------*/
    $(document).ready(function () {
        $(document).find(".select2-preselected").each(function () {
            var preselected = $(this).attr('data-preselected');
            if (preselected != '') {
                $(this).val(preselected);
                $(this).trigger('change');
            }
        });
    });

};


/** ----------------------------------------------------------
 *  - tiny mce
 *  - basic fixed height of 300px
 *  - reinitialized by nextloop ajax
 * @param numeric tinyMCEHeight optional height setting
 * @param numeric tinyMCESelector optional element selector
 * ---------------------------------------------------------*/
function nxTinyMCELite(tinyMCEHeight = 300, tinyMCESelector = '.tinymce-textarea-lite') {

    //remove
    tinymce.remove(tinyMCESelector);
    //initialize
    tinymce.init({
        selector: tinyMCESelector,
        language: NX.system_language,
        mode: 'exact',
        theme: "modern",
        skin: 'light',
        branding: false,
        resize: true,
        menubar: false,
        statusbar: false,
        forced_root_block: false,
        autoresize_min_height: 300,
        document_base_url: NX.site_url,
        plugins: [
            "fullscreen paste link",
            "pagebreak advlist lists",
            "contextmenu",
        ],
        height: tinyMCEHeight,
        toolbar: 'bold link bullist numlist alignleft aligncenter alignright',
        //autosave/update text area
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
            editor.on('FullscreenStateChanged', function (e) {
                if (e.state) {
                    $('.modal-dialog').attr('style', 'transform: none !important');
                } else {
                    $('.modal-dialog').attr('style', 'transform: translate(0,0)');
                }
            });
        },
    });
}

$(document).ready(function () {
    nxTinyMCELite();
});


/** ----------------------------------------------------------
 *  - tiny mce
 *  - basic fixed height of 300px
 *  - reinitialized by nextloop ajax
 * @param numeric tinyMCEHeight optional height setting
 * @param numeric tinyMCESelector optional element selector
 * ---------------------------------------------------------*/
function nxTinyMCEBasic(tinyMCEHeight = 400, tinyMCESelector = '.tinymce-textarea') {

    //remove
    tinymce.remove(tinyMCESelector);
    //initialize
    tinymce.init({
        selector: tinyMCESelector,
        language: NX.system_language,
        mode: 'exact',
        theme: "modern",
        skin: 'light',
        branding: false,
        resize: true,
        menubar: false,
        statusbar: false,
        forced_root_block: false,
        autoresize_min_height: 300,
        document_base_url: NX.site_url,
        plugins: [
            "fullscreen image paste link code media autoresize codesample",
            "table hr pagebreak toc advlist lists textcolor",
            "imagetools contextmenu colorpicker",
        ],
        height: tinyMCEHeight,
        toolbar: 'bold link bullist numlist image media alignleft aligncenter alignright outdent indent hr table code fullscreen',
        //autosave/update text area
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
            editor.on('FullscreenStateChanged', function (e) {
                if (e.state) {
                    $('.modal-dialog').attr('style', 'transform: none !important');
                } else {
                    $('.modal-dialog').attr('style', 'transform: translate(0,0)');
                }
            });
        },
        //upload images
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', 'upload-tinymce-image');
            xhr.setRequestHeader("X-CSRF-Token", NX.csrf_token);
            xhr.onload = function () {
                var json;
                if (xhr.status != 200) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }
                json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                success(json.location);
            };
            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        }
    });
}

$(document).ready(function () {
    nxTinyMCEBasic();
});



/** ----------------------------------------------------------
 *  - tiny mce
 *  - basic fixed height of 300px
 *  - reinitialized by nextloop ajax
 * @param numeric tinyMCEHeight optional height setting
 * @param numeric tinyMCESelector optional element selector
 * ---------------------------------------------------------*/
function nxTinyMCEExtended(tinyMCEHeight = 400, tinyMCESelector = '.tinymce-textarea-extended') {

    //remove
    tinymce.remove(tinyMCESelector);
    //initialize
    tinymce.init({
        selector: tinyMCESelector,
        language: NX.system_language,
        mode: 'exact',
        skin: 'light',
        branding: false,
        menubar: false,
        statusbar: false,
        resize: true,
        forced_root_block: false,
        autoresize_min_height: tinyMCEHeight,
        document_base_url: NX.site_url,
        plugins: [
            "fullscreen image paste link code media codesample autoresize",
            "table hr pagebreak toc advlist lists textcolor",
            "imagetools contextmenu colorpicker",
        ],
        mobile: {
            theme: 'mobile'
        },
        height: tinyMCEHeight,
        toolbar: 'formatselect | bold italic strikethrough forecolor backcolor textcolor colorpicker | link image media | alignleft aligncenter alignright alignjustify hr | numlist bullist outdent indent table | code media fullscreen',
        //autosave/update text area
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
            editor.on('FullscreenStateChanged', function (e) {
                if (e.state) {
                    $('.modal-dialog').attr('style', 'transform: none !important');
                } else {
                    $('.modal-dialog').attr('style', 'transform: translate(0,0)');
                }
            });
        },
        //upload images
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', 'upload-tinymce-image');
            xhr.setRequestHeader("X-CSRF-Token", NX.csrf_token);
            xhr.onload = function () {
                var json;
                if (xhr.status != 200) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }
                json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                success(json.location);
            };
            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        }
    });
}

$(document).ready(function () {
    nxTinyMCEExtended();
});


/** ----------------------------------------------------------
 *  - tiny mce
 *  - basic fixed height of 300px
 *  - reinitialized by nextloop ajax
 * @param numeric tinyMCEHeight optional height setting
 * @param numeric tinyMCESelector optional element selector
 * ---------------------------------------------------------*/
function nxTinyMCEExtendedLite(tinyMCEHeight = 400, tinyMCESelector = '.tinymce-textarea-extended-lite') {

    //remove
    tinymce.remove(tinyMCESelector);
    //initialize
    tinymce.init({
        selector: tinyMCESelector,
        language: NX.system_language,
        mode: 'exact',
        skin: 'light',
        branding: false,
        menubar: false,
        statusbar: false,
        resize: true,
        forced_root_block: false,
        autoresize_min_height: 300,
        document_base_url: NX.site_url,
        plugins: [
            "fullscreen image paste link code media codesample autoresize",
            "table hr pagebreak toc advlist lists textcolor",
            "imagetools contextmenu colorpicker",
        ],
        mobile: {
            theme: 'mobile'
        },
        height: tinyMCEHeight,
        toolbar: 'formatselect | bold italic strikethrough | link image | alignleft aligncenter alignright alignjustify hr | numlist bullist outdent indent table | code fullscreen',
        //autosave/update text area
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
            editor.on('FullscreenStateChanged', function (e) {
                if (e.state) {
                    $('.modal-dialog').attr('style', 'transform: none !important');
                } else {
                    $('.modal-dialog').attr('style', 'transform: translate(0,0)');
                }
            });
        },
        //upload images
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', 'upload-tinymce-image');
            xhr.setRequestHeader("X-CSRF-Token", NX.csrf_token);
            xhr.onload = function () {
                var json;
                if (xhr.status != 200) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }
                json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                success(json.location);
            };
            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        }
    });
}

$(document).ready(function () {
    nxTinyMCEExtendedLite();
});



/** ----------------------------------------------------------
 *  - tiny mce
 *  - basic fixed height of 300px
 *  - reinitialized by nextloop ajax
 * @param numeric tinyMCEHeight optional height setting
 * @param numeric tinyMCESelector optional element selector
 * @param string plugins optional list of additional plugins to load e.g. 'fullpage link image'
 * @param string toolbar optional list of additional toolbar items to load e.g. 'fullpage link image'
 * ---------------------------------------------------------*/
function nxTinyMCEAdvanced(tinyMCEHeight = 300, tinyMCESelector = '.tinymce-textarea-advanced', plugins = '', toolbar = '') {

    //remove
    tinymce.remove(tinyMCESelector);
    //initialize
    tinymce.init({
        selector: tinyMCESelector,
        mode: 'exact',
        theme: "modern",
        skin: 'light',
        branding: false,
        menubar: false,
        statusbar: false,
        forced_root_block: false,
        document_base_url: NX.site_url,
        plugins: [
            "code advlist autolink lists link image preview ",
            "table paste",
            plugins
        ],
        height: tinyMCEHeight,
        toolbar: [
            'source undo redo bold image link bullist numlist alignleft aligncenter alignright code',
            toolbar
        ],
        //autosave/update text area
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
}



/** ----------------------------------------------------------
 *  - tiny mce
 *  - basic fixed height of 300px
 *  - reinitialized by nextloop ajax
 * @param numeric tinyMCEHeight optional height setting
 * @param numeric tinyMCESelector optional element selector
 * ---------------------------------------------------------*/
function nxTinyMCEDocuments(tinyMCEHeight = 800, tinyMCESelector = '.tinymce-document-textarea') {

    //remove
    tinymce.remove(tinyMCESelector);
    //initialize
    tinymce.init({
        selector: tinyMCESelector,
        language: NX.system_language,
        mode: 'exact',
        skin: 'light',
        branding: false,
        menubar: false,
        statusbar: false,
        resize: true,
        forced_root_block: false,
        autoresize_min_height: tinyMCEHeight,
        document_base_url: NX.site_url,
        content_css: [
            "/public/vendor/css/bootstrap/bootstrap.min.css",
            "https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap",
            "/public/vendor/fonts/growcrm-icons/styles.css",
            "/public/themes/default/css/style.css",
            "/public/documents/css/tinymce.css",
        ],
        plugins: [
            "fullscreen image paste link code media codesample autoresize",
            "table hr pagebreak toc advlist lists textcolor",
            "imagetools contextmenu colorpicker",
        ],
        mobile: {
            theme: 'mobile'
        },
        height: tinyMCEHeight,
        toolbar: 'formatselect | bold italic strikethrough forecolor backcolor textcolor colorpicker | link image media | alignleft aligncenter alignright alignjustify hr | numlist bullist outdent indent table | code media fullscreen',
        //autosave/update text area
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
            editor.on('FullscreenStateChanged', function (e) {
                if (e.state) {
                    $('.modal-dialog').attr('style', 'transform: none !important');
                } else {
                    $('.modal-dialog').attr('style', 'transform: translate(0,0)');
                }
            });
        },
        //upload images
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', 'upload-tinymce-image');
            xhr.setRequestHeader("X-CSRF-Token", NX.csrf_token);
            xhr.onload = function () {
                var json;
                if (xhr.status != 200) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }
                json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                success(json.location);
            };
            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        }
    });
}

//Initialize and boot
NXbootstrap();



/** ----------------------------------------------------------
 * [jquery.confirm] - default settings
 * ---------------------------------------------------------*/
jconfirm.defaults = {
    theme: 'modern',
};

/** ----------------------------------------------------------
 *  [jquery.confirm]  
 *     - default delete item dialogue
 *     - uses lang as sent in header template
 * -----------------------------------------------------------*/
$(document).on('click', '.delete-item', function () {


    $self.tooltip("hide");
    $self.trigger("blur");

    //the clicked element
    var $self = $(this);
    //manually trigger confirm
    $.confirm({
        type: 'red',
        title: NXLANG.delete_confirmation,
        content: '<span class="x-details">' + NXLANG.are_you_sure_delete + '</span>',
        buttons: {
            no: {
                text: NXLANG.cancel,
                btnClass: ' btn-sm btn-outline-secondary',
            },
            yes: {
                text: NXLANG.continue,
                btnClass: ' btn-sm btn-outline-danger',
                action: function () {
                    //ajax request
                    nxAjaxUxRequest($self);
                }
            },
        }
    });

    return true;
});


/** ----------------------------------------------------------
 *  [jquery.confirm]  
 *     - default delete item dialogue
 *     - uses lang as sent in header template
 * -----------------------------------------------------------*/
$(document).on('click', '.confirm-action-danger, .confirm-action-info, .confirm-action-success', function () {
    //the clicked element
    var $self = $(this);


    $self.tooltip("hide");
    $self.trigger("blur");

    //default - red theme
    var confirm_popup_button_class = ' btn-sm btn-outline-danger';
    var confirm_popup_theme_style = 'red';

    //info - blue theme
    if ($self.hasClass('confirm-action-info')) {
        var confirm_popup_button_class = ' btn-sm btn-outline-info';
        var confirm_popup_theme_style = 'blue';
    }

    //info - blue theme
    if ($self.hasClass('confirm-action-success')) {
        confirm_popup_button_class = ' btn-sm btn-outline-success';
        confirm_popup_theme_style = 'green';
    }


    //reset any hidden fields
    $(".confirm_hidden_fields").val('');
    //manually trigger confirm
    $.confirm({
        type: confirm_popup_theme_style,
        title: $self.attr('data-confirm-title'),
        content: '<div class="x-details">' + $self.attr('data-confirm-text') + '</div>',
        buttons: {
            no: {
                text: NXLANG.cancel,
                btnClass: ' btn-sm btn-outline-secondary',
            },
            yes: {
                text: NXLANG.continue,
                btnClass: confirm_popup_button_class,
                action: function () {
                    //fast delete ux
                    if ($self.hasClass('js-delete-ux-confirm')) {
                        NX.uxDeleteItem($self);
                    }
                    //ajax request
                    nxAjaxUxRequest($self);
                }
            },
        }
    });

    return true;
});


/** ----------------------------------------------------------
 * [side filter panel] - toggle
 * ---------------------------------------------------------*/
NX.clearSystemCache = function ($self) {
    //manually trigger confirm
    $.confirm({
        type: 'red',
        title: $self.attr('data-confirm-title'),
        content: '<div class="x-details">' + $self.attr('data-confirm-text') + '</div>',
        buttons: {
            no: {
                text: NXLANG.cancel,
                btnClass: ' btn-sm btn-outline-secondary',
            },
            yes: {
                text: NXLANG.continue,
                btnClass: ' btn-sm btn-outline-danger',
                action: function () {
                    //fast delete ux
                    if ($self.hasClass('js-delete-ux-confirm')) {
                        NX.uxDeleteItem($self);
                    }
                    //ajax request
                    nxAjaxUxRequest($self);
                }
            },
        }
    });
    return false;
}

/** ----------------------------------------------------------
 * [side filter panel] - toggle
 * [April 2022] -  add this to skip the for being reset
 *    data-reset-form="skip"
 * ---------------------------------------------------------*/
NX.toggleSidePanel = function ($self) {


    //data
    var self = $self || {};
    var panel_id = self.data('target');
    var panel = $("#" + panel_id);
    var overlay = $(".page-wrapper-overlay");

    //set sidepanel name on overlay
    overlay.attr('data-target', panel_id);

    //reset form
    if ($self.attr('data-reset-form') == 'skip') {
        //do nothing - april 2022 fix - skip reset for prefilled forms
    } else {
        panel.find('form').trigger("reset");
        $('.js-select2-basic-search').val(null).trigger('change');
    }

    //toggle the correct side panel
    panel.slideDown(50);

    //show hide side panel
    panel.toggleClass("shw-rside");

    //show/hide overlay
    overlay.toggle();

    //add body scroll bar
    if (overlay.is(":visible")) {
        $('body').addClass('overflow-hidden');
    }
}

/** ----------------------------------------------------------
 * [list pages stats widget] toggle
 * ---------------------------------------------------------*/
NX.toggleListPagesStatsWidget = function ($self) {

    //data
    var self = $self || {};
    var target_id = self.data('target');
    var target = $("#" + target_id);
    //toggle
    if (!target.is(":visible")) {
        target.css('display', 'flex');
    } else {
        target.hide();
    }
}

/** ----------------------------------------------------------
 * [add user modal] - toggle client options
 * -----------------------------------------------------------*/
NX.toggleAddUserClientOptions = function ($self) {

    //data
    var self = $self || {};
    var target_id = self.data('target');
    var target = $("#" + target_id);

    //only if option is not already visible
    if (target_id == 'none') {
        $("#existing_client").val('no');
        $("#option_existing_client_container").hide();
    } else {
        if (!target.is(":visible")) {
            $("#existing_client").val('yes');
            target.fadeIn('slow');
        }
    }
}

/** ----------------------------------------------------------
 * [add item modal button] - reset target form
 * -----------------------------------------------------------*/
NX.resetTargetModalForm = function ($self) {
    //data
    var self = $self || {};
    var target_id = self.data('target');
    var target = $(target_id);
    //reset forms
    target.find('form').trigger("reset");
    target.find('select').val(null).trigger('change');
    //hide any toggle elements to default state
    target.find('.hidden').hide();
}

/** ----------------------------------------------------------
 * [reset filter panel] - resets the form fields
 * -----------------------------------------------------------*/
NX.resetFilterPanelFields = function ($self) {
    var target = $(".right-sidebar");
    //reset input fields
    target.find('form').trigger("reset");

    target.find('select').val(null).trigger('change');
    //reset hidden date fields
    $(".mysql-date").val('');
}


/** ----------------------------------------------------------
 * [switch toggle content] - toggle hidden content using switch
 * -----------------------------------------------------------*/
NX.switchToggleHiddenContent = function (self) {
    var target_id = self.data('target');
    var target = $("#" + target_id);
    if (self.is(':checked')) {
        target.slideDown("slow");
    } else {
        target.slideUp("slow");
    }
}

/** ----------------------------------------------------------
 *  [various] - toggle form options
 * -----------------------------------------------------------*/
NX.toggleFormOptions = function ($self) {

    //data
    var self = $self || {};
    var target_id = self.attr('data-target');
    var target_class = self.attr('data-target-class');
    var family_class = self.attr('data-family');

    //only if option is not already visible
    $("." + family_class).hide();
    $("#" + target_id).fadeIn('slow');
    $("." + target_class).fadeIn('slow');
}

/** ----------------------------------------------------------
 *  [login-signup-forgot] - toggle login forms
 * -----------------------------------------------------------*/
NX.toggleLoginForms = function ($self) {

    var self = $self || {};
    //data
    var target_id = self.data('target');
    var target = $("#" + target_id);

    //only if option is not already visible
    $(".login-signup-forgot").fadeOut(function () {
        target.fadeIn('slow');
    });
}


/** ----------------------------------------------------------
 *  [dynamic search] - list results dynamic search
 * -----------------------------------------------------------*/
$(document).ready(function () {

    var timeoutID = null;
    var delayTime = 1000; //miliseconds

    function nxDynamicSearch(self, e) {
        //var url = e.target.attributes.getNamedItem('data-url').value;
        nxAjaxUxRequest(self);
    }


    $(document).on('input', '.search-records', function (e) {
        var nxSearchRecord = $(this);
        clearTimeout(timeoutID);
        timeoutID = setTimeout(() => nxDynamicSearch(nxSearchRecord, e), delayTime);
    });


});

/** ----------------------------------------------------------
 * [expandable tab pages] project | lead
 * ---------------------------------------------------------*/
NX.expandTabbedPage = function ($self) {
    //data
    var self = $self || {};
    var split_screen = $("#projects-tab-split-screen");
    var single_screen = $("#projects-tab-single-screen");

    //hide project details split section and show regular section
    if (self.hasClass('js-ajax-ux-request')) {
        if (single_screen.hasClass('hidden')) {
            split_screen.remove();
            single_screen.removeClass('hidden').show();
        }
    }
}


/** ----------------------------------------------------------
 * [add-edit-project] - add or edit project button clicked
 * -----------------------------------------------------------*/
NX.addEditProjectButton = function ($self) {
    //set initial project progress for "manual slider"
    NX.varInitialProjectProgress = parseInt($self.attr('data-project-progress'));
}


/** ----------------------------------------------------------
 *  [update user preference] 
 *  - check if left menu is open/collapsed. Update with new state
 *  - check if statspanel is open collapsed. Update with new state
 * -----------------------------------------------------------*/
NX.updateUserUXPreferences = function ($self) {

    var tempurl = $self.attr('data-url-temp');
    var type = $self.attr('data-type');

    //left menu
    if (type == 'leftmenu') {
        setTimeout(function () {
            var new_menu_position = $('body').hasClass('mini-sidebar') ? 'collapsed' : 'open';
            var url = tempurl + '?leftmenu_position=' + new_menu_position;
            $self.attr('data-url', url);
            nxAjaxUxRequest($($self));
        }, 1000);
    }

    //stats panel
    if (type == 'statspanel') {
        setTimeout(function () {
            var new_statspanel_position = $('#list-pages-stats-widget').is(':visible') ? 'open' : 'collapsed';
            var url = tempurl + '?statspanel_position=' + new_statspanel_position;
            $self.attr('data-url', url);
            nxAjaxUxRequest($($self));
        }, 1000);
    }
}

/** ----------------------------------------------------------
 *  [apply filter button] - button clicked
 * -----------------------------------------------------------*/
NX.applyFilterButton = function ($self) {
    //reset search form field
    $("#search_query").val('');
}


/** ----------------------------------------------------------
 *  [lists main checkbox] - checkbox clicked
 * -----------------------------------------------------------*/
NX.listCheckboxAll = function ($self) {

    //actions container
    var $actions = $("#" + $self.attr('data-actions-container-class'));

    //children check boxes
    var $children = $("." + $self.attr('data-children-checkbox-class'));

    //actions container
    var options_tick_disabled = $("#" + $self.attr('data-tick-disabled'));

    //actions on check/uncheck
    if ($self.is(":checked")) {

        $actions.fadeIn();

        //check all visible childred
        $children.each(function () {
            //only tick check boxes that are not disabled
            if (!$(this).prop('disabled')) {
                $(this).prop('checked', true);
            }
        });

    } else {

        $actions.fadeOut();

        //uncheck all visible childred
        $children.prop('checked', false);

    }
}


/** ----------------------------------------------------------
 *  [lists main checkbox] - checkbox clicked
 * -----------------------------------------------------------*/
NX.listCheckbox = function ($self) {

    //crumbs container
    var $crumbs = $(".list-pages-crumbs");

    //actions container
    var $actions = $("#" + $self.attr('data-actions-container-class'));

    //get the parent table
    var wrapper = $self.parents('table:first');

    //count checked items
    var count = 0;
    wrapper.find(".listcheckbox").each(function () {
        if ($(this).is(":checked")) {
            count++;
        }
    });

    //actions on check/uncheck
    if (count > 0) {
        $actions.fadeIn();
    } else {
        $actions.fadeOut();
    }
}

/** ----------------------------------------------------------
 *  deselect list pages actions checkboxes after every ajax call
 * -----------------------------------------------------------*/
NX.listCheckboxesReset = function () {
    //unselect check boxes
    $(".listcheckbox").prop('checked', false);
    //hide actions
    $(".checkbox-actions").fadeOut();
}

/** ----------------------------------------------------------
 *  [projects & assiged users]
 *   select users to show in assigned drop downn list
 * -----------------------------------------------------------*/
NX.projectsAndAssignedClearToggle = function ($self) {

    //the assigned users dropdown list
    var dropdown_list = $("#" + $self.attr('data-assigned-dropdown'));

    //cear & disable projects dropdown
    dropdown_list.prop("disabled", true);
    dropdown_list.empty().trigger("change");
}

/** ----------------------------------------------------------
 *  [projects & assiged users]
 *   select users to show in assigned drop downn list
 * -----------------------------------------------------------*/
NX.projectAndAssignedCToggle = function (e, $self) {

    //the client's id
    var project_id = e.params.data.id;

    //the projects dropdown list
    var assigned_dropdown = $("#" + $self.attr('data-assigned-dropdown'));

    //cear & disable projects dropdown
    assigned_dropdown.prop("disabled", true);
    assigned_dropdown.empty().trigger("change");

    //backend ajax call to get clients projects
    $.ajax({
        type: 'GET',
        url: NX.site_url + "/feed/projectassigned?project_id=" + project_id
    }).then(function (data) {

        //loop through the returned array and create new select option items
        if (data.length > 0) {
            var option = '';
            assigned_dropdown.append(option).trigger('change');
        }
        for (var i = 0; i <= data.length - 1; i++) {
            var option = new Option(data[i].value, data[i].id, false, false);
            assigned_dropdown.append(option).trigger('change');
        }

        //do we have any data
        if (i > 0) {
            assigned_dropdown.prop("disabled", false);
            // manually trigger the `select2:select` event
            assigned_dropdown.trigger({
                type: 'select2:select',
                params: {
                    data: data
                }
            });
        }
    });
}


/** ----------------------------------------------------------
 *   enable or disable fields and buttons on the form
 * -----------------------------------------------------------*/
NX.recordTaskTimeToggle = function (action = 'disable') {


    //disable
    if (action == 'disable') {
        //reset timer recording form
        $("#manual_time_hours").prop("disabled", true);
        $("#manual_time_minutes").prop("disabled", true);
        $("#manual_timer_created").prop("disabled", true);
        $("#commonModalSubmitButton").prop("disabled", true);
    }


    //disable
    if (action == 'enable') {
        $("#manual_time_hours").prop("disabled", false);
        $("#manual_time_minutes").prop("disabled", false);
        $("#manual_timer_created").prop("disabled", false);
        $("#commonModalSubmitButton").prop("disabled", false);
    }
}

/** ----------------------------------------------------------
 *  [projects & tasks]
 *   clearing and disabling the dropdown
 * -----------------------------------------------------------*/
NX.projectsTasksClearToggle = function ($self, action = 'disable') {

    //the assigned users dropdown list
    var dropdown_list = $("#" + $self.attr('data-task-dropdown'));

    //cear & disable projects dropdown
    dropdown_list.prop("disabled", true);
    dropdown_list.empty().trigger("change");
}

/** ----------------------------------------------------------
 *  [projects & project tasks]
 *  Return a list of tasks assigned to the logged in user
 * -----------------------------------------------------------*/
NX.projectAssignedTasksToggle = function (e, $self) {

    //the client's id
    var project_id = e.params.data.id;

    //the projects dropdown list
    var task_dropdown = $("#" + $self.attr('data-task-dropdown'));

    //the no results found - block reset
    var no_results_found = $("#" + $self.attr('data-task-dropdown') + '_no_results');
    no_results_found.hide();

    //cear & disable projects dropdown
    task_dropdown.prop("disabled", true);
    task_dropdown.empty().trigger("change");

    //backend ajax call to get clients projects
    $.ajax({
        type: 'GET',
        url: NX.site_url + "/feed/projects-my-assigned-task?project_id=" + project_id
    }).then(function (data) {

        //loop through the returned array and create new select option items
        if (data.length > 0) {
            //var option = '<option value=""></option>';
            task_dropdown.append(option).trigger('change');
            //enable form fields (for manually recording task time)
            NX.recordTaskTimeToggle('enable');
        } else {
            //show - no results found block
            no_results_found.show();
            //disable form fields (for manually recording task time)
            NX.recordTaskTimeToggle('disable');
        }
        for (var i = 0; i <= data.length - 1; i++) {
            var option = new Option(data[i].value, data[i].id, false, false);
            task_dropdown.append(option).trigger('change');
        }

        //do we have any data
        if (i > 0) {
            task_dropdown.prop("disabled", false);
            // manually trigger the `select2:select` event
            task_dropdown.trigger({
                type: 'select2:select',
                params: {
                    data: data
                }
            });
        }
    });
}



/** ----------------------------------------------------------
 *  [clients & projects] - select client to show projects
 * -----------------------------------------------------------*/
NX.clientAndProjectsClearToggle = function ($self) {

    //the projects dropdown list
    var projects_dropdown = $("#" + $self.attr('data-projects-dropdown'));

    //cear & disable projects dropdown
    projects_dropdown.prop("disabled", true);
    projects_dropdown.empty().trigger("change");
}

/** ----------------------------------------------------------
 *  [clients & projects] - select client to show projects
 * -----------------------------------------------------------*/
NX.clientAndProjectsToggle = function (e, $self) {

    //the client's id
    var client_id = e.params.data.id;

    //feed ref
    var feed_ref = $self.attr('data-feed-request-type');

    //the projects dropdown list
    var projects_dropdown = $("#" + $self.attr('data-projects-dropdown'));

    //cear & disable projects dropdown
    projects_dropdown.prop("disabled", true);
    projects_dropdown.empty().trigger("change");

    //add loading class to projects dropdown
    $(".dynamic_" + $self.attr('data-projects-dropdown')).addClass('loading');

    //disable or main dropdown
    $self.prop("disabled", true);

    //backend ajax call to get clients projects
    $.ajax({
        type: 'GET',
        url: NX.site_url + "/feed/projects?ref=" + feed_ref + "&client_id=" + client_id
    }).then(function (data) {

        //loop through the returned array and create new select option items
        if (data.length > 0) {
            var option = '';
            projects_dropdown.append(option).trigger('change');
        }
        for (var i = 0; i <= data.length - 1; i++) {
            var option = new Option(data[i].value, data[i].id, false, false);
            projects_dropdown.append(option).trigger('change');
        }

        //do we have any data
        if (i > 0) {
            projects_dropdown.prop("disabled", false);
            // manually trigger the `select2:select` event
            projects_dropdown.trigger({
                type: 'select2:select',
                params: {
                    data: data
                }
            });
        }

        //remove loading class to projects dropdown
        $(".dynamic_" + $self.attr('data-projects-dropdown')).removeClass('loading');


        //enable or main dropdown
        $self.prop("disabled", false);

    });
}


/** ----------------------------------------------------------
 *  [toggle ticket editor or view mode]
 * -----------------------------------------------------------*/
NX.ticketEditorToggle = function () {
    //elements
    var ticket_display = $("#ticket-body");
    var ticket_editor = $("#ticket-editor");

    if (ticket_editor.is(":visible")) {
        ticket_editor.fadeOut(function () {
            ticket_display.show();
        });
    } else {
        ticket_display.fadeOut(function () {
            ticket_editor.show();
        });
    }
}


/** -------------------------------------------------------------------
 *  [toggle place holder elements] 
 *  @attr data-show-element-container - element to show
 *  @attr data-hide-element-container - element to hide (optional)
 * -------------------------------------------------------------------*/
NX.togglePlaceHolders = function ($self) {
    var $show_element = $("#" + $self.attr('data-show-element-container'));
    var $hide_element = $("#" + $self.attr('data-hide-element-container'));

    //toggle
    $self.hide();
    $hide_element.hide();
    $show_element.show();
}

/** ----------------------------------------------------------
 *  [toggle place holder elements] 
 *  @attr data-main-element-container
 * -----------------------------------------------------------*/
NX.toggleCloseButtonElements = function ($self) {
    var $show_element = $("#" + $self.attr('data-show-element-container'));
    var $hide_element = $("#" + $self.attr('data-hide-element-container'));
    //toggle
    $hide_element.hide();
    $show_element.show();
}


/** ----------------------------------------------------------
 *  [task - checklist text clicked]
 *  - can edit existing checklist item
 *  - close editing boxes
 *  - create new checklist item
 * -----------------------------------------------------------*/
NX.toggleEditTaskChecklist = function ($self) {
    //get copy of text area
    var $cloned = $("#element-checklist-text").clone();
    $cloned.addClass("copied-checklist-text");
    var toggle = $self.attr("data-toggle");

    //toggle open task for editing
    if (toggle == 'edit') {
        //restore all checklist items & remove any text areas
        $('#card-checklist').find('.copied-checklist-text').each(function () {
            var $checklist = $(this).closest('.checklist-item');
            $checklist.children().show();
            $checklist.show();
            $(this).remove();
        });
        //hide the checklist item
        $self.hide();
        //get the parent
        var $parent = $self.parent();
        //hide all elements
        $parent.children().hide();
        //add & show edit text field
        $parent.append($cloned);
        //add text to new element
        $cloned.find('textarea').val($self.html());
        //add the id of the element
        $cloned.find('#checklist-id').val($self.attr('data-id'));
        $cloned.find('#checklist-submit-button').attr('data-url', $self.attr('data-action-url'));
        $cloned.find('#checklist-submit-button').attr('data-resource', 'edit');
        $cloned.show();
        //show add newbuttons
        $("#card-checklist-add-new").show();
    }

    //toggle open task for editing
    if (toggle == 'new') {
        //restore all checklist items & remove any text areas
        $('#card-checklist').find('.copied-checklist-text').each(function () {
            var $checklist = $(this).closest('.checklist-item');
            $checklist.children().show();
            $checklist.show();
            $(this).remove();
        });
        //hide the checklist item
        $self.hide();
        //add new text area
        $('#card-checklist').append($cloned);
        //reset and show
        $cloned.find('textarea').html('');
        $(".checklist_text").html('');
        $cloned.find('#checklist-id').val('');
        $cloned.find('#checklist-submit-button').attr('data-url', $self.attr('data-action-url'));
        $cloned.find('#checklist-submit-button').attr('data-resource', 'new');
        $cloned.show();
    }


    //close any text field
    if (toggle == 'save') {
        //get the parent (if we are editing list item)
        var $checklist = $self.closest('.checklist-item');
        //remove all text areas
        if ($self.attr('data-resource') == 'edit') {
            $(".copied-checklist-text").remove();
        }
        //show original checklist (if we are editing)
        $checklist.children().show();
        $checklist.show();
        //show add newbuttons
        $("#card-checklist-add-new").show();
    }

    //close any text field
    if (toggle == 'close') {
        //get the parent (if we are editing list item)
        var $checklist = $self.closest('.checklist-item');
        //remove all text areas
        $(".copied-checklist-text").remove();
        //show original checklist (if we are editing)
        $checklist.children().show();
        $checklist.show();
        //show add newbuttons
        $("#card-checklist-add-new").show();
    }
}


/** ----------------------------------------------------------
 *  [task - reset the task form]
 * -----------------------------------------------------------*/
NX.resetCardModal = function ($self) {

    //re-hide the modal
    $("#cardModalContent").addClass('hidden');
    $("#cardModalContent").addClass('hidden');

    //form previously used to convert a lead
    $("#leadConvertToCustomer").hide();
    $("#leadConvertToCustomerFooter").hide();
    $("#cardModalBody").show();
    $("#cardModalBody").show();
}



/** ----------------------------------------------------------
 *  [better ux on delete items] 
 * - remove item from list whilst the ajax happend in background
 * -----------------------------------------------------------*/
NX.uxDeleteItem = function ($self) {
    var $parent = $("#" + $self.attr('data-parent-container'));
    $parent.slideUp();
    $parent.remove();
}



/** ----------------------------------------------------------
 *  [toggle task timer] 
 * -----------------------------------------------------------*/
NX.toggleTaskTimer = function ($self) {

    var taskid = $self.attr('data-task-id');

    //rest all the users timer buttons
    $(".timer-stop-button").hide();
    $(".timer-start-button").show();

    //remove
    $(".timers").removeClass('timer-running');

    //if this was a start button, now show specific stop buttons for this task
    if ($self.hasClass('timer-start-button')) {

        //hide start buttons on card and list
        $("#timer_button_start_table_" + taskid).hide();
        $("#timer_button_start_card_" + taskid).hide();


        //show stop buttons on card and list
        $("#timer_button_stop_table_" + taskid).show();
        $("#timer_button_stop_card_" + taskid).show();

        //show table list timer as running
        $("#task_timer_table_" + taskid).addClass('timer-running');
        //show card timer as running
        $("#task_timer_card_" + taskid).addClass('timer-running');

        //hide manual timer button in task
        $(".timer_button_manual_card_" + taskid).hide();
        $(".manual_timer_entry_" + taskid).hide();

    }

    if ($self.hasClass('timer-stop-button')) {
        //hide manual timer button in task
        $(".timer_button_manual_card_" + taskid).show();
        $(".manual_timer_entry_" + taskid).hide();
    }


    //i
}


/** ----------------------------------------------------------
 *  [toggle settings left menu]
 * -----------------------------------------------------------*/
NX.toggleSettingsLeftMenu = function ($self) {
    var $menu = $(".settings-menu");
    if ($menu.hasClass('toggle-left-menu')) {
        $menu.removeClass('toggle-left-menu');
    } else {
        $menu.addClass('toggle-left-menu');
    }
}



/** ----------------------------------------------------------
 *  [convert lead to customer form]
 * -----------------------------------------------------------*/
NX.convertLeadForm = function ($self, action) {

    //update form with actual content
    $("#convert_lead_firstname").val($("#card-lead-firstname-containter").html());
    $("#convert_lead_lastname").val($("#card-lead-lastname-containter").html());
    $("#convert_lead_email").val($("#card-lead-email").html());
    $("#convert_lead_phone").val($("#card-lead-phone").html());
    $("#convert_lead_job_position").val($("#lead_job_position").val());
    $("#convert_lead_company_name").val($("#lead_company_name").val());
    $("#convert_lead_website").val($("#lead_website").val());
    $("#convert_lead_street").val($("#lead_street").val());
    $("#convert_lead_city").val($("#lead_city").val());
    $("#convert_lead_state").val($("#lead_state").val());
    $("#convert_lead_zip").val($("#lead_zip").val());
    $("#convert_lead_source").val($("#lead_source").val());
    $("#convert_lead_title").val($("#lead_title").val());
    $("#convert_lead_description").val($("#lead_description").val());
    $("#convert_lead_value").val($("#lead_value").val());

    //clean up
    $(".form-control").each(function () {
        if ($(this).val() == '---') {
            $(this).val('');
        }
    });

    //country
    $("#convert_lead_country").val($("#lead_country").val()).trigger('change');

    //fade in the form
    if (action == 'show') {
        $("#cardModalBody").fadeOut(function () {
            $("#leadConvertToCustomer").fadeIn('slow');
            $("#leadConvertToCustomerFooter").fadeIn('slow');
        });
    } else {
        $("#leadConvertToCustomer").fadeOut(function () {
            $("#leadConvertToCustomerFooter").fadeOut('fast');
            $("#cardModalBody").fadeIn('slow');
        });
    }
}


/** ----------------------------------------------------------
 *  [top nav events icon clicked]
 * -----------------------------------------------------------*/
NX.eventsTopNav = function ($self) {
    //reset the panel
    $("#topnav-events-container").html('');
    //hide footer
    $("#topnav-events-container-footer").hide();
    //request
    nxAjaxUxRequest($self);
}


//scroll bar for events dropdown
function nxEventsTopNavScroll($self) {
    //only if element exists
    if ($("#topnav-events-container").length) {
        const ps = new PerfectScrollbar('#topnav-events-container', {
            wheelSpeed: 2,
            wheelPropagation: true,
            minScrollbarLength: 20
        });
    }
}

//scroll bar for events dropdown
function nxProjectTimelineScroll($self) {
    //only if element exists
    if ($(".project-timeline").length) {
        const ps2 = new PerfectScrollbar('#embed-content-container', {
            wheelSpeed: 2,
            wheelPropagation: true,
            minScrollbarLength: 20
        });
    }
}


/** ----------------------------------------------------------
 *  [top nav events - mark all events as read]
 * -----------------------------------------------------------*/
NX.eventsTopNavMarkAllRead = function ($self) {
    //reset the panel
    $("#sidepanel-notifications-events").html('');
    //hide mark all read button
    $("#sidepanel-notifications-mark-all-read").hide();
    //hide bell icon
    $("#topnav-notification-icon").hide();
    //request
    nxAjaxUxRequest($self);
}



/** ----------------------------------------------------------
 *  [top nav events - mark event(s) as read]
 * -----------------------------------------------------------*/
NX.eventsMarkRead = function ($self, items = 'single') {
    //remove a single item
    if (items == 'single') {
        var $event = $("#" + $self.attr('data-container'));
        $event.remove();
    }
    //remove all items
    if (items == 'all') {
        $("#topnav-events-container").html('');
    }
    //request
    nxAjaxUxRequest($self);
}




/** ----------------------------------------------------------
 *  [change the browser url] for back button functionality
 * -----------------------------------------------------------*/
NX.browserPushState = function ($self) {
    //dynamic url
    var new_url = $self.attr('data-dynamic-url');
    //push to browser
    if (typeof new_url != 'undefined' && new_url != null) {
        var stateObj = {
            title: NX.site_page_title,
            url: new_url,
        };
        console.log(stateObj);
        window.history.pushState(stateObj, NX.site_page_title, new_url);
    }
}


/** ----------------------------------------------------------
 *  [browser back button] listen for the back button and refresh
 * -----------------------------------------------------------*/
window.addEventListener("popstate", function (e) {
    window.location.href = location.href;
});



/** ----------------------------------------------------------
 *  [settings][email template selected]
 * -----------------------------------------------------------*/
NX.loadEmailTemplate = function ($self) {
    var value = $self.val();

    //update action url and start ajax request
    if (value !== 0) {
        $self.attr('data-url', value);
        nxAjaxUxRequest($self);
    }
}

/** ----------------------------------------------------------
 *  [settings][email template selected]
 * -----------------------------------------------------------*/
NX.toggleInvoiceTaxEditing = function ($self) {

    var action = $self.attr('id');

    //clicked on edit button
    if (action == 'invoice-tax-edit-button') {
        $("#invoice-tax-container").hide();
        $("#invoice-edit-tax-container").show();
    }

    //clicked on cancel button
    if (action == 'invoice-edit-tax-close-button') {
        $("#invoice-edit-tax-container").hide();
        $("#invoice-tax-container").show();
    }
}



/** ----------------------------------------------------------
 * payment method has been selected - show correct pay now buttons
 * -----------------------------------------------------------*/
NX.selectPaymentGateway = function ($self) {

    var gateway_id = $self.attr('data-gateway-id');

    //[stripe]- show please wait and send ajax request
    if (gateway_id == 'gateway-stripe') {
        //button
        var $gateway = $("#gateway-please-wait");
        //initiate ajax request to get stripe session
        nxAjaxUxRequest($self);
    }


    //[paypal] is stripe - show please wait and send ajax request
    if (gateway_id == 'gateway-paypal') {
        //button
        var $gateway = $("#gateway-please-wait");
        //initiate ajax request to get stripe session
        nxAjaxUxRequest($self);
    }

    //[razorpay]- show please wait and send ajax request
    if (gateway_id == 'gateway-razorpay') {
        //button
        var $gateway = $("#gateway-please-wait");
        //initiate ajax request to get stripe session
        nxAjaxUxRequest($self);
    }


    //[bank] just show bank details
    if (gateway_id == 'gateway-bank') {
        //button
        var $gateway = $("#gateway-bank");
    }

    //[others] just show the button
    if ($self.attr('data-button-action') == 'show-button') {
        var button_id = $self.attr('data-button-id');
        var $gateway = $("#" + button_id);
    }


    //hide all aother gateways
    $(".payment-gateways").hide();
    //hide check list
    $("#invoice-pay-options-container").hide();

    //show new title
    $("#invoice-pay-title-select-method").hide();
    $("#invoice-pay-title-complete-payment").show();

    //show this gateway
    $gateway.show();
}



/** ----------------------------------------------------------
 *  [shipping address] same as billing address
 * -----------------------------------------------------------*/
NX.shippingAddressSameBilling = function ($self) {
    //make address the same as billing
    if ($self.prop("checked")) {
        $("#client_shipping_street").val($("#client_billing_street").val());
        $("#client_shipping_city").val($("#client_billing_city").val());
        $("#client_shipping_state").val($("#client_billing_state").val());
        $("#client_shipping_zip").val($("#client_billing_zip").val());
        $("#client_shipping_country").val($("#client_billing_country").val()).trigger('change');
    } else {
        //make blank
        $("#client_shipping_street").val("");
        $("#client_shipping_city").val("");
        $("#client_shipping_state").val("");
        $("#client_shipping_zip").val("");
        $("#client_shipping_country").val("").trigger('change');

    }

}


/** ----------------------------------------------------------
 * set let menu tooltips
 * -----------------------------------------------------------*/
function NXleftMenuToolTips() {
    //distroy first
    $('.menu-tooltip').tooltip('dispose');

    if ($('body').hasClass('mini-sidebar')) {
        $(".menu-with-tooltip").addClass('menu-tooltip');
    } else {
        $(".menu-with-tooltip").removeClass('menu-tooltip');
    }
    $('.menu-tooltip').tooltip({
        trigger: 'hover',
        placement: 'right',
        delay: {
            hide: 0
        },
        template: '<div class="tooltip menu-tooltips" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
    });
    $(document).on('click', '.menu-tooltip', function () {
        $('.menu-tooltip').tooltip("hide");
    });
};
$(document).ready(function () {
    NXleftMenuToolTips();
    $('.menu-tooltip').mouseleave(function () {
        NXleftMenuToolTips();
    });
});





/*----------------------------------------------------------------
 * function to show left main menu scrollbar PerfectScrollbar()
 *---------------------------------------------------------------*/
function nxMainLeftMenuScroll() {
    const navLeftScroll = new PerfectScrollbar('#main-scroll-sidebar', {
        wheelSpeed: 2,
        wheelPropagation: true,
        minScrollbarLength: 20
    });
}

/*----------------------------------------------------------------
 * set variables and payload
 *--------------------------------------------------------------*/
function nxAutoHideSideMenu() {
    if (!$("#main-body").hasClass('mini-sidebar')) {
        $("#main-body").addClass('mini-sidebar')
    }
}
//window is already small
$(document).ready(function () {
    if ($(window).width() <= 765) {
        nxAutoHideSideMenu();
    }
    //window has been resized
    $(window).resize(function () {
        if ($(window).width() <= 765) {
            nxAutoHideSideMenu();
        }
    });
});


/** ----------------------------------------------------------
 *  [stripe product and price] - clear price
 * -----------------------------------------------------------*/
NX.stripeProductPriceClearToggle = function ($self) {

    //the projects dropdown list
    var price_dropdown = $("#" + $self.attr('data-prices-dropdown'));

    //cear & disable projects dropdown
    price_dropdown.prop("disabled", true);
    price_dropdown.empty().trigger("change");
}

/** ----------------------------------------------------------
 *  [stripe product and price] - get list of stripe prices 
 *  the selected stripe product
 * -----------------------------------------------------------*/
NX.stripeProductPriceToggle = function (e, $self) {

    //the client's id
    var product_id = e.params.data.id;

    //the projects dropdown list
    var prices_dropdown = $("#" + $self.attr('data-prices-dropdown'));

    //cear & disable projects dropdown
    prices_dropdown.prop("disabled", true);
    prices_dropdown.empty().trigger("change");

    //disable product also
    $self.prop("disabled", true);

    //add ajax loading to the price
    $(".dynamic-select2-price").addClass('loading');


    //backend ajax call to get clients projects
    $.ajax({
        type: 'GET',
        url: NX.site_url + "/subscriptions/getprices?product_id=" + product_id
    }).then(function (data) {

        //loop through the returned array and create new select option items
        if (data.length > 0) {
            var option = '';
            prices_dropdown.append(option).trigger('change');
        }
        for (var i = 0; i <= data.length - 1; i++) {
            var option = new Option(data[i].value, data[i].id, false, false);
            prices_dropdown.append(option).trigger('change');
        }

        //do we have any data
        if (i > 0) {
            prices_dropdown.prop("disabled", false);
            // manually trigger the `select2:select` event
            prices_dropdown.trigger({
                type: 'select2:select',
                params: {
                    data: data
                }
            });
        }

        //remove loading
        $(".dynamic-select2-price").removeClass('loading');

        //enable product
        $self.prop("disabled", false);

    });
}




/** ----------------------------------------------------------
 *  [custom fields] - settings button clicked
 * -----------------------------------------------------------*/
NX.toggleTableSettingsRow = function ($self) {

    //the settings row
    var settings_row = $("#" + $self.attr('data-settings-row-id'));

    var common_class = $self.attr('data-settings-common-rows');

    //remove actve class
    $("tr").removeClass('active-settings-rows');

    //show or hide the settings row
    if (!settings_row.is(":visible")) {
        //hide all other rows
        $(".toggle-table-settings-row").hide();
        //show this row
        settings_row.show();
        //add active class
        $("." + common_class).addClass('active-settings-rows');
    } else {
        //hide all rows
        $(".toggle-table-settings-row").hide();
    }

}





/** ----------------------------------------------------------
 *  [top nav reminders icon clicked]
 * -----------------------------------------------------------*/
NX.remindersTopNav = function ($self) {
    //reset the panel
    $("#topnav-reminders-container").html('');
    //hide footer
    $("#topnav-reminders-container-footer").hide();
    //request
    nxAjaxUxRequest($self);
}




/** ----------------------------------------------------------
 *  [top nav reminder - delete reminder]
 * -----------------------------------------------------------*/
NX.remindersMarkRead = function ($self, items = 'single') {
    var $reminder = $("#" + $self.attr('data-container'));
    $reminder.remove();

    //check if this was the last item
    if ($('.topnav-reminder').length == 0) {
        $("#topnav-reminders-dropdown").hide();
    }

    //request
    nxAjaxUxRequest($self);
}



/** ----------------------------------------------------------
 *  [clients & projects] - select client to show projects
 * -----------------------------------------------------------*/
NX.projectsAndMilestonesClearToggle = function ($self) {

    //the projects dropdown list
    var milestones_dropdown = $("#" + $self.attr('data-milestones-dropdown'));

    //cear & disable projects dropdown
    milestones_dropdown.prop("disabled", true);
    milestones_dropdown.empty().trigger("change");
}

/** ----------------------------------------------------------
 *  [clients & projects] - select client to show projects
 * -----------------------------------------------------------*/
NX.projectsAndMilestonesToggle = function (e, $self) {

    //the client's id
    var project_id = e.params.data.id;

    //the projects dropdown list
    var milestones_dropdown = $("#" + $self.attr('data-milestones-dropdown'));

    //cear & disable projects dropdown
    milestones_dropdown.prop("disabled", true);
    milestones_dropdown.empty().trigger("change");

    //add loading class to projects dropdown
    $(".dynamic_" + $self.attr('data-milestones-dropdown')).addClass('loading');

    //disable or main dropdown
    $self.prop("disabled", true);

    //backend ajax call to get clients projects
    $.ajax({
        type: 'GET',
        url: NX.site_url + "/feed/project-milestones?project_id=" + project_id
    }).then(function (data) {

        //loop through the returned array and create new select option items
        if (data.length > 0) {
            var option = '';
            milestones_dropdown.append(option).trigger('change');
        }
        for (var i = 0; i <= data.length - 1; i++) {
            var option = new Option(data[i].value, data[i].id, false, false);
            milestones_dropdown.append(option).trigger('change');
        }

        //do we have any data
        if (i > 0) {
            milestones_dropdown.prop("disabled", false);
            // manually trigger the `select2:select` event
            milestones_dropdown.trigger({
                type: 'select2:select',
                params: {
                    data: data
                }
            });
        }

        //remove loading class to projects dropdown
        $(".dynamic_" + $self.attr('data-milestones-dropdown')).removeClass('loading');


        //enable or main dropdown
        $self.prop("disabled", false);

    });
}


/*----------------------------------------------------------------
 * page loading - preloading
 *---------------------------------------------------------------*/
NProgress.set(0.90);