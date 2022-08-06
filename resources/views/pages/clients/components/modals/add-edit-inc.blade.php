<!--modal-->
<div class="row" id="js-trigger-clients-modal-add-edit" data-payload="{{ $page['section'] ?? '' }}">
    <div class="col-lg-12">

        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.company_name')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="client_company_name"
                    name="client_company_name" value="{{ $client->client_company_name ?? '' }}">
            </div>
        </div>



        @if(isset($page['section']) && $page['section'] == 'edit' && auth()->user()->is_team)

        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.category')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="client_categoryid"
                    name="client_categoryid">
                    @foreach($categories as $category)
                    <option value="{{ $category->category_id }}"
                        {{ runtimePreselected($client->client_categoryid ?? '', $category->category_id) }}>{{
                                            runtimeLang($category->category_name) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="example-month-input"
                class="col-sm-12 col-lg-3 col-form-label text-left">{{ cleanLang(__('lang.status')) }}</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="client_status" name="client_status">
                    <option></option>
                    <option value="active" {{ runtimePreselected($client->client_status ?? '', 'active') }}>
                        {{ cleanLang(__('lang.active')) }}</option>
                    <option value="suspended" {{ runtimePreselected($client->client_status ?? '', 'suspended') }}>
                        {{ cleanLang(__('lang.suspended')) }}
                    </option>
                </select>
            </div>
        </div>

        <div class="line"></div>
        @endif

        <!--contact section-->
        @if(isset($page['section']) && $page['section'] == 'create')
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.first_name')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="first_name" name="first_name"
                    placeholder="">
            </div>
        </div>
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.last_name')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="last_name" name="last_name" placeholder="">
            </div>
        </div>
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.email_address')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="email" name="email" placeholder="">
            </div>
        </div>

        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.category')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="client_categoryid"
                    name="client_categoryid">
                    @foreach($categories as $category)
                    <option value="{{ $category->category_id }}"
                        {{ runtimePreselected($client->client_categoryid ?? '', $category->category_id) }}>{{
                                            runtimeLang($category->category_name) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="line"></div>
        @endif
        <!--contact section-->


        <!--CUSTOMER FIELDS [expanded]-->
        @if(auth()->user()->is_team && config('system.settings_customfields_display_clients') == 'expanded')
        @include('misc.customfields')
        @endif
        <!--/#CUSTOMER FIELDS [expanded]-->

        <!--DESCRIPTION & DETAILS-->
        @if(auth()->user()->is_team)
        <div class="spacer row">
            <div class="col-sm-8">
                <span class="title">{{ cleanLang(__('lang.description_and_details')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" class="js-switch-toggle-hidden-content"
                            data-target="edit_client_description_toggle">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="hidden" id="edit_client_description_toggle">

            <textarea id="client_description" name="client_description"
                class="tinymce-textarea">{{ $client->client_description ?? '' }}</textarea>


            <!--tags-->
            <div class="form-group row m-t-20">
                <label class="col-12 text-left control-label col-form-label">{{ cleanLang(__('lang.tags')) }}</label>
                <div class="col-12">
                    <select name="tags" id="tags"
                        class="form-control form-control-sm select2-multiple select2-tags select2-hidden-accessible"
                        multiple="multiple" tabindex="-1" aria-hidden="true">

                        <!--array of selected tags-->
                        @if(isset($page['section']) && $page['section'] == 'edit')
                        @foreach($client->tags as $tag)
                        @php $selected_tags[] = $tag->tag_title ; @endphp
                        @endforeach
                        @endif
                        <!--/#array of selected tags-->

                        @foreach($tags as $tag)
                        <option value="{{ $tag->tag_title }}"
                            {{ runtimePreselectedInArray($tag->tag_title ?? '', $selected_tags  ?? []) }}>
                            {{ $tag->tag_title }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!--/#tags-->

            <div class="line m-t-30"></div>

        </div>
        @endif
        <!--/#DESCRIPTION & DETAILS-->


        <!--billing address section-->
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.billing_address')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="add_client_option_bill_address" id="add_client_option_bill_address"
                            class="js-switch-toggle-hidden-content" data-target="add_client_billing_address_section">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <!--billing address section-->


        <!--billing address section-->
        <div id="add_client_billing_address_section" class="hidden">
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.street')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="client_billing_street"
                        name="client_billing_street" value="{{ $client->client_billing_street ?? '' }}">
                </div>
            </div>
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.city')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="client_billing_city"
                        name="client_billing_city" value="{{ $client->client_billing_city ?? '' }}">
                </div>
            </div>
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.state')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="client_billing_state"
                        name="client_billing_state" value="{{ $client->client_billing_state ?? '' }}">
                </div>
            </div>
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.zipcode')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="client_billing_zip"
                        name="client_billing_zip" value="{{ $client->client_billing_zip ?? '' }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="example-month-input"
                    class="col-sm-12 col-lg-3 col-form-label text-left">{{ cleanLang(__('lang.country')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    @php $selected_country = $client->client_billing_country ?? ''; @endphp
                    <select class="select2-basic form-control form-control-sm" id="client_billing_country"
                        name="client_billing_country">
                        <option></option>
                        @include('misc.country-list')
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.telephone')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="client_phone" name="client_phone"
                        value="{{ $client->client_phone ?? '' }}">
                </div>
            </div>
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.website')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="client_website" name="client_website"
                        value="{{ $client->client_website ?? '' }}">
                </div>
            </div>
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.vat_tax_number')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="client_vat" name="client_vat"
                        value="{{ $client->client_vat ?? '' }}">
                </div>
            </div>
            <div class="line"></div>
        </div>
        <!--billing address section-->


        <!--shipping address section-->
        @if(config('system.settings_clients_shipping_address') == 'enabled')
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.shipping_address')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="add_client_option_shipping_address"
                            id="add_client_option_shipping_address" class="js-switch-toggle-hidden-content"
                            data-target="add_client_shipping_address_section">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        @endif
        <!--shipping address section-->


        <!--shipping address section-->
        @if(config('system.settings_clients_shipping_address') == 'enabled')
        <div id="add_client_shipping_address_section" class="hidden">
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.street')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="client_shipping_street"
                        name="client_shipping_street" value="{{ $client->client_shipping_street ?? '' }}">
                </div>
            </div>
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.city')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="client_shipping_city"
                        name="client_shipping_city" value="{{ $client->client_shipping_city ?? '' }}">
                </div>
            </div>
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.state')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="client_shipping_state"
                        name="client_shipping_state" value="{{ $client->client_shipping_state ?? '' }}">
                </div>
            </div>
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.zipcode')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="client_shipping_zip"
                        name="client_shipping_zip" value="{{ $client->client_shipping_zip ?? '' }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="example-month-input"
                    class="col-sm-12 col-lg-3 col-form-label text-left">{{ cleanLang(__('lang.country')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    @php $selected_country = $client->client_shipping_country ?? ''; @endphp
                    <select class="select2-basic form-control form-control-sm" id="client_shipping_country"
                        name="client_shipping_country">
                        <option></option>
                        @include('misc.country-list')
                    </select>
                </div>
            </div>
            <div class="form-group form-group-checkbox row" id="expense_billable_option">
                <label
                    class="col-sm-12 col-lg-3 col-form-label text-left">{{ cleanLang(__('lang.same_as_billing')) }}</label>
                <div class="col-6 text-left p-t-5">
                    <input type="checkbox" id="same_as_billing_address" name="same_as_billing_address"
                        class="filled-in chk-col-light-blue">
                    <label for="same_as_billing_address"></label>
                </div>
            </div>
        </div>
        @endif
        <!--shipping address section-->


        <!--APP MODULES-->
        @if(auth()->user()->is_team)
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.app_modules')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="add_client_option_other" id="add_client_option_other"
                            class="js-switch-toggle-hidden-content" data-target="client_app_modules_collaped">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <div id="client_app_modules_collaped" class="hidden">

            <!--(select2-preselected) &  (data-preselected) are optional-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">@lang('lang.enabled_modules')</label>
                <div class="col-sm-12 col-lg-9">
                    <select class="select2-basic form-control form-control-sm select2-preselected"
                        id="client_app_modules" name="client_app_modules"
                        data-preselected="{{ $client->client_app_modules ?? 'system' }}">
                        <option value="system">@lang('lang.use_system_settings')</option>
                        <option value="custom">@lang('lang.use_custom_settings')</option>
                    </select>
                </div>
            </div>


            <!--custom client modules settings-->
            <div id="client_app_modules_pemissions"
                class="{{ runtimeVisibility('client_app_modules_pemissions', $client->client_app_modules ?? 'system') }}">

                <div class="highlighted-panel">
                    <!--preselect when in create mode-->
                    @php $creation_prechecked = ($page['section'] == 'create') ? 'checked' : ''; @endphp

                    <!--_projects-->
                    @if(config('system.settings_modules_projects') == 'enabled')
                    <div class="form-group form-group-checkbox row">
                        <label class="col-sm-12 col-lg-10 col-form-label text-left">@lang('lang.projects')</label>
                        <div class="col-sm-12 col-lg-2 text-left p-t-5">
                            <input type="checkbox" id="client_settings_modules_projects"
                                name="client_settings_modules_projects"
                                {{ runtimePrechecked($client->client_settings_modules_projects ?? '') }} {{  $creation_prechecked }} class="filled-in chk-col-light-blue">
                            <label for="client_settings_modules_projects"></label>
                        </div>
                    </div>
                    @endif


                    <!--invoices-->
                    @if(config('system.settings_modules_invoices') == 'enabled')
                    <div class="form-group form-group-checkbox row">
                        <label class="col-sm-12 col-lg-10 col-form-label text-left">@lang('lang.invoices')</label>
                        <div class="col-sm-12 col-lg-2 text-left p-t-5">
                            <input type="checkbox" id="client_settings_modules_invoices"
                                name="client_settings_modules_invoices"
                                {{ runtimePrechecked($client->client_settings_modules_invoices ?? '') }} {{  $creation_prechecked }}  class="filled-in chk-col-light-blue">
                            <label for="client_settings_modules_invoices"></label>
                        </div>
                    </div>
                    @endif


                    <!--payments-->
                    @if(config('system.settings_modules_payments') == 'enabled')
                    <div class="form-group form-group-checkbox row">
                        <label class="col-sm-12 col-lg-10 col-form-label text-left">@lang('lang.payments')</label>
                        <div class="col-sm-12 col-lg-2 text-left p-t-5">
                            <input type="checkbox" id="client_settings_modules_payments"
                                name="client_settings_modules_payments"
                                {{ runtimePrechecked($client->client_settings_modules_payments ?? '') }} {{  $creation_prechecked }}  class="filled-in chk-col-light-blue">
                            <label for="client_settings_modules_payments"></label>
                        </div>
                    </div>
                    @endif


                    <!--knowledgebase-->
                    @if(config('system.settings_modules_knowledgebase') == 'enabled')
                    <div class="form-group form-group-checkbox row">
                        <label class="col-sm-12 col-lg-10 col-form-label text-left">@lang('lang.knowledgebase')</label>
                        <div class="col-sm-12 col-lg-2 text-left p-t-5">
                            <input type="checkbox" id="client_settings_modules_knowledgebase"
                                name="client_settings_modules_knowledgebase"
                                {{ runtimePrechecked($client->client_settings_modules_knowledgebase ?? '') }} {{  $creation_prechecked }}  class="filled-in chk-col-light-blue">
                            <label for="client_settings_modules_knowledgebase"></label>
                        </div>
                    </div>
                    @endif


                    <!--estimates-->
                    @if(config('system.settings_modules_estimates') == 'enabled')
                    <div class="form-group form-group-checkbox row">
                        <label class="col-sm-12 col-lg-10 col-form-label text-left">@lang('lang.estimates')</label>
                        <div class="col-sm-12 col-lg-2 text-left p-t-5">
                            <input type="checkbox" id="client_settings_modules_estimates"
                                name="client_settings_modules_estimates"
                                {{ runtimePrechecked($client->client_settings_modules_estimates ?? '') }} {{  $creation_prechecked }}  class="filled-in chk-col-light-blue">
                            <label for="client_settings_modules_estimates"></label>
                        </div>
                    </div>
                    @endif


                    <!--subscriptions-->
                    @if(config('system.settings_modules_subscriptions') == 'enabled')
                    <div class="form-group form-group-checkbox row">
                        <label class="col-sm-12 col-lg-10 col-form-label text-left">@lang('lang.subscriptions')</label>
                        <div class="col-sm-12 col-lg-2 text-left p-t-5">
                            <input type="checkbox" id="client_settings_modules_subscriptions"
                                name="client_settings_modules_subscriptions"
                                {{ runtimePrechecked($client->client_settings_modules_subscriptions ?? '') }} {{  $creation_prechecked }}  class="filled-in chk-col-light-blue">
                            <label for="client_settings_modules_subscriptions"></label>
                        </div>
                    </div>
                    @endif


                    <!--tickets-->
                    @if(config('system.settings_modules_tickets') == 'enabled')
                    <div class="form-group form-group-checkbox row">
                        <label class="col-sm-12 col-lg-10 col-form-label text-left">@lang('lang.tickets')</label>
                        <div class="col-sm-12 col-lg-2 text-left p-t-5">
                            <input type="checkbox" id="client_settings_modules_tickets"
                                name="client_settings_modules_tickets"
                                {{ runtimePrechecked($client->client_settings_modules_tickets ?? '') }} {{  $creation_prechecked }}  class="filled-in chk-col-light-blue">
                            <label for="client_settings_modules_tickets"></label>
                        </div>
                    </div>
                    @endif

                    <div class="alert alert-info">@lang('lang.only_system_enabled_modules_enabled') <a href="{{ url('app/settings/modules') }}" target="_blank">(@lang('lang.see_settings'))</a></div>
                </div>

            </div>

        </div>
        @endif


        <!--CUSTOMER FIELDS [collapsed]-->
        @if(auth()->user()->is_team && config('system.settings_customfields_display_clients') == 'toggled')
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.more_information')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="add_client_option_other" id="add_client_option_other"
                            class="js-switch-toggle-hidden-content" data-target="client_custom_fields_collaped">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <div id="client_custom_fields_collaped" class="hidden">

            @if(config('app.application_demo_mode'))
            <!--DEMO INFO-->
            <div class="alert alert-info">
                <h5 class="text-info"><i class="sl-icon-info"></i> Demo Info</h5> 
                These are custom fields. You can change them or <a href="{{ url('app/settings/customfields/projects') }}">create your own.</a>
            </div>
            @endif
            
            @include('misc.customfields')
        </div>
        @endif
        <!--/#CUSTOMER FIELDS [collapsed]-->


        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>
    </div>
</div>