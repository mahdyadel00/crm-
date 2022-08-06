<!--modal-->
<div class="modal" role="dialog" aria-labelledby="foo" id="commonModal" {!! clean(runtimeAllowCloseModalOptions()) !!}>
    <div class="modal-dialog" id="commonModalContainer">
        <form action="" method="post" id="commonModalForm" class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header" id="commonModalHeader">
                    <h4 class="modal-title" id="commonModalTitle"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"
                        id="commonModalCloseIcon">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <!--optional button for when header is hidden-->
                <span class="close x-extra-close-icon" data-dismiss="modal" aria-hidden="true"
                    id="commonModalExtraCloseIcon">
                    <i class="ti-close"></i>
                </span>
                <div class="modal-body min-h-200" id="commonModalBody">
                    <!--dynamic content here-->
                </div>
                <div class="modal-footer" id="commonModalFooter">
                    <button type="button" id="commonModalCloseButton" class="btn btn-rounded-x btn-secondary waves-effect text-left" data-dismiss="modal">{{ cleanLang(__('lang.close')) }}</button>
                    <button type="submit" id="commonModalSubmitButton"
                        class="btn btn-rounded-x btn-danger waves-effect text-left" data-url="" data-loading-target=""
                        data-ajax-type="POST" data-on-start-submit-button="disable">{{ cleanLang(__('lang.submit')) }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!--notes: see events.js for deails-->