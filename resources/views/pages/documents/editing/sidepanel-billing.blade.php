<!-- right-sidebar -->
<div class="right-sidebar documents-side-panel-billing sidebar-xl" id="documents-side-panel-billing">
    <form>
        <div class="slimscrollright">
            <!--title-->
            <div class="rpanel-title">
                <!--add class'due'to title panel -->
                <i class="ti-alarm-clock display-inline-block m-t--5"></i>
                <div class="display-inline-block">
                    @lang('lang.edit_billing')
                </div>
                <span>
                    <i class="ti-close js-close-side-panels" data-target="documents-side-panel-billing"
                        id="documents-side-panel-billing-close-icon"></i>
                </span>
            </div>
            <!--title-->
            <!--body-->
            <div class="r-panel-body documents-side-panel-billing-body  p-b-80">

                <div id="documents-side-panel-billing-content">
                    <!--dynamic content-->
                </div>

                <div class="alert alert-info hidden" id="documents-side-panel-billing-info">
                    <h5 class="text-info"><i class="sl-icon-info"></i> @lang('lang.info')</h5>
                    @lang('lang.documents_billing_info')
                    <div class="line"></div>
                    {pricing_table}
                </div>

            </div>
            <!--body-->
        </div>
    </form>
</div>
<!--sidebar-->