<div class="row" id="js-trigger-invoices-modal-add-edit" data-payload="{{ $page['section'] ?? '' }}">
    <div class="col-lg-12">

        <!--meta data - creatd by-->
        @if(isset($page['section']) && $page['section'] == 'edit')
        <div class="modal-meta-data">
            <small><strong>{{ cleanLang(__('lang.created_by')) }}:</strong> {{ $invoice->first_name }}
                {{ $invoice->last_name}} |
                {{ runtimeDate($invoice->bill_created) }}</small>
        </div>
        @endif



        <!--client and project-->
        @if(config('visibility.invoice_modal_client_project_fields'))
        <!--client-->
        <div class="client-selector">

            <!--existing client-->
            <div class="client-selector-container" id="client-existing-container">
                <div class="form-group row">
                    <label
                        class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.client')) }}*</label>
                    <div class="col-sm-12 col-lg-9">
                        <!--select2 basic search-->
                        <select name="bill_clientid" id="bill_clientid"
                            class="clients_and_projects_toggle form-control form-control-sm js-select2-basic-search-modal select2-hidden-accessible"
                            data-projects-dropdown="bill_projectid" data-feed-request-type="clients_projects"
                            data-ajax--url="{{ url('/') }}/feed/company_names">
                            <!--regular invoices-->
                            @if(isset($invoice->bill_clientid) && $invoice->bill_clientid != '')
                            <option value="{{ $invoice->bill_clientid ?? '' }}">{{ $invoice->client_company_name }}
                            </option>
                            @endif
                            <!--creating invoice from an expense-->
                            @if(config('visibility.invoice_from_expense_client_name'))
                            <option value="{{ $expense->expense_clientid ?? '' }}">{{ $expense->client_company_name }}
                            </option>
                            @endif
                        </select>
                    </div>
                </div>
                <!--projects-->
                <div class="form-group row">
                    <label
                        class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.project')) }}</label>
                    <div class="col-sm-12 col-lg-9">
                        <select class="select2-basic form-control form-control-sm dynamic_bill_projectid" data-allow-clear="true"
                            id="bill_projectid" name="bill_projectid" disabled>
                        </select>
                    </div>
                </div>
            </div>

            <!--new client-->
            <div class="client-selector-container hidden" id="client-new-container">
                <div class="form-group row">
                    <label
                        class="col-sm-12 col-lg-4 text-left control-label col-form-label required">{{ cleanLang(__('lang.company_name')) }}*</label>
                    <div class="col-sm-12 col-lg-8">
                        <input type="text" class="form-control form-control-sm" id="client_company_name"
                            name="client_company_name">
                    </div>
                </div>

                <div class="form-group row">
                    <label
                        class="col-sm-12 col-lg-4 text-left control-label col-form-label required">{{ cleanLang(__('lang.first_name')) }}*</label>
                    <div class="col-sm-12 col-lg-8">
                        <input type="text" class="form-control form-control-sm" id="first_name" name="first_name"
                            placeholder="">
                    </div>
                </div>
                <div class="form-group row">
                    <label
                        class="col-sm-12 col-lg-4 text-left control-label col-form-label required">{{ cleanLang(__('lang.last_name')) }}*</label>
                    <div class="col-sm-12 col-lg-8">
                        <input type="text" class="form-control form-control-sm" id="last_name" name="last_name"
                            placeholder="">
                    </div>
                </div>
                <div class="form-group row">
                    <label
                        class="col-sm-12 col-lg-4 text-left control-label col-form-label required">{{ cleanLang(__('lang.email_address')) }}*</label>
                    <div class="col-sm-12 col-lg-8">
                        <input type="text" class="form-control form-control-sm" id="email" name="email" placeholder="">
                    </div>
                </div>
            </div>

            <!--option buttons-->
            <div class="client-selector-links">
                <a href="javascript:void(0)" class="client-type-selector" data-type="new"
                    data-target-container="client-new-container">@lang('lang.new_client')</a> |
                <a href="javascript:void(0)" class="client-type-selector active" data-type="existing"
                    data-target-container="client-existing-container">@lang('lang.existing_client')</a>
            </div>

            <!--client type indicator-->
            <input type="hidden" name="client-selection-type" id="client-selection-type" value="existing">
        </div>

        @endif



        <!--invoice date-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.invoice_date')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control  form-control-sm pickadate" name="bill_date_add_edit"
                    autocomplete="off" value="{{ runtimeDatepickerDate($invoice->bill_date ?? '') }}">
                <input class="mysql-date" type="hidden" name="bill_date" id="bill_date_add_edit"
                    value="{{ $invoice->bill_date ?? '' }}">
            </div>
        </div>

        <!--due date-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.due_date')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm pickadate" name="bill_due_date_add_edit"
                    autocomplete="off" value="{{ runtimeDatepickerDate($invoice->bill_due_date ?? '') }}">
                <input class="mysql-date" type="hidden" name="bill_due_date" id="bill_due_date_add_edit"
                    value="{{ $invoice->bill_due_date ?? '' }}">
            </div>
        </div>



        <!--clients projects-->
        @if(config('visibility.invoice_modal_clients_projects'))
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.project')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="bill_projectid" name="bill_projectid">
                    @foreach(config('settings.clients_projects') as $project)
                    <option value="{{ $project->project_id ?? '' }}">{{ $project->project_title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        <!--invoice category-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.category')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="bill_categoryid" name="bill_categoryid">
                    @foreach($categories as $category)
                    <option value="{{ $category->category_id }}"
                        {{ runtimePreselected($invoice->bill_categoryid ?? '', $category->category_id) }}>{{
                                runtimeLang($category->category_name) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="line"></div>


        <!--otions toggle-->
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.additional_information')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" class="js-switch-toggle-hidden-content"
                            data-target="edit_bill_recurring_toggle">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="hidden" id="edit_bill_recurring_toggle">
            <!--tags-->
            <div class="form-group row">
                <label class="col-12 text-left control-label col-form-label">{{ cleanLang(__('lang.tags')) }}</label>
                <div class="col-12">
                    <select name="tags" id="tags"
                        class="form-control form-control-sm select2-multiple {{ runtimeAllowUserTags() }} select2-hidden-accessible"
                        multiple="multiple" tabindex="-1" aria-hidden="true">
                        <!--array of selected tags-->
                        @if(isset($page['section']) && $page['section'] == 'edit')
                        @foreach($invoice->tags as $tag)
                        @php $selected_tags[] = $tag->tag_title ; @endphp
                        @endforeach
                        @endif
                        <!--/#array of selected tags-->
                        @foreach($tags as $tag)
                        <option value="{{ $tag->tag_title }}"
                            {{ runtimePreselectedInArray($tag->tag_title ?? '', $selected_tags ?? []) }}>
                            {{ $tag->tag_title }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- notes-->
            <div class="form-group row">
                <label class="col-12 text-left control-label col-form-label">{{ cleanLang(__('lang.notes')) }}</label>
                <div class="col-12">
                    <textarea id="bill_notes" name="bill_notes"
                        class="tinymce-textarea">{{ $invoice->bill_notes ?? '' }}</textarea>
                </div>
            </div>


            <!-- terms-->
            <div class="form-group row">
                <label
                    class="col-12 text-left control-label col-form-label">{{ cleanLang(__('lang.terms_and_conditions')) }}</label>
                <div class="col-12">
                    <textarea id="bill_terms" name="bill_terms" class="tinymce-textarea">
                        @if(isset($page['section']) && $page['section'] == 'create')
                        {{ config('system.settings_invoices_default_terms_conditions') }}
                        @else
                        {{ $invoice->bill_terms ?? '' }}
                        @endif                 
                </textarea>
                </div>
            </div>
        </div>
        <!--/#options toggle-->



        <!--source-->
        <input type="hidden" name="source" value="{{ request('source') }}">

        <!--expenses payload-->
        @if(config('visibility.invoice_modal_expenses_payload'))
        <input type="hidden" name="expense_payload[]" value="{{ config('settings.expense_id') }}">
        @endif

        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>

        <!--recurring notes-->
        <div class="alert alert-info m-t-10"><i class="sl-icon-refresh text-warning"></i>
            {{ cleanLang(__('lang.recurring_invoice_options_info')) }}</div>
    </div>
</div>