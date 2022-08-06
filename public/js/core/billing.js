"use strict";

/*----------------------------------------------------------------
 * output debug data - only if debug mode is enabled
 * [returns] - bool
 *--------------------------------------------------------------*/
NXINVOICE.log = function (payload1, payload2) {
    if (NX.debug_javascript) {
        if (payload1 != undefined) {
            console.log(payload1);
        }
        if (payload2 != undefined) {
            console.log(payload2);
        }
    }
};


/*----------------------------------------------------------------
 * [TOGGLE DISCOUNT AND TAX BUTTONS]
 * - enable or disable these buttons, if the billing has no value
 *--------------------------------------------------------------*/
NXINVOICE.toggleTaxDiscountButtons = function (value = 0) {

    var bill_final_amount = Number($("#bill_final_amount").val());
    var bill_subtotal = Number($("#bill_subtotal").val());

    //log
    NXINVOICE.log('[billing] toggleTaxDiscountButtons() - based on billing value [bill_final_amount]: (' + bill_final_amount + ')');

    //dom
    var $discount_button = $("#billing-discounts-popover-button");
    var $tax_button = $("#billing-tax-popover-button");

    //does the bill have a laue

}


/*----------------------------------------------------------------
 * Bill has loaded. Let do some intial tasks
 *--------------------------------------------------------------*/

NXINVOICE.DOM.domState = function () {

    NXINVOICE.log('[billing] state() - setting billing doma state- [payload]', NXINVOICE.DATA.INVOICE);

    //toggle discount and tax buttons

    NXINVOICE.toggleTaxDiscountButtons();

    //remove class from crumbs to avoid actions when check boxes are ticked
    $("#breadcrumbs").removeClass('list-pages-crumbs');
}



/*----------------------------------------------------------------
 * properly format money
 *--------------------------------------------------------------*/

function nxFormatDecimal(number = 0) {

    return accounting.formatNumber(number, 2, "", ".");

}



/*----------------------------------------------------------------
 * [TOGGLE TAX POPOVER]
 * - set the tax DOm elements
 *--------------------------------------------------------------*/
NXINVOICE.toggleTaxDom = function (tax_type = '') {



    NXINVOICE.log('[billing] initialiseTaxPopover() - toggling tax popover - [tax_type]: ' + tax_type);

    //popover elements visibility
    if (tax_type == 'inline') {
        $("#billing-tax-popover-inline-info").show();
        $("#billing-tax-popover-summary-info").hide();
    }

    if (tax_type == 'summary') {
        $("#billing-tax-popover-inline-info").hide();
        $("#billing-tax-popover-summary-info").show();
    }

    if (tax_type == 'none') {
        $("#billing-tax-popover-inline-info").hide();
        $("#billing-tax-popover-summary-info").hide();
    }

    //update tax type
    $("#billing-tax-type").val(tax_type);


    //preselect check boxes
    $('#billing-logic-taxes :selected').each(function (i, selected) {
        var element_id = $(selected).attr('id');
        $("#" + element_id).prop("checked", true);
    });
};


/*----------------------------------------------------------------
 * [TOGGLE DICOUNTS POPOVER]
 * - set visibility of the form fields
 *--------------------------------------------------------------*/
NXINVOICE.toggleDiscountDom = function (bill_discount_type = '') {

    //get current values
    var bill_discount_value = '';

    //log
    NXINVOICE.log('[billing] toggleDiscountDom() setting popover state [current_discount_type]: (' + bill_discount_type + ')');


    //dom
    var $fixed_container = $("#billing-discounts-popover-fixed");
    var $percentage_container = $("#billing-discounts-popover-percentage");

    //default visibility
    $fixed_container.hide();
    $percentage_container.hide();

    //fixed discount
    if (bill_discount_type == 'fixed') {
        var bill_discount_value = $("#bill_discount_amount").val();
    }

    //percentage discount
    if (bill_discount_type == 'percentage') {
        var bill_discount_value = $("#bill_discount_percentage").val();
    }


    //percentage discount
    if (bill_discount_type == 'none') {
        $("#js-billing-discount-type").val('none')
        var bill_discount_value = $("#bill_discount_percentage").val();
    }


    //fixed discount
    if (bill_discount_type == 'fixed') {
        $percentage_container.hide();
        $fixed_container.show();
        $("#js-billing-discount-type").val('fixed')
        $("#js_bill_discount_amount").val(nxFormatDecimal(bill_discount_value));
    }

    //percentage discount
    if (bill_discount_type == 'percentage') {
        $fixed_container.hide();
        $percentage_container.show();
        $("#js-billing-discount-type").val('percentage')
        $("#js_bill_discount_percentage").val(Number(bill_discount_value));
    }

}


/*----------------------------------------------------------------
 * [TOGGLE ADJUSTMENTS POPOVER]
 *--------------------------------------------------------------*/
NXINVOICE.toggleAdjustmentDom = function () {

    NXINVOICE.log('[billing] toggleAdjustmentDom() setting adjustment popover state');

    //set the data from the 
    var bill_adjustment_description = $("#bill_adjustment_description").val();
    var bill_adjustment_amount = $("#bill_adjustment_amount").val();

    //set forn data
    $("#js_bill_adjustment_amount").val(nxFormatDecimal(bill_adjustment_amount));
    $("#js_bill_adjustment_description").val(bill_adjustment_description);

}

/*----------------------------------------------------------------
 * update the amount due bill label
 *--------------------------------------------------------------*/

NXINVOICE.DOM.updateAmountDue = function (bill_final_amount = 0) {

    NXINVOICE.log("[billing] NXINVOICE.DOM.updateAmountDue() - updating amount due label [bill_final_amount]: (" + bill_final_amount + ")");

    //amount due label
    var amount_due_label = $("#billing-details-amount-due");

    //calculate based on the current bill balance
    var amount_due = bill_final_amount - Number($("#bill_total_payments").val());
    if (amount_due > 0) {
        //update amount due and turn lable to red color
        amount_due_label.html(accounting.formatMoney(amount_due)).removeClass("label-success").addClass("label-danger");
    } else {
        //update amount due and turn lable to red color
        amount_due_label.html(accounting.formatMoney(amount_due)).removeClass("label-danger").addClass("label-success");
    }
}


/*----------------------------------------------------------------
 * [LINE ITEM]
 * -add new blank line item
 *--------------------------------------------------------------*/
NXINVOICE.DOM.itemNewLine = function (data = {}) {

    NXINVOICE.log("[billing] NXINVOICE.DOM.itemNewLine() - cloning time line - [payload]");
    NXINVOICE.log(data);

    //get data is any was provided
    var item_unit = (data.item_unit != null) ? data.item_unit : '';
    var item_quantity = (data.item_quantity != null) ? data.item_quantity : '';
    var item_description = (data.item_description != null) ? data.item_description : '';
    var item_rate = (data.item_rate != null) ? data.item_rate : '';
    var item_total = (data.item_total != null) ? data.item_total : '';
    var item_linked_type = (data.item_linked_type != null) ? data.item_linked_type : '';
    var item_linked_id = (data.item_linked_id != null) ? data.item_linked_id : '';

    //check for deuplicate licked items (expense or task etc)
    if (item_linked_type != '') {
        var check = item_linked_type + '|' + item_linked_id; //e.g. data-duplicate-check='expense|23'
        if ($("input[data-duplicate-check='" + check + "']").length > 0) {
            NXINVOICE.log("[billing] NXINVOICE.DOM.itemNewLine() - the item being added is a duplicate. Will skip (" + check + ")");
            //note this duplcate error
            NXINVOICE.DATA.expense_duplicate_count++;
            return;
        }
    }

    //new element (plain)
    var lineitem = $("#billing-line-template-plain").find('tr').first().clone();


    //prefill if any data has been sent
    lineitem.find(".js_item_description").html(item_description);
    lineitem.find(".js_item_quantity").val(item_quantity);
    lineitem.find(".js_item_unit").val(item_unit);
    lineitem.find(".js_item_rate").val(item_rate);
    lineitem.find(".js_item_total").val(item_total);
    lineitem.find(".js_item_linked_type").val(item_linked_type);
    lineitem.find(".js_item_linked_id").val(item_linked_id);
    lineitem.find(".js_linetax_rate").val('');

    //add unique id to the ide
    var uniqueid = NX.uniqueID();

    //add unique id to the table row <tr>
    lineitem.attr('id', uniqueid);

    //change field names to name='foo[xxx]' array with unique id
    lineitem.find(".js_item_description").attr("name", "js_item_description[" + uniqueid + "]");
    lineitem.find(".js_item_quantity").attr("name", "js_item_quantity[" + uniqueid + "]");
    lineitem.find(".js_item_unit").attr("name", "js_item_unit[" + uniqueid + "]");
    lineitem.find(".js_item_rate").attr("name", "js_item_rate[" + uniqueid + "]");
    lineitem.find(".js_item_total").attr("name", "js_item_total[" + uniqueid + "]");
    lineitem.find(".js_linetax_rate").attr("name", "js_linetax_rate[" + uniqueid + "]");
    lineitem.find(".js_item_linked_type").attr("name", "js_item_linked_type[" + uniqueid + "]");
    lineitem.find(".js_item_linked_id").attr("name", "js_item_linked_id[" + uniqueid + "]");
    lineitem.find(".js_item_linked_type").attr("data-duplicate-check", item_linked_type + "|" + item_linked_id); //used for tracking duplicates
    lineitem.find(".js_item_type").attr("name", "js_item_type[" + uniqueid + "]");



    //add hidden field (to track unique id)
    lineitem.append('<input type="hidden" name="uniqueid" value="' + uniqueid + '">');

    //append finished line to the table
    $("#billing-items-container").append(lineitem);

    //remove button focus
    self.blur();
};





/*----------------------------------------------------------------
 * [LINE ITEM]
 * -add new blank line item
 *--------------------------------------------------------------*/
NXINVOICE.DOM.timeNewLine = function (data = {}) {

    NXINVOICE.log("[billing] NXINVOICE.DOM.timeNewLine() - cloning time line - [payload]");
    NXINVOICE.log(data);

    //get data is any was provided
    var item_description = (data.item_description != null) ? data.item_description : '';
    var item_unit = (data.item_unit != null) ? data.item_unit : NXLANG.invoice_time_unit;
    var item_hours = (data.item_hours != null) ? data.item_hours : '';
    var item_minutes = (data.item_minutes != null) ? data.item_minutes : '';
    var item_rate = (data.item_rate != null) ? data.item_rate : '';
    var item_total = (data.item_total != null) ? data.item_total : '';
    var item_linked_id = (data.item_linked_id != null) ? data.item_linked_id : '';
    var item_timers_list = (data.item_timers_list != null) ? data.item_timers_list : '';


    //round item total
    item_total = accounting.toFixed(item_total, 2);

    //new element (plain or time)
    var lineitem = $("#billing-line-template-time").find('tr').first().clone();


    //prefill if any data has been sent
    lineitem.find(".js_item_description").html(item_description);
    lineitem.find(".js_item_hours").val(item_hours);
    lineitem.find(".js_item_minutes").val(item_minutes);
    lineitem.find(".js_item_unit").val(item_unit);
    lineitem.find(".js_item_rate").val(item_rate);
    lineitem.find(".js_item_total").val(item_total);
    lineitem.find(".js_item_linked_id").val(item_linked_id);
    lineitem.find(".js_linetax_rate").val('');
    lineitem.find(".js_item_unit").val(item_unit);
    lineitem.find(".js_item_timers_list").val(item_timers_list);


    //add unique id to the ide
    var uniqueid = NX.uniqueID();

    //add unique id to the table row <tr>
    lineitem.attr('id', uniqueid);

    //change field names to name='foo[xxx]' array with unique id
    lineitem.find(".js_item_description").attr("name", "js_item_description[" + uniqueid + "]");
    lineitem.find(".js_item_hours").attr("name", "js_item_hours[" + uniqueid + "]");
    lineitem.find(".js_item_minutes").attr("name", "js_item_minutes[" + uniqueid + "]");
    lineitem.find(".js_item_unit").attr("name", "js_item_unit[" + uniqueid + "]");
    lineitem.find(".js_item_rate").attr("name", "js_item_rate[" + uniqueid + "]");
    lineitem.find(".js_item_total").attr("name", "js_item_total[" + uniqueid + "]");
    lineitem.find(".js_linetax_rate").attr("name", "js_linetax_rate[" + uniqueid + "]");
    lineitem.find(".js_item_linked_type").attr("name", "js_item_linked_type[" + uniqueid + "]");
    lineitem.find(".js_item_linked_id").attr("name", "js_item_linked_id[" + uniqueid + "]");
    lineitem.find(".js_item_timers_list").attr("name", "js_item_timers_list[" + uniqueid + "]");
    lineitem.find(".js_item_type").attr("name", "js_item_type[" + uniqueid + "]");



    //add hidden field (to track unique id)
    lineitem.append('<input type="hidden" name="uniqueid" value="' + uniqueid + '">');

    //append finished line to the table
    $("#billing-items-container").append(lineitem);

    //remove button focus
    self.blur();
};


/*-----------------------------------------------------------------------------------------------------------
 * [RECALCULATE LINE ITEMS]
 * - validate and calculate each time items todal
 * ------------------------------------------------------------------------------------------------------------------*/
NXINVOICE.CALC.recalculateLines = function () {

    NXINVOICE.log("[billing] recalculateLines() - validating and recalculating each line item");

    //(1) ------------------------------ find each line item --------------------------------
    $("#billing-items-container").find(".billing-line-item").each(function () {

        NXINVOICE.log("[billing] recalculateLines() - found a line item. Now validating and calculating it");

        var lineitem = $(this);
        var id = lineitem.attr('id');
        var type = lineitem.attr('type');

        //each input fields
        var description = lineitem.find(".js_item_description");
        var quantity = lineitem.find(".js_item_quantity").val();
        var unit = lineitem.find(".js_item_unit").val();
        var rate = lineitem.find(".js_item_rate").val();
        var total = lineitem.find(".js_item_total");
        var selected_taxes = lineitem.find(".js_linetax_rate");
        var tax = lineitem.find(".js_linetax_total");

        //for time items
        var hours = lineitem.find(".js_item_hours").val();
        var minutes = lineitem.find(".js_item_minutes").val();



        //get the total line tax for this row
        var line_tax = 0;
        if ($("#bill_tax_type").val() == 'lineitem') {
            selected_taxes.find(':selected').each(function () {
                line_tax += Number($(this).val());
            });
        }

        NXINVOICE.log("[billing] recalculateLines() - [quantity]: (" + quantity + ") - [rate]: (" + rate + ") - [linetax]: (" + line_tax + ") - [type]: (" + type + ")");


        /** ---------------------------------------------------
         * SET ZERO DEFAULTS
         * ignore if this is the currently focused item
         * [02-04-2021]
         * --------------------------------------------------*/
        if (hours == '' || hours == null) {
            if (lineitem.find(".js_item_hours").is(":focus")) {
                //do nothing
            } else {
                lineitem.find(".js_item_hours").val(0);
            }
        }
        if (minutes == '' || minutes == null) {
            if (lineitem.find(".js_item_minutes").is(":focus")) {
                //do nothing
            } else {
                lineitem.find(".js_item_minutes").val(0);
            }
        }

        /** ---------------------------------------------------
         * PLAIN LINE ITEMS
         * --------------------------------------------------*/
        if (type == 'plain') {
            //if row is valid, workout total
            if (quantity > 0 && rate > 0) {
                //line total and tax
                var linetotal = quantity * rate;
                total.val(nxFormatDecimal(linetotal));
                //work out tax
                var linetax = linetotal * line_tax / 100;
                //save line tax (sum) for later calculations
                tax.val(linetax);
                //increase bill total
                NXINVOICE.DATA.calc_total += linetotal;
                NXINVOICE.log("[billing] reclaculateBill() - line item is valid. [line item total]: " + linetotal);
            } else {
                NXINVOICE.log("[billing] reclaculateBill() - line item is invalid and is skipped");
                total.val('');
            }
        }

        /** ---------------------------------------------------
         * TIME LINE ITEMS
         * --------------------------------------------------*/
        if (type == 'time') {
            //if row is valid, workout total
            if ((hours > 0 || minutes > 0) && rate > 0) {

                //defaults minutes total
                var minutes_total = 0;
                //hours total
                var hours_total = hours * rate;
                //if we have minutes
                if (minutes > 0) {
                    minutes_total = (minutes / 60) * rate;
                }
                //line total
                var linetotal = hours_total + minutes_total;

                //round to 2 decimal places
                var linetotal = accounting.toFixed(linetotal, 2);

                //format to decimal
                total.val(nxFormatDecimal(linetotal));
                //work out tax
                var linetax = linetotal * line_tax / 100;
                //save line tax (sum) for later calculations
                tax.val(linetax);
                //increase bill total
                NXINVOICE.DATA.calc_total += linetotal;
                NXINVOICE.log("[billing] reclaculateBill() - line item is valid. [line item total]: " + linetotal);
            } else {
                NXINVOICE.log("[billing] reclaculateBill() - line item is invalid and is skipped");
                total.val('');
            }
        }

    });
}

/*----------------------------------------------------------------
 * [LINE ITEM]
 * -delete line item
 *--------------------------------------------------------------*/
NXINVOICE.DOM.deleteLine = function (self) {

    NXINVOICE.log("[billing] NXINVOICE.DOM.deleteLine() - deleteing line item");

    //find parent
    var lineitem = self.closest('tr');

    //remove it
    lineitem.remove();

    //recalculate bill
    NXINVOICE.CALC.reclaculateBill();

};


/*----------------------------------------------------------------
 * [ADD INVOICE ITEM]
 * -add the selected product as an bill item
 *--------------------------------------------------------------*/
NXINVOICE.DOM.addSelectedProductItems = function (self) {

    NXINVOICE.log("[billing] NXINVOICE.DOM.addSelectedProductItems() - adding items selected in add items modal");

    //count
    var count_selected = 0;

    //check if items were selected
    $("#items-list-table").find(".items-checkbox").each(function () {
        if ($(this).is(":checked")) {
            //save to object
            var data = {
                'item_description': $(this).attr('data-description'),
                'item_quantity': $(this).attr('data-quantity'),
                'item_unit': $(this).attr('data-unit'),
                'item_rate': $(this).attr('data-rate'),
                'item_total': $(this).attr('data-rate'),
            }

            //create new line item
            NXINVOICE.DOM.itemNewLine(data);

        }

        count_selected++;
    });

    //close modal or show error
    $("#itemsModal").modal('hide');


    //recalculate bill
    NXINVOICE.CALC.reclaculateBill();
};


/**----------------------------------------------------------------------
 * [ADD EXPENSE]
 * -add the selected expense as a bill line item
 *--------------------------------------------------------------*/
NXINVOICE.DOM.addSelectedExpense = function (self) {

    NXINVOICE.log("[billing] NXINVOICE.DOM.addSelectedExpense() - adding expenses selected in add expenses modal");

    //count
    var count_selected = 0;

    //duplicates checker
    NXINVOICE.DATA.expense_duplicate_count = 0;

    //check if expenses were selected
    $("#expenses-list-table").find(".expenses-checkbox").each(function () {
        if ($(this).is(":checked")) {
            //save to object
            var data = {
                'item_description': $(this).attr('data-description'),
                'item_quantity': $(this).attr('data-quantity'),
                'item_unit': $(this).attr('data-unit'),
                'item_rate': $(this).attr('data-rate'),
                'item_total': $(this).attr('data-rate'),
                'item_linked_type': 'expense',
                'item_linked_id': $(this).attr('data-expense-id'), //expense_id
            }

            //create new line expense
            NXINVOICE.DOM.itemNewLine(data);
        }

        count_selected++;
    });

    //reclaculate bill
    NXINVOICE.CALC.reclaculateBill();

    //error message about duplicated expense
    if (NXINVOICE.DATA.expense_duplicate_count) {
        NX.notification({
            type: 'error',
            message: NXLANG.selected_expense_is_already_on_invoice
        });
    }


    //close modal
    $("#expensesModal").modal('hide');
};


/**----------------------------------------------------------------------
 * [ADD TIME BILLING]
 * -add the selected time as a bill line item
 *--------------------------------------------------------------*/
NXINVOICE.DOM.addSelectedTimebilling = function (self) {

    NXINVOICE.log("[billing] NXINVOICE.DOM.addSelectedTimebilling() - adding hours selected in add time billing modal");

    //count
    var count_selected = 0;

    //check if items were selected
    $("#tasks-list-table").find(".tasks-checkbox").each(function () {
        if ($(this).is(":checked")) {
            //save to object
            var data = {
                'item_description': $(this).attr('data-description'),
                'item_hours': $(this).attr('data-hours'),
                'item_minutes': $(this).attr('data-minutes'),
                'item_rate': $(this).attr('data-rate'),
                'item_unit': $(this).attr('data-unit'),
                'item_total': $(this).attr('data-total'),
                'item_linked_type': $(this).attr('data-linked-type'),
                'item_linked_id': $(this).attr('data-linked-id'),
                'item_timers_list': $(this).attr('data-timers-list'),
            }
            //create new line expense
            NXINVOICE.DOM.timeNewLine(data);
        }

        count_selected++;
    });

    //reclaculate bill
    NXINVOICE.CALC.reclaculateBill();

    //close modal
    $("#timebillingModal").modal('hide');
};



/*----------------------------------------------------------------
 * [UPDATE TAX TYPE]
 * -set the selected tax type
 *--------------------------------------------------------------*/
NXINVOICE.updateTax = function () {

    //get tax type from popover form
    var tax_type = $("#billing-tax-type").val();


    NXINVOICE.log("[billing] updateTaxType() - updating tax type [type]: (" + tax_type + ")");

    //deselect all taxes
    $("#bill_logic_taxes").val([]);

    //[logic] update tax is being updates
    if (tax_type == 'summary') {
        $(".bill_col_tax").hide();
    }


    //tax table columns visibility
    if (tax_type == 'inline') {
        $(".bill_col_tax").show();
    }

    //tax table columns visibility
    if (tax_type == 'none') {
        $(".bill_col_tax").hide();
    }

    //[logic] update bill tax type
    $("#bill_tax_type").val(tax_type);

    // do this for each selected tax rate
    $("#billing-tax-popover-summary-info").find(".js_summary_tax_rate").each(function () {
        //mark as selected
        if ($(this).is(":checked")) {
            //get uniqie tax id
            var id = $(this).attr('data-tax-unique-id');
            $("#bill_logic_taxes option[id='" + id + "']").prop("selected", true);
        }
    });

    //close popover
    $('#billing-tax-popover-button').popover('hide');

    //recalculate bill
    NXINVOICE.CALC.reclaculateBill(self);
}


/*----------------------------------------------------------------
 * [UPDATE ADJUSTMENTS]
 * -
 *--------------------------------------------------------------*/
NXINVOICE.updateAdjustment = function () {

    NXINVOICE.log("[billing] updateAdjustment() - updating bill adjustment amount");

    //get adjustment description
    var bill_adjustment_description = $("#js_bill_adjustment_description").val();
    var bill_adjustment_amount = $("#js_bill_adjustment_amount").val();

    //check it its zero amount
    if (Number(bill_adjustment_amount) == 0) {
        NXINVOICE.removeAdjustment();
    }

    //update logic form
    $("#bill_adjustment_description").val(bill_adjustment_description);
    $("#bill_adjustment_amount").val(bill_adjustment_amount);

    //update displayed data
    $("#billing-adjustment-container-description").html(bill_adjustment_description);

    //better 'negative' amount formatting (accounting.formatMoney() returns $-9.99 instead of -$9.99)
    if (bill_adjustment_amount < 0) {
        bill_adjustment_amount = -bill_adjustment_amount;
        $("#billing-adjustment-container-amount").html('-' + accounting.formatMoney(bill_adjustment_amount));
    } else {
        $("#billing-adjustment-container-amount").html(accounting.formatMoney(bill_adjustment_amount));
    }

    //show adjustment line
    $("#billing-adjustment-container").show();

    //close discount popover
    $('#billing-adjustment-popover-button').popover('hide');

    //recalculate bill
    NXINVOICE.CALC.reclaculateBill(self);

}


/*----------------------------------------------------------------
 * [REMOVE ADJUSTMENTS]
 * -
 *--------------------------------------------------------------*/
NXINVOICE.removeAdjustment = function () {

    //log
    NXINVOICE.log("[billing] removeAdjustment() - updating bill adjustment amount to zero");


    //update logic form
    $("#bill_adjustment_description").val('');
    $("#bill_adjustment_amount").val(0);

    //update displayed data
    $("#billing-adjustment-container-description").html('');
    $("#billing-adjustment-container-amount").html(accounting.formatMoney(0));

    //hide the row
    $("#billing-adjustment-container").hide();

    //close discount popover
    $('#billing-adjustment-popover-button').popover('hide');

    //recalculate bill
    NXINVOICE.CALC.reclaculateBill(self);

}

/*----------------------------------------------------------------
 * [UPDATE DISCOUNT TYPE]
 * -set the selected discount type
 *--------------------------------------------------------------*/
NXINVOICE.updateDiscount = function () {

    NXINVOICE.log("[billing] updateDiscountType() - updating discount type");

    //type
    var discount_type = $("#js-billing-discount-type").val();


    //validation percentage
    if (discount_type == 'percentage') {
        NXINVOICE.log("[billing] updateDiscountType() - [percentage] ", Number($("#js_bill_discount_percentage").val()));
        if (Number($("#js_bill_discount_percentage").val()) > 100 || Number($("#bill_discount_type").val()) <= 0) {
            //error message
            NX.notification({
                type: 'error',
                message: NXLANG.invalid_discount
            });
            return;
        }
    }

    //validation percentage
    if (discount_type == 'fixed') {
        NXINVOICE.log("[billing] updateDiscountType() - [fixed] ", Number($("#js_bill_discount_amount").val()));
        if (Number($("#js_bill_discount_amount").val()) <= 0) {
            //error message
            NX.notification({
                type: 'error',
                message: NXLANG.invalid_discount
            });
            return;
        }
    }

    //fixed discount
    if (discount_type == 'fixed') {
        //update logif form
        $("#bill_discount_type").val('fixed');
        $("#dom-billing-discount-type").html('(' + NXLANG.fixed + ')');
        $("#bill_discount_percentage").val(0.00);
        $("#bill_discount_amount").val($("#js_bill_discount_amount").val());
    }

    //percentage discount
    if (discount_type == 'percentage') {
        $("#bill_discount_type").val('percentage');
        $("#dom-billing-discount-type").html('(' + $("#js_bill_discount_percentage").val() + '%)');
        $("#bill_discount_percentage").val($("#js_bill_discount_percentage").val());
        $("#bill_discount_amount").val(0.00);

    }

    //no discount
    if (discount_type == 'none') {
        $("#bill_discount_type").val('none');
        $("#bill_discount_percentage").val(0.00);
        $("#bill_discount_amount").val(0.00);
    }

    //close discount popover
    $('#billing-discounts-popover-button').popover('hide');

    //recalculate bill
    NXINVOICE.CALC.reclaculateBill(self);

}


/*----------------------------------------------------------------
 * [RECALCULATE INVOICE]
 * -calculate total tax rates (summary or line)
 *--------------------------------------------------------------*/
NXINVOICE.CALC.reclaculateBill = function (self) {

    NXINVOICE.log("[billing] reclaculateBill() - recalculating bill");

    //default tax rate
    var bill_tax_total_percentage = 0.00;

    //amount before an deductions
    var bill_subtotal = 0.00;

    //total discount amount
    var bill_discount_amount = 0.00;

    //total tax amount
    var bill_tax_total_amount = 0.00;

    //total before tax
    var bill_amount_before_tax = 0.00;

    //bill sum
    var bill_final_amount = 0.00;

    //adjustment
    var bill_adjustment_amount = $("#bill_adjustment_amount").val();

    //recalculate lines
    NXINVOICE.CALC.recalculateLines();



    //(1) ----------------------- SUM UP LINE ITEMS -------------------------------------
    NXINVOICE.log("[billing] reclaculateBill() - summing up all line items - started");
    $("#billing-items-container").find(".billing-line-item").each(function () {
        //each line
        var lineitem = $(this);

        //each line item total
        var linetotal = Number(lineitem.find(".js_item_total").val());

        //validate that its a number
        if (typeof linetotal == 'number') {
            NXINVOICE.log("[billing] reclaculateBill() - valid line item found [total]: (" + linetotal + ")");
            bill_subtotal += linetotal;
        }
    });

    //(2) ----------------- UPDATE SUBTOTAL (SUM BEFORE ADJUSTMENTS)--------------------------------
    NXINVOICE.log("[billing] reclaculateBill() - updating subtotal [subtotal]: (" + bill_subtotal + ") ");
    $("#billing-subtotal-figure").html(accounting.formatMoney(bill_subtotal));


    //(3) ----------------- DEDUCT ANY DISCOUNTS AND SET SUM BEFOR TAX--------------------
    var bill_discount_type = $("#bill_discount_type").val();
    var bill_discount_percentage = Number($("#bill_discount_percentage").val());
    var bill_discount_amount = Number($("#bill_discount_amount").val());
    NXINVOICE.log("[billing] reclaculateBill() - calculating discounts [bill_discount_type]: (" + bill_discount_type + ") [bill_discount_percentage]: (" + bill_discount_percentage + ") [bill_discount_amount] (" + bill_discount_amount + ")");


    //if bill is percentage based
    if (bill_discount_type == 'percentage') {
        //calculate
        var bill_discount_amount = (bill_subtotal * bill_discount_percentage) / 100;
        //log
        NXINVOICE.log("[billing] reclaculateBill() - discount is percentage based. [bill_discount_amount]: (" + bill_discount_amount + ")");
    }



    //if bill is fixed
    if (bill_discount_type == 'fixed') {
        //log
        NXINVOICE.log("[billing] reclaculateBill() - discount is fixed. [bill_discount_amount]: (" + bill_discount_amount + ")");
    }


    //do we have a discount
    if (bill_discount_amount > 0) {
        var bill_amount_before_tax = bill_subtotal - bill_discount_amount;
        //show subtotal
        $("#billing-table-section-subtotal").show();
        //set visibilty
        $("#billing-table-section-discounts").show();
        //discount amount
        $("#billing-sums-discount").html(accounting.formatMoney(bill_discount_amount));
        //log
        NXINVOICE.log("[billing] reclaculateBill() - there is a discount - setting DOM  and [bill_amount_before_tax]: (" + bill_amount_before_tax + ")");
        //set amount before tas
        $("#billing-sums-before-tax").html(accounting.formatMoney(bill_amount_before_tax));
    } else {
        //hide subtotal
        $("#billing-table-section-subtotal").hide();
        //hide discounts section
        $("#billing-table-section-discounts").hide();
        //set amunt before tax to be same as subtotal
        bill_amount_before_tax = bill_subtotal;
        //log
        NXINVOICE.log("[billing] reclaculateBill() - there is no discount - [bill_amount_before_tax]: (" + bill_amount_before_tax + ")");
    }




    //(1) ------------------------ SUMMARY TAX ------------------------------------------
    if ($("#bill_tax_type").val() == 'summary') {
        //log
        NXINVOICE.log("[billing] reclaculateBill() - calculating summary taxes");

        //tax row
        var tax_row = '';

        //sum up each selected tax rate. Then add a new row in the bill table
        $("#bill_logic_taxes").find(':selected').each(function () {
            var taxrate = Number($(this).val().split("|")[0]);
            var taxname = $(this).val().split("|")[1];
            var uniqueid = $(this).val().split("|")[2];

            //calculate each tax
            var tax_amount = (bill_amount_before_tax * taxrate) / 100;
            //create table row
            tax_row += '<tr class="billing-sums-tax-container" id="billing-sums-tax-container-' + uniqueid + '">' +
                '<td>' + taxname + ' <span class="x-small">(' + taxrate + '%)</span></td>' +
                '<td>' + accounting.formatMoney(tax_amount) + '</td></tr>';
            bill_tax_total_percentage += taxrate;
        });

        //do we have tax
        if (bill_tax_total_percentage > 0) {
            //log
            NXINVOICE.log("[billing] reclaculateBill() - this bill has [summary] based tax [tax_percentage]: (" + bill_tax_total_percentage + "%)");
            //tax calculation
            bill_tax_total_amount = (bill_amount_before_tax * bill_tax_total_percentage) / 100;
            //replace bill tax row in table
            $("#billing-table-section-tax").html(tax_row);
            //show subtotal
            $("#billing-table-section-subtotal").show();
            //show before tax section
            if (bill_discount_amount > 0) {
                $("#billing-sums-before-tax-container").show();
            }
            //show tax section
            $("#billing-table-section-tax").show();
            //log
            NXINVOICE.log("[billing] reclaculateBill() - the total tax on this bill is [total_tax_amount]: (" + bill_tax_total_amount + ")");
        } else {
            NXINVOICE.log("[billing] reclaculateBill() - this bill does not have any applicable tax");
            //hide before tax section
            $("#billing-sums-before-tax-container").hide();
            //hide tax section
            $("#billing-table-section-tax").hide();
        }
    }



    //update bills final sums
    bill_final_amount = bill_subtotal - bill_discount_amount + bill_tax_total_amount;
    NXINVOICE.log("[billing] reclaculateBill() - total amount before adjustment. [bill_final_amount]: (" + bill_final_amount + ")");

    //adjustment
    NXINVOICE.log("[billing] reclaculateBill() - accounting for the adjustment. [bill_adjustment_amount]: (" + bill_adjustment_amount + ")");

    bill_final_amount = bill_final_amount + Number(bill_adjustment_amount);

    //update final amount dom
    $("#billing-sums-total").html(accounting.formatMoney(bill_final_amount));

    //save values to logc form
    $("#bill_subtotal").val(bill_subtotal);
    $("#bill_discount_amount").val(nxFormatDecimal(bill_discount_amount));
    $("#bill_amount_before_tax").val(nxFormatDecimal(bill_amount_before_tax));
    $("#bill_tax_total_percentage").val(bill_tax_total_percentage);
    $("#bill_tax_total_amount").val(nxFormatDecimal(bill_tax_total_amount));
    $("#bill_final_amount").val(nxFormatDecimal(bill_final_amount));

    //update amount due label
    NXINVOICE.DOM.updateAmountDue(bill_final_amount);

    NXINVOICE.log("[billing] reclaculateBill() - bill claculation finished [final amount]: (" + bill_final_amount + ")");

    //set bills DOM state
    NXINVOICE.DOM.domState();

}


/*----------------------------------------------------------------
 * [SAVE INVOICE BUTTON]
 *--------------------------------------------------------------*/
NXINVOICE.CALC.saveBill = function (self) {

    NXINVOICE.log("[billing] saveBill() - started");

    if (NXINVOICE.CALC.validateLines()) {

        //recalculate bill
        NXINVOICE.CALC.reclaculateBill();

        //send to backend
        nxAjaxUxRequest(self);

    } else {
        //error message
        NX.notification({
            type: 'error',
            message: NXLANG.action_not_completed_errors_found
        });
    }

}



/*----------------------------------------------------------------
 * [SAVE INVOICE BUTTON]
 *--------------------------------------------------------------*/
NXINVOICE.CALC.validateLines = function (self) {

    //log
    NXINVOICE.log("[billing] validateLines() - started");


    var count_bill_error = 0;

    $("#billing-items-container").find(".billing-line-item").each(function () {

        NXINVOICE.log("[billing] recalculateLines() - found a line item. Now validating and calculating it");

        var lineitem = $(this);

        //each input fields
        var $description = lineitem.find(".js_item_description");
        var $quantity = lineitem.find(".js_item_quantity");
        var $hours = lineitem.find(".js_item_hours");
        var $minutes = lineitem.find(".js_item_minutes");
        var $unit = lineitem.find(".js_item_unit");
        var $rate = lineitem.find(".js_item_rate");
        var $type = lineitem.find(".js_item_type");


        //reset errors
        $description.removeClass('error');
        $quantity.removeClass('error');
        $unit.removeClass('error');
        $rate.removeClass('error');
        $hours.removeClass('error');
        $minutes.removeClass('error');

        //validate description
        if ($description.val() == '') {
            $description.addClass('error');
            count_bill_error++;
        }

        //validate plain item quantity
        if ($type.val() == 'plain') {
            if ($quantity.val() == '' || $quantity.val() <= 0) {
                $quantity.addClass('error');
                count_bill_error++;
            }
        }

        //validate time item quantity
        if ($type.val() == 'time') {
            if ($hours.val() == '' || $hours.val() == null) {
                //just set to zero
                $hours.val(0);
            }
            if ($minutes.val() == '' || $minutes.val() == null) {
                //if hours are also 0 then show error
                $minutes.val(0);
            }
        }

        //validate unit
        if ($unit.val() == '') {
            $unit.addClass('error');
            count_bill_error++;
        }

        //validate rate
        if ($rate.val() == '' || $rate.val() <= 0) {
            $rate.addClass('error');
            count_bill_error++;
        }
    });

    //log
    NXINVOICE.log("[billing] validateLines() - (" + count_bill_error + ") error found");


    //validate
    if (count_bill_error == 0) {
        return true;
    } else {
        return false;
    }

}


/*----------------------------------------------------------------
 * [revalidateItem] clear errors when item is changed
 *--------------------------------------------------------------*/
NXINVOICE.DOM.revalidateItem = function (self) {

    //validate description & unit
    if (self.hasClass('js_item_description') || self.hasClass('js_item_unit')) {
        if (self.hasClass('error') && self.val() != '') {
            self.removeClass('error')
        }
    }

    //validate rate & quantity
    if (self.hasClass('js_item_rate') || self.hasClass('js_item_quantity') || self.hasClass('js_item_hours') || self.hasClass('js_item_minutes')) {
        if (self.hasClass('error') && self.val() > 0) {
            self.removeClass('error')
        }
    }

}