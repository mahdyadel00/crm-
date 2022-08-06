<!--modal-->
<div class="modal" role="dialog" aria-labelledby="timebillingModal" id="timebillingModal" {!! clean(runtimeAllowCloseModalOptions()) !!}>
    <div class="modal-dialog modal-xl" id="timebillingModalContainer">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="timebillingModalTitle">{{ cleanLang(__('lang.billable_hours')) }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body p-t-10 p-b-0" id="timebillingModalBody">
                <div id="timebilling-table-wrapper">
                    <!--dynamic content here-->
                </div>
            </div>

            <div class="modal-footer p-t-0 p-b-20 invoice-billing-footer" id="timebillingModalFooter">
                <button type="submit" id="timebillingModalSelectButton"
                    class="btn btn-rounded-x btn-danger waves-effect text-left hidden" data-url="" data-loading-target=""
                    data-ajax-type="POST" data-on-start-submit-button="disable">{{ cleanLang(__('lang.add_selected_items')) }}</button>
            </div>
        </div>
    </div>
</div>