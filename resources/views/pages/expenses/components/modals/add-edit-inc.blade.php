<div class="row" id="js-trigger-expenses" data-client-id="{{ $expense->expense_clientid ?? '' }}"
    data-payload="{{ config('visibility.expense_modal_trigger_clients_project_list') }}">
    <div class="col-lg-12">


        <!--description-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.description')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <textarea class="w-100" id="expense_description" rows="4"
                    name="expense_description">{{ $expense->expense_description ?? '' }}</textarea>
            </div>
        </div>

        <!--date-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.date')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm pickadate" autocomplete="off" name="expense_date"
                    value="{{ runtimeDatepickerDate($expense->expense_date ?? '') }}">
                <input class="mysql-date" type="hidden" name="expense_date" id="expense_date"
                    value="{{ $expense->expense_date ?? '' }}">
            </div>
        </div>


        <!--amount-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.amount')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"
                        id="basic-addon2">{{ config('system.settings_system_currency_symbol') }}</span>
                    <input type="number" name="expense_amount" id="expense_amount" class="form-control form-control-sm"
                        value="{{ $expense->expense_amount ?? '' }}" aria-describedby="basic-addon2">
                </div>
            </div>
        </div>


        <!--category-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.category')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="expense_categoryid"
                    name="expense_categoryid">
                    @foreach($categories as $category)
                    <option value="{{ $category->category_id }}"
                        {{ runtimePreselected($expense->expense_categoryid ?? '', $category->category_id) }}>{{
                        runtimeLang($category->category_name) }}</option>
                    @endforeach
                </select>
            </div>
        </div>



        <!--column visibility-->
        @if(config('visibility.expense_modal_client_project_fields'))
        <div>
            <!--not yet invoice invoiced - can change client/project-->
            @if(config('visibility.expense_modal_edit_client_and_project'))
            <!--project-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.project')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <!--select2 basic search-->
                    <select name="expense_projectid" id="expense_projectid"
                        class="form-control form-control-sm js-select2-basic-search-modal"
                        data-ajax--url="{{ url('/') }}/feed/projects?ref=general">
                        @if(isset($expense->expense_projectid) && $expense->expense_projectid != '')
                        <option value="{{ $expense->expense_projectid ?? '' }}">{{ $expense->project_title }}
                        </option>
                        @endif
                    </select>
                    <!--select2 basic search-->
                </div>
            </div>
            @else
            <!--already invoiced - cannot change client/project-->
            <!--existing client-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.client')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control" value="{{ $expense->client_company_name ?? '' }}" disabled>
                    <input type="hidden" name="expense_clientid" value="{{ $expense->expense_clientid ?? '' }}">
                </div>
            </div>
            <!--existing client-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.project')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" value="{{ $expense->project_title ?? '' }}"
                        disabled>
                    <input type="hidden" name="expense_projectid" value="{{ $expense->expense_projectid ?? '' }}">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-12 text-right">
                    <small>{{ cleanLang(__('lang.expense_has_already_been_invoiced')) }}</small>
                </div>
            </div>
            <div class="line"></div>
            @endif
        </div>
        @endif

        <!--clients projects-->
        @if(config('visibility.expense_modal_clients_projects'))
        <div class="form-group row">
            <label for="example-month-input"
                class="col-sm-12 col-lg-3 col-form-label text-left">{{ cleanLang(__('lang.project')) }}</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="expense_projectid"
                    name="expense_projectid">
                    @foreach(config('settings.clients_projects') as $project)
                    <option value="{{ $project->project_id ?? '' }}">{{ $project->project_title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        <!--project manager<>-->
        @if(auth()->user()->role->role_expenses_scope == 'global')
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.team_member')) }}</label>
            <div class="col-sm-12 col-lg-9">
                <select name="expense_creatorid" id="expense_creatorid"
                    class="select2-basic form-control form-control-sm">
                    <option></option>
                    <!--users list-->
                    @foreach(config('system.team_members') as $user)
                    <option value="{{ $user->id }}"
                        {{ runtimePreselected($user->id, $expense->expense_creatorid ?? '') }}>{{
                        $user->full_name }}</option>
                    @endforeach

                    <!--/#users list-->
                </select>
            </div>
        </div>
        @endif


        <!--billable-->
        <div class="form-group form-group-checkbox row" id="expense_billable_option">
            <label class="col-sm-12 col-lg-3 col-form-label text-left">{{ cleanLang(__('lang.billable')) }}?</label>
            <div class="col-6 text-left p-t-5">
                @if(isset($page['section']) && $page['section'] == 'edit')
                <input type="checkbox" id="expense_billable" name="expense_billable"
                    class="filled-in chk-col-light-blue" {{ runtimePrechecked($expense['expense_billable'] ?? '') }}
                    {{runtimeExpenseBillable($expense->expense_billing_status ?? '') }}>
                @else
                <input type="checkbox" id="expense_billable" name="expense_billable"
                    class="filled-in chk-col-light-blue"
                    {{runtimePrechecked(config('system.settings_expenses_billable_by_default')) }}>
                @endif
                <label for="expense_billable"></label>
            </div>
        </div>

        <div class="line"></div>


        <!--attach recipt - toggle-->
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.attach_receipt')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="show_more_settings_expenses" id="show_more_settings_expenses"
                            class="js-switch-toggle-hidden-content" data-target="add_expense_attach_receipt">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>


        <!--attach recipt-->
        <div class="hidden" id="add_expense_attach_receipt">
            <!--fileupload-->
            <div class="form-group row">
                <div class="col-sm-12">
                    <div class="dropzone dz-clickable" id="fileupload_expense_receipt">
                        <div class="dz-default dz-message">
                            <i class="icon-Upload-toCloud"></i>
                            <span>{{ cleanLang(__('lang.drag_drop_file')) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--fileupload-->
            <!--existing files-->
            @if(isset($page['section']) && $page['section'] == 'edit')
            <table class="table table-bordered">
                <tbody>
                    @foreach($attachments as $attachment)
                    <tr id="expense_attachment_{{ $attachment->attachment_id }}">
                        <td>{{ $attachment->attachment_filename }} </td>
                        <td class="w-px-40"> <button type="button"
                                class="btn btn-danger btn-circle btn-sm confirm-action-danger"
                                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" active"
                                data-ajax-type="DELETE"
                                data-url="{{ url('/expenses/attachments/'.$attachment->attachment_uniqiueid) }}">
                                <i class="sl-icon-trash"></i>
                            </button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <!--pass source-->
        <input type="hidden" name="source" value="{{ request('source') }}">
        <input type="hidden" name="ref" value="{{ request('ref') }}">

        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>
    </div>
</div>