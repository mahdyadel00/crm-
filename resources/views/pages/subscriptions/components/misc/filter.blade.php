<!-- right-sidebar -->
<div class="right-sidebar" id="sidepanel-filter-subscriptions">
    <form>
        <div class="slimscrollright">
            <!--title-->
            <div class="rpanel-title">
                <i class="icon-Filter-2"></i>{{ cleanLang(__('lang.filter_subscriptions')) }}
                <span>
                    <i class="ti-close js-close-side-panels" data-target="sidepanel-filter-subscriptions"></i>
                </span>
            </div>
            <!--title-->
            <!--body-->
            <div class="r-panel-body">

                @if(config('visibility.filter_panel_client_project'))
                <!--company name-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.client_name')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select name="filter_subscription_clientid" id="filter_subscription_clientid"
                                    class="clients_and_projects_toggle form-control form-control-sm js-select2-basic-search select2-hidden-accessible"
                                    data-projects-dropdown="filter_subscription_projectid"
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
                                <select
                                    class="select2-basic form-control form-control-sm dynamic_filter_subscription_projectid"
                                    id="filter_subscription_projectid" name="filter_subscription_projectid" disabled>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!--status-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.status')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select name="filter_subscription_status" id="filter_subscription_status"
                                    class="form-control form-control-sm select2-basic select2-multiple select2-hidden-accessible"
                                    multiple="multiple" tabindex="-1" aria-hidden="true" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="pending">@lang('lang.pending')</option>
                                    <option value="active">@lang('lang.active')</option>
                                    <option value="failed">@lang('lang.failed')</option>
                                    <option value="cancelled">@lang('lang.cancelled')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!--filter item-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.amount')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-6 input-group input-group-sm">
                                <span
                                    class="input-group-addon">{{ config('system.settings_system_currency_symbol') }}</span>
                                <input type="number" name="filter_subscription_final_amount_min"
                                    id="filter_subscription_final_amount_min" class="form-control form-control-sm"
                                    placeholder="{{ cleanLang(__('lang.min')) }}">
                            </div>
                            <div class="col-md-6 input-group input-group-sm">
                                <span
                                    class="input-group-addon">{{ config('system.settings_system_currency_symbol') }}</span>
                                <input type="number" name="filter_subscription_final_amount_max"
                                    id="filter_subscription_final_amount_max" class="form-control form-control-sm"
                                    placeholder="{{ cleanLang(__('lang.max')) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <!--filter item-->

                <!--filter item-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.payments')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-6 input-group input-group-sm">
                                <span
                                    class="input-group-addon">{{ config('system.settings_system_currency_symbol') }}</span>
                                <input type="number" name="filter_subscription_payments_min"
                                    id="filter_subscription_payments_min" class="form-control form-control-sm"
                                    placeholder="{{ cleanLang(__('lang.minimum')) }}">
                            </div>
                            <div class="col-md-6 input-group input-group-sm">
                                <span
                                    class="input-group-addon">{{ config('system.settings_system_currency_symbol') }}</span>
                                <input type="number" name="filter_subscription_payments_max"
                                    id="filter_subscription_payments_max" class="form-control form-control-sm"
                                    placeholder="{{ cleanLang(__('lang.maximum')) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <!--filter item-->

                <!--filter item-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.date_created')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="filter_subscription_date_started_start"
                                    class="form-control form-control-sm pickadate" autocomplete="off"
                                    placeholder="Start">
                                <input class="mysql-date" type="hidden" name="filter_subscription_date_started_start"
                                    id="filter_subscription_date_started_start" value="">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="filter_subscription_date_started_end"
                                    class="form-control form-control-sm pickadate" autocomplete="off" placeholder="End">
                                <input class="mysql-date" type="hidden" name="filter_subscription_date_started_end"
                                    id="filter_subscription_date_started_end" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <!--filter item-->


                <!--buttons-->
                <div class="buttons-block">
                    <button type="button" name="foo1"
                        class="btn btn-rounded-x btn-secondary js-reset-filter-side-panel">{{ cleanLang(__('lang.reset')) }}</button>
                    <input type="hidden" name="action" value="search">
                    <input type="hidden" name="source" value="{{ $page['source_for_filter_panels'] ?? '' }}">
                    <button type="button" class="btn btn-rounded-x btn-danger js-ajax-ux-request apply-filter-button"
                        data-url="{{ urlResource('/subscriptions/search?') }}" data-type="form"
                        data-ajax-type="GET">{{ cleanLang(__('lang.apply_filter')) }}</button>
                </div>
            </div>
            <!--body-->
        </div>
    </form>
</div>
<!--sidebar-->