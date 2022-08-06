/** -----------------------------------------------------------------------------
 *  GROWCRM
 *  This lite version is used by direct view pages like [webforms] [invoice] etc
 *  
 *  [INCLUDED VENDOR JS]
 *     - Javascript bootstrapped here is listed in vendor-lite.js
 *    
 *   [BOOTING]
 *     - This is bootstrapped by boot-lite.js
 *-------------------------------------------------------------------------------*/

"use strict";

function NXbootstrap($self, action) {

    //select2 defaults
    $.fn.select2.defaults.set("theme", "bootstrap");
    $.fn.select2.defaults.set("containerCssClass", ":all:");
    $.fn.select2.defaults.set("width", null);
    $.fn.select2.defaults.set("maximumSelectionSize", 6);
    $.fn.select2.defaults.set("allowClear", true);
    $.fn.select2.defaults.set("placeholder", ""); //we must have something to for allowClear to work

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

            //close oll tooltips when body has been clicked
            $(document).on('click', 'body', function () {
                $('.tooltip').remove();
            });

        });


        //initialise opovers
        $(function () {
            $('[data-toggle="popover"]').popover();
            $('.data-toggle-popover').popover();
        });

        //hide tooltip when the button or link is clicked
        $(document).on('click', '[data-toggle="tooltip"]', function () {
            $('[data-toggle="tooltip"]').tooltip("hide");
        });
        $(document).on('click', '.data-toggle-tooltip', function () {
            $('[data-toggle="tooltip"]').tooltip("hide");
        });

        //default date pickers
        $('.pickadate').datepicker({
            format: NX.date_picker_format,
            language: "lang",
            autoclose: true,
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

        //select 2 tags
        $(".select2-tags").select2({
            theme: "bootstrap",
            width: null,
            containerCssClass: ':all:',
            tags: true,
            multiple: true,
            tokenSeparators: [',', ' '],
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
            console.log(preselected);
            if (preselected != '') {
                $(this).val(preselected);
                $(this).trigger('change');
            }
        });
    });

};

//Initialize and boot
NXbootstrap();