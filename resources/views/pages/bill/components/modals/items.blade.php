<!--modal-->
<div class="modal" role="dialog" aria-labelledby="itemsModal" id="itemsModal" {!! runtimeAllowCloseModalOptions()
    !!}>
    <div class="modal-dialog modal-xl" id="itemsModalContainer">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="itemsModalTitle">{{ cleanLang(__('lang.invoice_products')) }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body p-t-10 p-b-0" id="itemsModalBody">
                <!--search box-->
                <div class="clearfix">
                    <div class="header-search pull-right"  id="header-search-items">
                        <i class="sl-icon-magnifier"></i>
                            <input type="text" class="form-control search-records list-actions-search"
                                data-url="{{ url('items/search?action=search&itemresource_type=invoice') }}"
                                data-type="form" data-ajax-type="post" data-form-id="header-search-items" id="search_query" name="search_query" placeholder="{{ cleanLang(__('lang.search')) }}">
                    </div>
                </div>
                <div id="items-table-wrapper">
                    <!--dynamic content here-->
                </div>
            </div>

            <div class="modal-footer p-t-0 p-b-20 invoice-billing-footer" id="itemsModalFooter">
                <button type="submit" id="itemsModalSelectButton"
                    class="btn btn-rounded-x btn-danger waves-effect text-left hidden" data-url="" data-loading-target=""
                    data-ajax-type="POST" data-on-start-submit-button="disable">{{ cleanLang(__('lang.add_selected_items')) }}</button>
            </div>
        </div>
    </div>
</div>