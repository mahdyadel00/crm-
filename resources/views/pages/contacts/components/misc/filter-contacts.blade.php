<!-- right-sidebar -->
<div class="right-sidebar" id="sidepanel-filter-contacts">
    <form>
        <div class="slimscrollright">
            <!--title-->
            <div class="rpanel-title">
                <i class="icon-Filter-2"></i>{{ cleanLang(__('lang.filter_contacts')) }}
                <span>
                    <i class="ti-close js-close-side-panels" data-target="sidepanel-filter-contacts"></i>
                </span>
            </div>
            <!--title-->
            <!--body-->
            <div class="r-panel-body">

                <!--company name-->
                @if(config('visibility.filter_panel_client'))
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.company_name')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <!--select2 basic search-->
                                <select name="filter_clientid" id="filter_clientid"
                                    class="form-control form-control-sm js-select2-basic-search select2-multiple select2-hidden-accessible"
                                    multiple="multiple" tabindex="-1" aria-hidden="true"
                                    data-ajax--url="{{ url('/') }}/feed/company_names"></select>
                                <!--select2 basic search-->
                            </div>
                        </div>
                    </div>
                </div>
                @endif



                <!--name-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.name')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <!--select2 basic search-->
                                <select name="filter_name" id="filter_name"
                                    class="form-control form-control-sm js-select2-basic-search select2-multiple select2-hidden-accessible"
                                    multiple="multiple" tabindex="-1" aria-hidden="true"
                                    data-ajax--url="{{ url('/') }}/feed/contacts?type=client"></select>
                                <!--select2 basic search-->
                            </div>
                        </div>
                    </div>
                </div>


                <!--email address-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.email_address')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <!--select2 basic search-->
                                <select name="filter_email" id="filter_email"
                                    class="form-control form-control-sm js-select2-basic-search select2-multiple select2-hidden-accessible"
                                    multiple="multiple" tabindex="-1" aria-hidden="true"
                                    data-ajax--url="{{ url('/') }}/feed/email?type=client"></select>
                                <!--select2 basic search-->
                            </div>
                        </div>
                    </div>
                </div>


                <!--type-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.user_type')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select name="filter_account_owner" id="filter_account_owner"
                                    class="form-control form-control-sm select2-basic select2-multiple"
                                    multiple="multiple" tabindex="-1" aria-hidden="true">
                                    <option value="yes">{{ cleanLang(__('lang.account_owner')) }}</option>
                                    <option value="no">{{ cleanLang(__('lang.user')) }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!--date added-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.date_added')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="filter_date_created_start" autocomplete="off"
                                     class="form-control form-control-sm pickadate"
                                    placeholder="Start">
                                <input class="mysql-date" type="hidden" name="filter_date_created_start" id="filter_date_created_start" value="">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="filter_date_created_end" 
                                    class="form-control form-control-sm pickadate" autocomplete="off" placeholder="End">
                                <input class="mysql-date" type="hidden" name="filter_date_created_end" id="filter_date_created_end" value="">
                            </div>
                        </div>
                    </div>
                </div>


                <!--buttons-->
                <div class="buttons-block">
                    <button type="button" name="foo1"
                        class="btn btn-rounded-x btn-secondary js-reset-filter-side-panel">{{ cleanLang(__('lang.reset')) }}</button>
                    <input type="hidden" name="action" value="search">
                    <input type="hidden" name="source" value="{{ $page['source_for_filter_panels'] ?? '' }}">
                    <button type="button" class="btn btn-rounded-x btn-danger js-ajax-ux-request apply-filter-button"
                        data-url="{{ urlResource('/contacts/search') }}" data-type="form" data-ajax-type="GET">{{ cleanLang(__('lang.apply_filter')) }}</button>
                </div>


            </div>
            <!--body-->
        </div>
    </form>
</div>
<!--sidebar-->