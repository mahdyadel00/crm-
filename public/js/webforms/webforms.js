"use strict";

var NXBUILDER = (typeof NXBUILDER == 'undefined') ? {} : NXBUILDER;

/**--------------------------------------------------------------------------------------
 * [FORM BUILRDER EVENT] - FIELDS ADDED TO CANVAS
 * @description: When fields are added to the canvas, they should be disabled from the
 * left panel beccause each custom field is a [single use] field.
 * -------------------------------------------------------------------------------------*/
NXBUILDER.toggleAvailableFields = function (field_id) {

    var field = $(field_id);

    //find the form field
    var form_field = field.find('.prev-holder').find('.form-control').attr('id');

    if (form_field != '') {
        var element_id = form_field.replace('-preview', '');

        //now find it in the left panel
        var custom_field = $("." + element_id).closest('li');

        //disable the custom fields
        custom_field.addClass('disabled-field');
    }
}

/**--------------------------------------------------------------------------------------
 * [RENDER THE FORM BUILDER]
 * @description: This is the main function that creates the form builder and listens for
 * events triggered by user actions on the form
 * -------------------------------------------------------------------------------------*/
function NXFormBuilder() {

    //the form container
    var builder_container = document.getElementById('webform-builder-container');

    //options - basic settings
    var builder_options = {
        controlPosition: 'left',
        dataType: 'json',
        stickyControls: {
            enable: false,
        },
    };

    //controls ordering
    builder_options.controlOrder = [
        'header',
        'paragraph',
        'radio-group',
        'file',
        'text',
        'number',
        'date',
        'select',
        'textarea',
        'checkbox-group',
    ];

    //disable some buttons
    builder_options.disabledActionButtons = [
        'data',
        'save',
        'clear'
    ];

    //options - disabled fields
    builder_options.disableFields = [
        'autocomplete',
        'button',
        'checkbox-group',
        'date',
        'hidden',
        'number',
        'radio-group',
        'select',
        'starRating',
        'textarea',
        'text',
        'file',
    ];

    //options - existing fields (to be made dynamic later)
    builder_options.defaultFields = NXBUILDER.current_field;


    //disable fields that has already been added
    builder_options.typeUserEvents = {
        'text': {
            onadd: function (field_id) {
                NXBUILDER.toggleAvailableFields(field_id, 'input');
            },
        },
        'select': {
            onadd: function (field_id) {
                NXBUILDER.toggleAvailableFields(field_id, 'select');
            },
        },
        'textarea': {
            onadd: function (field_id) {
                NXBUILDER.toggleAvailableFields(field_id, 'textarea');
            },
        },
        'number': {
            onadd: function (field_id) {
                NXBUILDER.toggleAvailableFields(field_id, 'input');
            },
        },
        'date': {
            onadd: function (field_id) {
                NXBUILDER.toggleAvailableFields(field_id, 'input');
            },
        },
        'checkbox-group': {
            onadd: function (field_id) {
                NXBUILDER.toggleAvailableFields(field_id, 'checkbox-group');
            },
        },
        'file': {
            onadd: function (field_id) {
                NXBUILDER.toggleAvailableFields(field_id, 'file');
            },
        },
    }

    builder_options.fields = NXBUILDER.custom_field;


    //render the form builder
    NXBUILDER.form = $(builder_container).formBuilder(builder_options);

    //show save button
    $("#webform-builder-buttons-container").show();
}


/**--------------------------------------------------------------------------------------
 * [FORM BUILRDER EVENT] - FIELDS REMOVED FROM CANVAS
 * -------------------------------------------------------------------------------------*/
$(document).on('click', '.del-button', function () {

    //find the parent
    var custom_container = $(this).parents('li.form-field');

    //find the id of the form field
    var form_field = custom_container.find('.prev-holder').find('.form-control').attr('id');

    if (form_field != '') {
        var element_id = form_field.replace('-preview', '');

        //now find it in the left panel
        var custom_field = $("." + element_id).closest('li');

        //enable the custom fields
        custom_field.removeClass('disabled-field');
    }
});


/**--------------------------------------------------------------------------------------
 * [FORM BUILRDER EVENT] - SAVE BUTTON CLICKED
 * -------------------------------------------------------------------------------------*/
$(document).on('click', '#webform-builder-save-button', function () {

    //get the current form data as json
    var form_data = NXBUILDER.form.actions.getData('json', true);

    //add it to hidden form field
    $("#webform-builder-payload").val(form_data);

    //submit form
    nxAjaxUxRequest($(this));


});