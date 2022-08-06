"use strict";

NXDOC.DOM = {};


/**--------------------------------------------------------------------------------------
 * [RESIZE TEXT] 
 * @description: show dynamic doc pages
 * -------------------------------------------------------------------------------------*/
$(document).ready(function () {
    $(document).on({
        mouseenter: function () {
            $(this).find('.doc-edit-icon').show();
        },
        mouseleave: function () {
            $(this).find('.doc-edit-icon').hide();
        }
    }, ".js-doc-editing");
});



/**--------------------------------------------------------------------------------------
 * [UPDATE PROPOSAL/CONTRACT HERO HEADER]
 * @description: update image
 * -------------------------------------------------------------------------------------*/
function NXDOCHeroHeader() {

    //upload avatar
    $("#doc_hero_header_image").dropzone({
        url: "/upload-general-image",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        maxFiles: 1,
        maxFilesize: 2, // MB
        acceptedFiles: 'image/jpeg, image/png',
        thumbnailWidth: null,
        thumbnailHeight: null,
        init: function () {
            this.on("error", function (file, message, xhr) {

                //is there a message from backend [abort() response]
                if (typeof xhr != 'undefined' && typeof xhr.response != 'undefined') {
                    var error = $.parseJSON(xhr.response);
                    var message = error.notification.value;
                }

                //any other message
                message = (typeof message == 'undefined' || message == '' ||
                    typeof message == 'object') ? NXLANG.generic_error : message;

                //error message
                NX.notification({
                    type: 'error',
                    message: message
                });
                //remove the file
                this.removeFile(file);
            });
        },
        success: function (file, response) {
            //get the priview box dom elemen
            var $preview = $(file.previewElement);
            //create a hidden form field for this file
            $preview.append('<input type="hidden" name="image_filename"  value="' + response.filename + '">');
            $preview.append('<input type="hidden" name="image_directory"  value="' + response.uniqueid + '">');
        }
    });


    //validation
    $("#commonModalForm").validate().destroy();
    $("#commonModalForm").validate({
        rules: {
            doc_heading: "required",
            doc_title: "required"
        },
        submitHandler: function (form) {
            nxAjaxUxRequest($("#commonModalSubmitButton"));
        }
    });
}
//[hero header] - initiate dropzone
$(document).ready(function () {
    if ($("#documents-side-panel-hero").length) {
        NXDOCHeroHeader();
    }
});


/**--------------------------------------------------------------------------------------
 * [EDIT ESTIMATE]
 * -------------------------------------------------------------------------------------*/
$(document).ready(function () {
    $(document).on('click', '#js-document-billing', function () {

        //reset the side panel content
        $("#documents-side-panel-billing-content").html('');
        $("#documents-side-panel-billing-info").hide();

        //loadajax
        nxAjaxUxRequest($(this));

    });
});



/**--------------------------------------------------------------------------------------
 * [EDIT ESTIMATE] - reinitialize the popover buttons
 * -------------------------------------------------------------------------------------*/
function NXDOCEstimateInitialise() {
    $(document).find(".js-elements-popover-button").each(function () {
        $(this).popover({
            html: true,
            sanitize: false, //The HTML is NOT user generated
            template: NX.basic_popover_template,
            title: $(this).data('title'),
            content: function () {
                //popover elemenet
                var str = $(this).attr('data-popover-content');
                //decode html entities
                return $("<div/>").html(str).text();
            }
        });
    });
};



/**--------------------------------------------------------------------------------------
 * [TEXT EDITOR]
 * -------------------------------------------------------------------------------------*/
 $(document).ready(function () {
    if ($("#doc_body").length) {
        nxTinyMCEDocuments(800, '.tinymce-document-textarea');
    }
});

