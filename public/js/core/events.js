"use strict";

$(document).ready(function () {

    /** --------------------------------------------------------------------------------------------------
     *  [subscription product - price] - on selecting product, update the price for that product
     *  [notes]
     * --------------------------------------------------------------------------------------------------*/
    //client list has been reset or cleared
    $(document).on("select2:unselecting", ".stripe_product_price", function (e) {
        NX.clientAndProjectsClearToggle($(this));
    });
    //client list has been reset or cleared
    $(document).on("select2:select", ".stripe_product_price", function (e) {
        NX.stripeProductPriceToggle(e, $(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [link on a div]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".click-url", function (e) {
        window.location = $(this).attr("data-url");
    });


    /** --------------------------------------------------------------------------------------------------
     *  [remove preloader]
     * -------------------------------------------------------------------------------------------------*/
    $(".preloader").fadeOut('slow', function () {
        NProgress.done();
    });


    /** --------------------------------------------------------------------------------------------------
     *  prevent events from bubbling down
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".js-stop-propagation", function (event) {
        event.stopPropagation();
    });


    /** --------------------------------------------------------------------------------------------------
     *  [side filter panel] - toggle
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".js-toggle-side-panel", function () {
        NX.toggleSidePanel($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [stats widget] - toggle
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".js-toggle-stats-widget", function () {
        NX.toggleListPagesStatsWidget($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [add user modal] - toggle client options
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".js-toggle-client-options", function () {
        NX.toggleAddUserClientOptions($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [add item modal button] - reset target form
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".reset-target-modal-form", function () {
        NX.resetTargetModalForm($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [reset filter panel] - resets the form fields
     * ---------------------------------------------------------*/
    $(document).on("click", ".js-reset-filter-side-panel", function () {
        NX.resetFilterPanelFields($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [add clients modal] - toggle address section
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".js-switch-toggle-hidden-content", function () {
        NX.switchToggleHiddenContent($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [various] - toggle form options
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".js-toggle-form-options", function () {
        NX.toggleFormOptions($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [pushstate] - change url in address bar
     * -------------------------------------------------------------------------------------------------*/
    $(".project-top-nav").on("click", 'a', function () {
        NX.expandTabbedPage($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [add-edit-project] - add or edit project button clicked
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.add-edit-project-button', function () {
        NX.addEditProjectButton($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [update user preference] - e.g. leftmenu, stats
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.update-user-ux-preferences', function () {
        NX.updateUserUXPreferences($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [apply filter button] - button clicked
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.apply-filter-button', function () {
        NX.applyFilterButton($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [lists main checkbox] - checkbox clicked
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("change", '.listcheckbox-all', function () {
        NX.listCheckboxAll($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [lists main checkbox] - checkbox clicked
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("change", '.listcheckbox', function () {
        NX.listCheckbox($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [clients & projects] - on selecting clients, update the projects dropdown to show clients projects
     *  [notes]
     *       - the clients dropdown must have the following
     *       - class="clients_and_projects_toggle"
     *       - data-projects-dropdown="id-of-the-projects-dropdown"
     *       - data-feed-request-type="filter_tickets" (as checked in feed controller)
     * --------------------------------------------------------------------------------------------------*/
    //client list has been reset or cleared
    $(document).on("select2:unselecting", ".clients_and_projects_toggle", function (e) {
        NX.clientAndProjectsClearToggle($(this));
    });
    //client list has been reset or cleared
    $(document).on("select2:select", ".clients_and_projects_toggle", function (e) {
        NX.clientAndProjectsToggle(e, $(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [project & users] - on selecting project, update users dropdown to show assigned users
     *  [notes]
     *       - the projects dropdown must have the following
     *       - class="projects_assigned_toggle"
     *       - data-assigned-dropdown="id-of-the-users-dropdown"
     * --------------------------------------------------------------------------------------------------*/
    //client list has been reset or cleared
    $(document).on("select2:unselecting", ".projects_assigned_toggle", function (e) {
        NX.projectsAndAssignedClearToggle($(this));
    });
    //client list has been reset or cleared
    $(document).on("select2:select", ".projects_assigned_toggle", function (e) {
        NX.projectAndAssignedCToggle(e, $(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [project & users] - on selecting project, update tasks dropdown to show  tasks
     *  [notes]
     *       - the projects dropdown must have the following
     *       - class="projects_my_tasks_toggle"
     *       - data-task-dropdown="id-of-the-task-dropdown"
     * --------------------------------------------------------------------------------------------------*/
    //project list has been reset or cleared
    $(document).on("select2:unselecting", ".projects_my_tasks_toggle", function (e) {
        //toggle task drop down
        NX.projectsTasksClearToggle($(this));
        //reset fields and button (for manual timer recording)
        NX.recordTaskTimeToggle('disable');
    });
    //projecct list - a project has been selected
    $(document).on("select2:select", ".projects_my_tasks_toggle", function (e) {
        //populate the tasks dropdown
        NX.projectAssignedTasksToggle(e, $(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [toggle ticket editor or view mode]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.ticket-editor-toggle', function () {
        NX.ticketEditorToggle($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [default category icon] clicked - show alert
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".js-system-default-category", function () {
        $.confirm({
            theme: 'modern',
            type: 'blue',
            title: NXLANG.default_category,
            content: NXLANG.system_default_category_cannot_be_deleted,
            buttons: {
                cancel: {
                    text: NXLANG.close,
                    btnClass: ' btn-sm btn-outline-info',
                },
            },
        });
    });

    /** --------------------------------------------------------------------------------------------------
     *  [toggle ticket editor or view mode]
     * -------------------------------------------------------------------------------------------------*/
    $(document).ready(function () {
        $(document).on({
            mouseenter: function () {
                $(this).find('.js-hover-actions-target').show();
            },
            mouseleave: function () {
                $(this).find('.js-hover-actions-target').hide();
            }
        }, ".js-hover-actions");
    });



    /** --------------------------------------------------------------------------------------------------
     *  [general placeholder clicked]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.js-toggle-placeholder-element', function () {
        NX.togglePlaceHolders($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [general close button clicked]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.js-toggle-close-button', function () {
        NX.toggleCloseButtonElements($(this));
    });


    /** ----------------------------------------------------------
     *  [jquery.confirm]  
     *     - form fields in confirm dialogue (such as check boxes)
     * -----------------------------------------------------------*/
    //set the form value to the actual form value
    $(document).on('change', '.confirm_action_checkbox', function () {
        //hidden field
        $field = $("#" + $(this).attr('data-field-id'));
        if ($(this).is(':checked')) {
            $field.val('on')
        } else {
            $field.val('')
        }
    });


    /** --------------------------------------------------------------------------------------------------
     *  [task - checklist text clicked]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.js-card-checklist-toggle', function (e) {
        e.preventDefault();
        //toggle
        NX.toggleEditTaskChecklist($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [task - reset the task form]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.reset-card-modal-form', function (e) {
        NX.resetCardModal($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [task - checklist text clicked]
     * -------------------------------------------------------------------------------------------------*/
    $(document).ready(function () {
        $(document).on({
            mouseenter: function () {
                //hide all
                $('.checklist-item-delete').hide();
                $(this).find('.checklist-item-delete').show();
            },
            mouseleave: function () {
                $('.checklist-item-delete').hide();
            }
        }, ".checklist-item");
    });


    /** --------------------------------------------------------------------------------------------------
     *  [better ux on delete items] - remove item from list whilst the ajax happend in background
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.js-delete-ux', function () {
        NX.uxDeleteItem($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [toggle task timer buttons]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.js-timer-button', function (e) {
        NX.toggleTaskTimer($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [toggle settings left menu]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.js-toggle-settings-menu', function (e) {
        NX.toggleSettingsLeftMenu($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [convert lead to a customer]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", '.js-lead-convert-to-customer', function (e) {
        NX.convertLeadForm($(this), 'show');
    });
    $(document).on("click", '.js-lead-convert-to-customer-close', function (e) {
        NX.convertLeadForm($(this), 'hide');
    });


    /** --------------------------------------------------------------------------------------------------
     *  [top nav events - show]
     * -------------------------------------------------------------------------------------------------*/
    $("#topnav-notification-dropdown").on("show.bs.dropdown", function (e) {
        NX.eventsTopNav($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [top nav events - mark all events as read]
     * -------------------------------------------------------------------------------------------------*/
    $("#topnav-notification-mark-all-read").on('click', function (e) {
        NX.eventsTopNavMarkAllRead($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [top nav events - mark one event as read]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '.js-notification-mark-read-single', function (e) {
        NX.eventsMarkRead($(this), 'single');
    });

    /** --------------------------------------------------------------------------------------------------
     *  [app][change dynamic urls]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '.js-dynamic-url', function (e) {
        NX.browserPushState($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [settings][change dynamic urls]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '.js-dynamic-settings-url', function (e) {
        //set the dynamic url
        var self = $(this);
        var url = self.attr('data-url');
        var dynamic_url = 'app' + url;
        self.attr('data-dynamic-url', dynamic_url);
        //update browser address bar
        NX.browserPushState($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [settings][email template selected]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('change', '#selectEmailTemplate', function (e) {
        NX.loadEmailTemplate($(this));
    });



    /** --------------------------------------------------------------------------------------------------
     *  [settings][clear cache]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '#clearSystemCache', function (e) {
        NX.clearSystemCache($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [settings][clear cache]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '.toggle-invoice-tax', function (e) {
        NX.toggleInvoiceTaxEditing($(this));
    });


    /** ------------------------------------------------------------------------------
     *  - close any other static popover windows
     * ------------------------------------------------------------------------------- */
    $(document).on('click', '.js-elements-popover-button', function () {
        $('.js-elements-popover-button').not(this).popover('hide');
    });

    /** ---------------------------------------------------
     *  Show a popover with dynamic html content
     *  - html content is set in a hidden div
     *  - button has id of hidden div
     *  <button class="btn btn-info btn-sm js-dynamic-popover-button" tabindex="0" 
     *          data-popover-content="--html entities endoced (php) html here---"
     *          data-placement="top"
     *          data-title="Taxes Rates~"> Tax Rates~ </button>
     * -------------------------------------------------- */
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

    /** --------------------------------------------------------------------------------------------------
     *  [paypal] payment opion selected
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '#invoice-make-payment-button', function (e) {
        $("#invoice-buttons-container").hide();
        $("#invoice-pay-container").show();
    });



    /** --------------------------------------------------------------------------------------------------
     *  [paynow] cancel payment button clicked
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '#invoice-cancel-payment-button', function (e) {
        $("#invoice-pay-container").hide();
        $(".payment-gateways").hide();
        $("#invoice-buttons-container").show();
    });


    /** --------------------------------------------------------------------------------------------------
     *  [paypal] payment opion selected
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '.invoice-pay-gateway-selector', function (e) {
        NX.selectPaymentGateway($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [disable button on click]
     *  disable the button on click
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '.disable-on-click', function (e) {
        $(this).prop("disabled", true);
        $(this).addClass('button-loading-annimation');
    });

    /** --------------------------------------------------------------------------------------------------
     *  [disable button on click]
     *  disable a button on click and add the loading annimation. Good for Stripe and Payal buttons etc
     *  [IMPORTANT] do not use this class on ajax buttons. They have their own data-property for this
     *              using this will prevent ajax form submitting
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '.disable-on-click-loading', function (e) {

        $(this).prop("disabled", true); //this is stopping form submits
        $(this).addClass('button-loading-annimation');
    });


    /** --------------------------------------------------------------------------------------------------
     *  [disable button on click]
     *  disable a button, change to please wait, add loading annimation
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '.disable-on-click-please-wait', function (e) {
        $(this).html('&nbsp;&nbsp;&nbsp;' + NXLANG.please_wait + '...&nbsp;&nbsp;&nbsp;');
        $(this).prop("disabled", true); //this is stopping form submits
        $(this).addClass('button-loading-annimation');
    });



    /** --------------------------------------------------------------------------------------------------
     *  [shipping address] same as billing address
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", "#same_as_billing_address", function () {
        NX.shippingAddressSameBilling($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [toggle menu clicked] - update the autoscroll bar
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".main-hamburger-menu", function () {
        if (typeof navleft != 'undefined') {
            navLeftScroll.update();
        }
        //reset menu tooltips
        NXleftMenuToolTips();
    });
    $(document).on("click", ".settings-hamburger-menu", function () {
        if (typeof navleft != 'undefined') {
            navLeftScroll.update();
        }
    });




    /** --------------------------------------------------------------------------------------------------
     *  [select2] clear validation.js errors (if any) on selecting drop down
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('select2:select', '.select2-hidden-accessible', function () {
        try {
            if ($(this).valid()) {
                $(this).next('span').removeClass('error').addClass('valid');
            }
        } catch (err) {
            //we are expecting this error for none validated select2 elements. nothing to do here.
        }
    });


    /** --------------------------------------------------------------------------------------------------
     *  toggle target element
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".toggle-collapse", function (e) {
        e.preventDefault();
        var target = $(this).attr('href');
        if (target != '') {
            $(target).toggle();
        }
    });



    /*----------------------------------------------------------------
     *  [tax button clicked] - set popover dom
     *---------------------------------------------------------------*/
    $(document).on('click', '#billing-tax-popover-button', function (e) {
        //is the tax button enabled?
        if ($(this).hasClass('disabled')) {
            $(this).popover('hide');
            //error message
            NX.notification({
                type: 'error',
                message: NXLANG.add_lineitem_items_first
            });
            NXINVOICE.log('[invoicing] initialiseTaxPopover() - tax button is disabled');
        } else {
            NXINVOICE.toggleTaxDom($("#bill_tax_type").val());
        }
    });

    /*----------------------------------------------------------------
     *  [tax type] - tax type drop down has been changed
     *---------------------------------------------------------------*/
    $(document).on('change', '#billing-tax-type', function () {
        NXINVOICE.toggleTaxDom($(this).val());
    });


    /*----------------------------------------------------------------
     *  [tax popover - submit button] - clicked
     *---------------------------------------------------------------*/
    $(document).on('click', '#billing-tax-popover-update', function (e) {
        //update tax type
        NXINVOICE.updateTax();
    });



    /*----------------------------------------------------------------
     *  [adjustments button clicked] - set popover dom
     *---------------------------------------------------------------*/
    $(document).on('click', '#billing-adjustment-popover-button', function () {
        NXINVOICE.toggleAdjustmentDom();
    });

    /*----------------------------------------------------------------
     *  [adjustments popover - submit button] - clicked
     *---------------------------------------------------------------*/
    $(document).on('click', '#billing-adjustment-popover-update', function (e) {
        //update tax type
        NXINVOICE.updateAdjustment();
    });

    /*----------------------------------------------------------------
     *  [adjustments popover - remove button] - clicked
     *---------------------------------------------------------------*/
    $(document).on('click', '#billing-adjustment-popover-remove', function (e) {
        //update tax type
        NXINVOICE.removeAdjustment();
    });


    /*----------------------------------------------------------------
     *  [discount button clicked] - set popover dom
     *---------------------------------------------------------------*/
    $(document).on('click', '#billing-discounts-popover-button', function () {
        //is the discounts button enabled?
        if ($(this).hasClass('disabled')) {
            $(this).popover('hide');
            //error message
            NX.notification({
                type: 'error',
                message: NXLANG.add_lineitem_items_first
            });
        } else {
            NXINVOICE.toggleDiscountDom($("#bill_discount_type").val());
        }
    });


    /*----------------------------------------------------------------
     *  [discount type] - tax type drop down has been changed
     * ------------------------------------------------------------*/
    $(document).on('change', '#js-billing-discount-type', function () {
        NXINVOICE.toggleDiscountDom($(this).val());
    });


    /*----------------------------------------------------------------
     *  [discount popover - submit button] - clicked
     * ------------------------------------------------------------*/
    $(document).on('click', '#billing-discount-popover-update', function (e) {
        //update tax type
        NXINVOICE.updateDiscount();
    });


    /*----------------------------------------------------------------
     *  [line item] - add new blank line button has been clicked
     * ------------------------------------------------------------*/
    $(document).on('click', '#billing-item-actions-blank', function (e) {
        NXINVOICE.DOM.itemNewLine();
    });


    /*----------------------------------------------------------------
     *  [line item] - add new blank line button has been clicked
     * ------------------------------------------------------------*/
    $(document).on('click', '#billing-time-actions-blank', function (e) {
        NXINVOICE.DOM.timeNewLine();
    });


    /*----------------------------------------------------------------
     *  [line item] - delete line button has been clicked
     * ------------------------------------------------------------*/
    $(document).on('click', '.js-billing-line-item-delete', function (e) {
        NXINVOICE.DOM.deleteLine($(this));
    });


    /*----------------------------------------------------------------
     *  [bill item] - add selected bill items button clicked
     * ------------------------------------------------------------*/
    $(document).on('click', '#itemsModalSelectButton', function (e) {
        NXINVOICE.DOM.addSelectedProductItems($(this));
    });


    /*----------------------------------------------------------------
     *  [epxense item] - add selected expenses button clicked
     * ------------------------------------------------------------*/
    $(document).on('click', '#expensesModalSelectButton', function (e) {
        NXINVOICE.DOM.addSelectedExpense($(this));
    });



    /*----------------------------------------------------------------
     *  [epxense item] - add time billing button clicked
     * ------------------------------------------------------------*/
    $(document).on('click', '#timebillingModalSelectButton', function (e) {
        NXINVOICE.DOM.addSelectedTimebilling($(this));
    });


    /*----------------------------------------------------------------
     *  [tax type] - tax type drop down has been changed
     * ------------------------------------------------------------*/
    $(document).on('change keyup input paste', '.calculation-element', function () {
        NXINVOICE.CALC.reclaculateBill(self);
    });


    /*----------------------------------------------------------------
     *  [deciamls] keep 2 deciaml places for all number fields
     * -----------------------------------------------------------*/
    $(".decimal-field").blur(function () {
        this.value = parseFloat(this.value).toFixed(2);
    });


    /*----------------------------------------------------------------
     *  [save changes]
     * ------------------------------------------------------------*/
    $(document).on("click", '#billing-save-button', function () {
        NXINVOICE.CALC.saveBill($(this));
    });


    /*----------------------------------------------------------------
     *  [tax type] - tax type drop down has been changed
     * ------------------------------------------------------------*/
    $(document).on('change keyup input paste', '.js_line_validation_item', function () {
        NXINVOICE.DOM.revalidateItem($(this));
    });


    /*----------------------------------------------------------------
     *  [paynow] button clicked
     * -----------------------------------------------------------*/
    $(document).on('click', '#invoice-make-payment-button', function (e) {
        $("#invoice-buttons-container").hide();
        $("#invoice-pay-container").show();
    });

    //prevent event dropwon from closing on click event
    $(document).on('click', '.top-nav-events', function (e) {
        e.stopPropagation();
    });

    //prevent event dropwon from closing on click event
    $(document).on('click', '.js-do-not-close-on-click', function (e) {
        e.stopPropagation();
    });

    //prevent event dropwon from closing on click of selecte2 on topnav timer
    $(document).on('click', '.js-do-not-close-on-click > .select2-search__field', function (e) {
        e.stopPropagation();
    });



    /*----------------------------------------------------------------
     *  show plan modal window
     * -----------------------------------------------------------*/
    $(document).on('click', '.show-modal-button', function () {
        var title = $(this).attr('data-modal-title');
        //change title (if applicable)
        $("#plainModalTitle").html(title);
        //reset body
        $("#plainModalBody").html('');
        //modal size (modal-lg | modal-sm | modal-xl)
        var modal_size = $(this).attr('data-modal-size');
        if (modal_size == '' || modal_size == null) {
            modal_size = 'modal-lg';
        }
        //set modal size
        $("#plainModalContainer").addClass(modal_size);
    });


    /*----------------------------------------------------------------
     *  show common modal window. This function set the ajax attr
     *  for for the modal window, that has been triggered by a button
     * 
     * -----------------------------------------------------------*/
    $(document).on('click', '.edit-add-modal-button', function () {

        //variables
        var url = $(this).attr('data-url');
        var modal_title = $(this).attr('data-modal-title');
        var action_url = $(this).attr('data-action-url');
        var action_class = $(this).attr('data-action-ajax-class');
        var action_loading_target = $(this).attr('data-action-ajax-loading-target');
        var action_method = $(this).attr('data-action-method');
        var action_type = $(this).attr('data-action-type');
        var action_form_id = $(this).attr('data-action-form-id');
        var add_class = $(this).attr('data-add-class');
        var top_padding = $(this).attr('data-top-padding'); //set to 'none'
        var button_loading_annimation = $(this).attr('data-button-loading-annimation');


        //modal-lg modal-sm modal-xl modal-xs
        var modal_size = $(this).attr('data-modal-size');
        if (modal_size == '' || modal_size == null) {
            modal_size = 'modal-lg';
        }

        //objects
        var $button = $("#commonModalSubmitButton");

        //enable button - incase it was previously disable by another function
        $button.prop("disabled", false);

        //set modal size
        $("#commonModalContainer").removeClass('modal-lg modal-sm modal-xl modal-xs');
        $("#commonModalContainer").addClass(modal_size);

        //update form style
        var form_style = $(this).attr('data-form-design');
        if (form_style != '') {
            //remove previous styles
            $("#commonModalForm").removeClass('form-material')
            $("#commonModalForm").addClass(form_style)
        }

        //add custom class
        if (add_class != '') {
            $("#commonModalContainer").addClass(add_class);
        }


        //change title
        $("#commonModalTitle").html(modal_title);
        //reset body
        $("#commonModalBody").html('');
        //hide footer
        $("#commonModalFooter").hide();
        //change form action
        $("#commonModalForm").attr('action', action_url);


        //[submit button] - reset
        $button.show();
        $button.removeClass('js-ajax-ux-request');
        $button.addClass(action_class);

        //defaults
        $("#commonModalHeader").show();
        $("#commonModalFooter").show();
        $("#commonModalCloseButton").show();
        $("#commonModalCloseIcon").show();
        $("#commonModalExtraCloseIcon").hide();

        //hidden elements
        if ($(this).attr('data-header-visibility') == 'hidden') {
            $("#commonModalHeader").hide();
        }
        if ($(this).attr('data-footer-visibility') == 'hidden') {
            $("#commonModalFooter").hide();
        }
        if ($(this).attr('data-close-button-visibility') == 'hidden') {
            $("#commonModalCloseButton").hide();
        }
        if ($(this).attr('data-header-close-icon') == 'hidden') {
            $("#commonModalCloseIcon").hide();
        }
        if ($(this).attr('data-header-extra-close-icon') == 'visible') {
            $("#commonModalExtraCloseIcon").show();
        }

        //remove top padding
        if (top_padding == 'none') {
            $("#commonModalBody").addClass('p-t-0');
        } else {
            $("#commonModalBody").removeClass('p-t-0');
        }

        //[submit button] - update attributes etc (if provided)
        //$button.addClass(action_class);
        $button.attr('data-url', action_url);
        $button.attr('data-loading-target', action_loading_target);
        $button.attr('data-ajax-type', action_method);

        //add loading annimation on button
        if (button_loading_annimation == 'yes') {
            $button.attr('data-button-loading-annimation', 'yes');
        }

        //form post
        if (action_type == "form") {
            $button.attr('data-type', 'form');
            $button.attr('data-form-id', action_form_id);
        }
    });


    /*----------------------------------------------------------------
     *  show actions modal window - action modal
     * -----------------------------------------------------------*/
    $(document).on('click', '.actions-modal-button', function () {

        //variables
        var url = $(this).attr('data-url');
        var modal_title = $(this).attr('data-modal-title');
        var action_url = $(this).attr('data-action-url');
        var action_method = $(this).attr('data-action-method');
        var add_body_class = $(this).attr('data-body-class');


        //additional variable
        var action_type = $(this).attr('data-action-type');
        var action_form_id = $(this).attr('data-action-form-id');

        //add class to modal body
        if (add_body_class != '') {
            $("#actionsModalBody").addClass(add_body_class);
        }

        //objects
        var $button = $("#actionsModalButton");

        //change title
        $("#actionsModalTitle").html(modal_title);
        //reset body
        $("#actionsModalBody").html('');
        //hide footer
        $("#actionsModalFooter").hide();

        //$button.addClass(action_class);
        $button.attr('data-url', action_url);
        $button.attr('data-ajax-type', action_method);
        $button.attr('data-type', action_type);
        $button.attr('data-form-id', action_form_id);
        $button.attr('data-skip-checkboxes-reset', true);
    });


    /*----------------------------------------------------------------
     * show category hover button
     *--------------------------------------------------------------*/
    $(document).on('mouseover', '.kb-category', function () {
        $(this).find(".kb-hover-icons").show();
    });
    $(document).on('mouseout', '.kb-category', function () {
        $(this).find(".kb-hover-icons").hide();
    });


    /*----------------------------------------------------------------
     * Better ux on teaks checkbox click
     *--------------------------------------------------------------*/
    $(document).on('click', '.toggle_task_status', function () {
        var parentid = $(this).attr('data-container');
        var parent = $("#" + parentid);
        if ($(this).prop("checked") == true) {
            parent.addClass('task-completed');
        } else {
            parent.removeClass('task-completed');
        }
    });


    /** --------------------------------------------------------------------------------------------------
     *  toggle tasks and leads custom fields
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", "#custom-fields-panel-edit-button", function (e) {
        e.preventDefault();
        $(".custom-fields-panel-edit").hide();
        $("#custom-fields-panel").show();
    });
    $(document).on("click", "#custom-fields-panel-close-button", function (e) {
        e.preventDefault();
        $("#custom-fields-panel").hide();
        $(".custom-fields-panel-edit").show();
    });





    /*----------------------------------------------------------------
     *  create modal - start
     * -----------------------------------------------------------*/
    $(document).on('click', '.create-modal-button', function () {


        //set modal splash message
        $("#create-modal-splash-text").html($(this).attr('data-splash-text'));

        //set create url
        $("#create-new-client-button").attr('data-url', $(this).attr('data-new-client-url'));

        //show defauly set
        $(".create-modal-option-contaiers").hide();
        $("#option-existing-client-container").show();


    });


    /*----------------------------------------------------------------
     *  create modal - selector
     * -----------------------------------------------------------*/
    $(document).on('click', '.create-modal-selector', function () {

        var target_containter = $(this).attr('data-target-container');

        //hide all containers
        $(".create-modal-option-contaiers").hide();

        //show the target container
        $("#" + target_containter).show();

    });



    /*----------------------------------------------------------------
     *  create modal - selector
     * -----------------------------------------------------------*/
    $(document).on('click', '.client-type-selector', function () {

        var target_containter = $(this).attr('data-target-container');

        //hide all containers
        $(".client-selector-container").hide();
        $(".client-type-selector").removeClass('active');

        //set clients election type
        $("#client-selection-type").val($(this).attr('data-type'))

        //show the target container
        $("#" + target_containter).show();
        $(this).addClass('active');


    });


    /*----------------------------------------------------------------
     *  [stop topnav timer] - clicked
     *---------------------------------------------------------------*/
    $(document).on('click', '#my-timer-time-topnav-stop-button', function (e) {
        //hide timer
        $("#my-timer-container-topnav").hide();
    });



    /** --------------------------------------------------------------------------------------------------
     *  [card assigned user - add button clicked]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".card-task-assigned, .card-lead-assigned", function (e) {
        NXCardsAssignedSelect();
    });


    /** --------------------------------------------------------------------------------------------------
     *  [reminder panel] - toggle
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".js-toggle-reminder-panel", function () {
        NX.toggleReminderPanel($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [toggle row with settings on a table] - this is reusable
     *  - settings button must have the following attributes
     *       - [data-settings-row-id] - corresponding to the row with the settings
     *       - [data-settings-common-rows] - a common class for the parent and also settings rows
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".js-toggle-table-settings-row", function () {
        NX.toggleTableSettingsRow($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [custom field] - standard form - required checked box clicked
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("change", ".custom-fields-standard-form-required-checkbox", function () {
        nxAjaxUxRequest($(this));
    });

    /** --------------------------------------------------------------------------------------------------
     *  [custom field] - standard form - how to display the form
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("change", "#custom_fields_display_setting", function () {
        nxAjaxUxRequest($(this));
    });



    /** --------------------------------------------------------------------------------------------------
     *  [cliet modules permissions] - toggle the permissions in add/edit clients modal
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("select2:select", "#client_app_modules", function (e) {

        var selection = e.params.data.id;
        console.log(selection);
        //toggle permissions
        if (selection == 'system') {
            $("#client_app_modules_pemissions").hide();
        } else {
            $("#client_app_modules_pemissions").show();
        }
    });


    /** --------------------------------------------------------------------------------------------------
     *  [reminders] - close reminder buttom clicked
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", "#close_reminder_button", function () {
        //hide the calendar
        $("#card-reminder-create-container").hide();
        //show add button
        $("#card-reminder-create-button").show();
    });

    /** --------------------------------------------------------------------------------------------------
     *  [reminders] - edit reminder icon
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", "#card-a-reminder-edit-button", function () {
        //show edit button
        $("#card-a-reminder-buttons").toggle();
    });


    /** --------------------------------------------------------------------------------------------------
     *  [top nav reminders - show]
     * -------------------------------------------------------------------------------------------------*/
    $("#topnav-reminders-dropdown").on("show.bs.dropdown", function (e) {
        NX.remindersTopNav($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [top nav reminder - delete one reminder]
     * -------------------------------------------------------------------------------------------------*/
    $(document).on('click', '.js-reminder-mark-read-single', function (e) {
        NX.remindersMarkRead($(this), 'single');
    });


    /** --------------------------------------------------------------------------------------------------
     *  [top nav reminders - delete all]
     * -------------------------------------------------------------------------------------------------*/
    $("#topnav-reminders-delete-all").on('click', function (e) {
        //aajx request
        nxAjaxUxRequest($(this));
        //hide icon
        $("#topnav-reminders-dropdown").hide();
    });


    /** --------------------------------------------------------------------------------------------------
     *  [tasks/leads] - cancel tags editing button clicked
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", "#card-tags-button-cancel", function () {
        $("#card-tags-edit-tags-container").hide();
        $("#card-tags-current-tags-container").show();
    });
    $(document).on("click", "#card-tags-button-edit", function () {
        $('#card_tags').select2('destroy');
        $('#card_tags').select2(null).trigger("change");

        //reset select2 dropdown
        $('#card_tags').select2({
            theme: "bootstrap",
            width: null,
            containerCssClass: ':all:',
            tags: true,
            multiple: true,
            tokenSeparators: [' '],
        }).val(NX.array_1).trigger("change");
        //show and hide
        $("#card-tags-edit-tags-container").show();
        $("#card-tags-current-tags-container").hide();
    });



    /** --------------------------------------------------------------------------------------------------
     *  [notifications panel] - toggle
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".js-toggle-notifications-panel", function () {
        NX.toggleNotificationsPanel($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [side panel with menu] - menu clicked
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".right-sidepanel-menu", function () {
        $(".right-sidepanel-menu").removeClass('active');
        $(this).addClass('active');
    });



    /** --------------------------------------------------------------------------------------------------
     *  [close side panels] - toggle
     * -------------------------------------------------------------------------------------------------*/
    $(document).on("click", ".js-close-side-panels", function () {
        NX.closeSidePanel($(this));
    });


    /** --------------------------------------------------------------------------------------------------
     *  [projects & milestones] - on selecting project, update the milestones dropdown
     * --------------------------------------------------------------------------------------------------*/
    //client list has been reset or cleared
    $(document).on("select2:unselecting", ".projects_and_milestones_toggle", function (e) {
        NX.projectsAndMilestonesClearToggle($(this));
    });
    //client list has been reset or cleared
    $(document).on("select2:select", ".projects_and_milestones_toggle", function (e) {
        NX.projectsAndMilestonesToggle(e, $(this));
    });



    /*----------------------------------------------------------------
     *  proposals modal - client type selector
     * -----------------------------------------------------------*/
    $(document).on('click', '.customer-type-selector', function () {

        var target_containter = $(this).attr('data-target-container');

        //hide all containers
        $(".customer-selector-container").hide();
        $(".customer-type-selector").removeClass('active');

        //set clients election type
        $("#customer-selection-type").val($(this).attr('data-type'))

        //show the target container
        $("#" + target_containter).show();
        $(this).addClass('active');

    });


    /** --------------------------------------------------------------------------------------------------
     *  [tasks] - creating a new task. Show client users for assignment, when project has been
     *            selected
     * --------------------------------------------------------------------------------------------------*/
    //client list has been reset or cleared
    $(document).on("select2:unselecting", ".projects_assigned_client_toggle", function (e) {
        NXTaskProjectToggleClear($(this));
    });
    //client list has been reset or cleared
    $(document).on("select2:select", ".projects_assigned_client_toggle", function (e) {
        NXTaskProjectToggle(e, $(this));
    });


});