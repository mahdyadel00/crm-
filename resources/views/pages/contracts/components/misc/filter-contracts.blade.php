<!-- right-sidebar -->
<div class="right-sidebar" id="sidepanel-filter-contracts">
    <form>
        <div class="slimscrollright">
            <!--title-->
            <div class="rpanel-title">
                <i class="icon-Filter-2"></i>@lang('lang.filter_contracts')
                <span>
                    <i class="ti-close js-close-side-panels" data-target="sidepanel-filter-contracts"></i>
                </span>
            </div>

            <!--body-->
            <div class="r-panel-body">


                <!--company name-->
                @if(config('visibility.filter_panel_client'))
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.client_name')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select name="filter_doc_client_id" id="filter_doc_client_id"
                                    class="clients_and_projects_toggle form-control form-control-sm js-select2-basic-search select2-hidden-accessible"
                                    data-projects-dropdown="filter_doc_project_id"
                                    data-feed-request-type="clients_projects"
                                    data-ajax--url="{{ url('/') }}/feed/company_names"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <!--project-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.project')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select class="select2-basic form-control form-control-sm dynamic_doc_project_id"
                                    id="filter_doc_project_id" name="filter_doc_project_id" disabled>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                @endif


                <!--categorgies-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.category')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select name="filter_contract_categoryid" id="filter_contract_categoryid"
                                    class="form-control form-control-sm select2-basic select2-multiple select2-hidden-accessible"
                                    multiple="multiple" tabindex="-1" aria-hidden="true">
                                    @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}">
                                        {{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>


                <!--contract_start_date-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.contract_start_date')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="filter_doc_date_start_start"
                                    class="form-control form-control-sm pickadate" autocomplete="off"
                                    placeholder="{{ cleanLang(__('lang.start')) }}">
                                <input class="mysql-date" type="hidden" id="filter_doc_date_start_start"
                                    name="filter_doc_date_start_start" value="">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="filter_doc_date_start_end"
                                    class="form-control form-control-sm pickadate" autocomplete="off"
                                    placeholder="{{ cleanLang(__('lang.end')) }}">
                                <input class="mysql-date" type="hidden" id="filter_doc_date_start_end"
                                    name="filter_doc_date_start_end" value="">
                            </div>
                        </div>
                    </div>
                </div>


                <!--valid_until-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.valid_until')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="filter_doc_date_end_start"
                                    class="form-control form-control-sm pickadate" autocomplete="off"
                                    placeholder="{{ cleanLang(__('lang.start')) }}">
                                <input class="mysql-date" type="hidden" id="filter_doc_date_end_start"
                                    name="filter_doc_date_end_start" value="">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="filter_doc_date_end_end"
                                    class="form-control form-control-sm pickadate" autocomplete="off"
                                    placeholder="{{ cleanLang(__('lang.end')) }}">
                                <input class="mysql-date" type="hidden" id="filter_doc_date_end_end"
                                    name="filter_doc_date_end_end" value="">
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
                        data-url="{{ urlResource('/contracts/search') }}" data-type="form"
                        data-ajax-type="GET">{{ cleanLang(__('lang.apply_filter')) }}</button>
                </div>
            </div>
            <!--body-->
        </div>
    </form>
</div>
<!--sidebar-->