<!--modal-->
<div class="modal create-modal" role="dialog" aria-labelledby="foo" id="createModal">
    <div class="modal-dialog">
        <form action="" method="post" id="selectorModalForm" class="form-horizontal">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="createModalCloseIcon">
                    <i class="ti-close"></i>
                </button>
                <div class="create-modal-body">


                    <!--LEFT SIDE-->
                    <div class="create-modal-section section-left ">
                        <div class="x-heading">
                            <h3 id="create-modal-splash-text">
                                <!-- dynamic title-->
                            </h3>
                        </div>
                        <div class="x-option">
                            <div class="form-group form-group-checkbox row">
                                <input name="group5" type="radio" id="client_type_existing"
                                    data-target-container="option-existing-client-container" checked
                                    class="with-gap radio-col-blue create-modal-selector">
                                <label for="client_type_existing"><span
                                        class="x-label-text">@lang('lang.existing_client')</span></label>
                            </div>
                        </div>
                        <div class="x-option">
                            <div class="form-group form-group-checkbox row">
                                <input name="group5" type="radio" id="client_type_new"
                                    data-target-container="option-new-client-container"
                                    class="with-gap radio-col-blue create-modal-selector">
                                <label for="client_type_new"><span
                                        class="x-label-text">@lang('lang.new_client')</span></label>
                            </div>
                        </div>
                    </div>

                    <!--RIGHT SIZE-->
                    <div class="create-modal-section section-right" id="create-modal-section-right">

                        <!--EXISTING CLIENT-->
                        <div id="option-existing-client-container" class="create-modal-option-contaiers hidden">

                            <div class="form-group row">
                                <label
                                    class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.client')) }}*</label>
                                <div class="col-sm-12 col-lg-9">
                                    <!--select2 basic search-->
                                    <select name="bill_clientid" id="bill_clientid"
                                        class="clients_and_projects_toggle form-control form-control-sm js-select2-basic-search-modal select2-hidden-accessible"
                                        data-projects-dropdown="bill_projectid"
                                        data-feed-request-type="clients_projects"
                                        data-ajax--url="{{ url('/') }}/feed/company_names">
                                    </select>
                                    <!--select2 basic search-->
                                    </select>
                                </div>
                            </div>

                            <!--projects-->
                            <div class="form-group row">
                                <label
                                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.project')) }}</label>
                                <div class="col-sm-12 col-lg-9">
                                    <select class="select2-basic form-control form-control-sm dynamic_bill_projectid"
                                        id="bill_projectid" name="bill_projectid" disabled>
                                    </select>
                                </div>
                            </div>

                            <!--continue button-->
                            <div class="text-right p-t-30">
                                <button type="submit" id="submitButton"
                                    class="btn btn-info waves-effect text-left js-ajax-request" data-url=""
                                    data-loading-target="create-modal-section-right"
                                    disabled>@lang('lang.continue')</button>
                            </div>
                        </div>


                        <!--NEW CLIENT-->
                        <div id="option-new-client-container" class="create-modal-option-contaiers">

                            <div class="form-group row">
                                <label
                                    class="col-sm-12 col-lg-4 text-left control-label col-form-label required">{{ cleanLang(__('lang.company_name')) }}*</label>
                                <div class="col-sm-12 col-lg-8">
                                    <input type="text" class="form-control form-control-sm" id="client_company_name"
                                        name="client_company_name" value="{{ $client->client_company_name ?? '' }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-sm-12 col-lg-4 text-left control-label col-form-label required">{{ cleanLang(__('lang.first_name')) }}*</label>
                                <div class="col-sm-12 col-lg-8">
                                    <input type="text" class="form-control form-control-sm" id="first_name"
                                        name="first_name" placeholder="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label
                                    class="col-sm-12 col-lg-4 text-left control-label col-form-label required">{{ cleanLang(__('lang.last_name')) }}*</label>
                                <div class="col-sm-12 col-lg-8">
                                    <input type="text" class="form-control form-control-sm" id="last_name"
                                        name="last_name" placeholder="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label
                                    class="col-sm-12 col-lg-4 text-left control-label col-form-label required">{{ cleanLang(__('lang.email_address')) }}*</label>
                                <div class="col-sm-12 col-lg-8">
                                    <input type="text" class="form-control form-control-sm" id="email" name="email"
                                        placeholder="">
                                </div>
                            </div>

                            <!--[dynamic button]-->
                            <span class="hidden edit-add-modal-button js-ajax-ux-request reset-target-modal-form" id="create-new-client-dynamic-button"
                                data-toggle="modal" data-target="#commonModal"
                                data-url="--dynamic--"
                                data-loading-target="commonModalBody" data-modal-title="--dynamic--"
                                data-action-url="--dynamic--" data-action-method="POST"
                                data-action-ajax-loading-target="commonModalBody"
                                data-loading-target="create-modal-section-right"></span>

                            <!--continue button-->
                            <div class="text-right p-t-30">
                                <button type="submit" id="create-new-client-button"
                                    class="btn btn-info waves-effect text-left js-ajax-request" data-url=""
                                    data-type="form" data-form-id="option-new-client-container" data-ajax-type="POST"
                                    data-loading-target="create-modal-section-right"
                                    data-on-start-submit-button="disable">@lang('lang.create_new_client')</button>
                            </div>
                        </div>

                    </div>


                    <div class="create-modal-section section-right hidden" id="create-modal-section-content">


                    </div>
                </div>
            </div>
        </form>
    </div>
</div>