    <!----------Assigned----------->
    <div class="x-section">
        <div class="x-title">
            <h6>{{ cleanLang(__('lang.assigned_users')) }}</h6>
        </div>
        <span id="lead-assigned-container" class="">
            @include('pages.lead.components.assigned')
        </span>
        <!--user-->
        <span class="x-assigned-user x-assign-new js-card-settings-button-static card-lead-assigned text-info"
            tabindex="0" data-popover-content="card-lead-team" data-title="{{ cleanLang(__('lang.assign_users')) }}"><i
                class="mdi mdi-plus"></i></span>
    </div>

    <!----------settings----------->
    <div class="x-section">

        <!--customer-->
        @if($lead->lead_converted == 'yes')
        <div class="x-element x-customer">{{ cleanLang(__('lang.customer')) }}</div>
        @endif

        <div class="x-title">
            <h6>{{ cleanLang(__('lang.details')) }}</h6>
        </div>
        <!--Name-->
        <div class="x-element text-center font-14" id="card-lead-element-container-name">
            @if($lead->permission_edit_lead)
            <span class="x-highlight x-editable js-card-settings-button-static" id="card-lead-name" tabindex="0"
                data-popover-content="card-lead-name-popover" data-title="{{ cleanLang(__('lang.name')) }}">
                <span id="card-lead-firstname-containter">{{ $lead->lead_firstname }}</span> <span
                    id="card-lead-lastname-containter">{{ $lead->lead_lastname }}</span></span>
            @else
            <span class="x-highlight">{{ $lead->lead_firstname }} {{ $lead->lead_lastname }}</span>
            @endif
        </div>
        <!--value-->
        <div class="x-element"><i class="mdi mdi-cash-multiple"></i> <span>{{ cleanLang(__('lang.value')) }}: </span>
            @if($lead->permission_edit_lead)
            <span class="x-highlight x-editable js-card-settings-button-static" id="card-lead-value" tabindex="0"
                data-popover-content="card-lead-value-popover" data-value="{{ $lead->lead_value }}"
                data-title="{{ cleanLang(__('lang.value')) }}">{{ runtimeMoneyFormat($lead->lead_value) }}</span>
            @else
            <span class="x-highlight">{{ runtimeMoneyFormat($lead->lead_value) }}</span>
            @endif
        </div>
        <!--status-->
        <div class="x-element" id="card-lead-status"><i class="mdi mdi-flag"></i>
            <span>{{ cleanLang(__('lang.status')) }}: </span>
            @if($lead->permission_edit_lead)
            <span class="x-highlight x-editable js-card-settings-button-static" id="card-lead-status-text" tabindex="0"
                data-popover-content="card-lead-status-popover"
                data-title="{{ cleanLang(__('lang.status')) }}">{{ runtimeLang($lead->leadstatus_title) }}</strong></span>
            @else
            <span class="x-highlight">{{ runtimeLang($lead->leadstatus_title) }}</span>
            @endif
        </div>
        <!--added-->
        <div class="x-element" id="lead-date-added"><i class="mdi mdi-calendar-plus"></i>
            <span>{{ cleanLang(__('lang.added')) }}:</span>
            @if($lead->permission_edit_lead)
            <span class="x-highlight x-editable card-pickadate"
                data-url="{{ url('/leads/'.$lead->lead_id.'/update-date-added/') }}" data-type="form"
                data-form-id="lead-date-added" data-hidden-field="lead_created"
                data-container="lead-date-added-container" data-ajax-type="post"
                id="lead-date-added-container">{{ runtimeDate($lead->lead_created) }}</span></span>
            <input type="hidden" name="lead_created" id="lead_created">
            @else
            <span class="x-highlight">{{ runtimeDate($lead->lead_created) }}</span>
            @endif
        </div>

        <!--category-->
        <div class="x-element" id="card-lead-category"><i class="mdi mdi-folder"></i>
            <span>{{ cleanLang(__('lang.category')) }}:
            </span>
            @if($lead->permission_edit_lead)
            <span class="x-highlight x-editable js-card-settings-button-static" id="card-lead-category-text"
                tabindex="0" data-popover-content="card-lead-category-popover"
                data-title="{{ cleanLang(__('lang.status')) }}">{{ runtimeLang($lead->category_name) }}</strong></span>
            @else
            <span class="x-highlight">{{ runtimeLang($lead->category_name) }}</span>
            @endif
        </div>
        <!--last contacted-->
        <div class="x-element" id="lead-contacted"><i class="mdi mdi-message-text"></i>
            <span>{{ cleanLang(__('lang.contacted')) }}:
            </span>
            @if($lead->permission_edit_lead)
            <span class="x-highlight x-editable card-pickadate"
                data-url="{{ url('/leads/'.$lead->lead_id.'/update-contacted/') }}" data-type="form"
                data-progress-bar='hidden' data-form-id="lead-contacted" data-hidden-field="lead_last_contacted"
                data-container="lead-contacted-container" data-ajax-type="post"
                id="lead-contacted-container">{{ runtimeDate($lead->lead_last_contacted) }}</span>
            <input type="hidden" name="lead_last_contacted" id="lead_last_contacted">
            @else
            <span class="x-highlight">{{ runtimeDate($lead->lead_last_contacted) }}</span>
            @endif
        </div>
        <!--telephone-->
        <div class="x-element"><i class="mdi mdi-phone"></i> <span>{{ cleanLang(__('lang.telephone')) }}: </span>
            @if($lead->permission_edit_lead)
            <span class="x-highlight x-editable js-card-settings-button-static" id="card-lead-phone" tabindex="0"
                data-popover-content="card-lead-phone-popover" data-value="{{ $lead->lead_phone }}"
                data-title="{{ cleanLang(__('lang.telephone')) }}">{{ $lead->lead_phone ?? '---' }}</span>
            @else
            <span class="x-highlight">{{ $lead->lead_phone ?? '---' }}</span>
            @endif
        </div>

        <!--email-->
        <div class="x-element"><i class="mdi mdi-email"></i> <span>{{ cleanLang(__('lang.email')) }}: </span>
            @if($lead->permission_edit_lead)
            <span class="x-highlight x-editable js-card-settings-button-static" id="card-lead-email" tabindex="0"
                data-popover-content="card-lead-email-popover" data-value="{{ $lead->lead_email }}"
                data-title="{{ cleanLang(__('lang.email')) }}">{{ $lead->lead_email ?? '---' }}</span>
            @else
            <span class="x-highlight">{{ $lead->lead_email ?? '---' }}</span>
            @endif
        </div>

        <!--Source-->
        <div class="x-element" id="card-lead-source"><i class="mdi mdi-magnify-plus"></i>
            <span>{{ cleanLang(__('lang.source')) }}:
            </span>
            @if($lead->permission_edit_lead)
            <span class="x-highlight x-editable js-card-settings-button-static" id="card-lead-source-text" tabindex="0"
                data-popover-content="card-lead-source-popover"
                data-title="{{ cleanLang(__('lang.source')) }}">{{ $lead->lead_source ?? '---' }}</strong></span>
            @else
            <span class="x-highlight">{{ $lead->lead_source ?? '---' }}</span>
            @endif
        </div>


        <!--reminder-->
        @if(config('visibility.modules.reminders'))
        <div class="card-reminders-container" id="card-reminders-container">
            @include('pages.reminders.cards.wrapper')
        </div>
        @endif

    </div>




    <!----------tags----------->
    <div class="card-tags-container" id="card-tags-container">
        @include('pages.lead.components.tags')
    </div>


    <!----------actions----------->
    <div class="x-section">
        <div class="x-title">
            <h6>{{ cleanLang(__('lang.actions')) }}</h6>
        </div>
        <!--convert to customer-->
        @if($lead->permission_edit_lead && $lead->lead_converted == 'no')
        <div class="x-element x-action js-lead-convert-to-customer" id="card-lead-milestone" tabindex="0"
            data-popover-content="card-lead-milestones" data-title="{{ cleanLang(__('lang.convert_to_customer')) }}"><i
                class="mdi mdi-redo-variant"></i>
            <span class="x-highlight">@lang('lang.convert_to_customer')</strong></span>
        </div>
        @endif

        <!--archive-->
        @if($lead->permission_edit_lead && runtimeArchivingOptions())
        <div class="x-element x-action confirm-action-info  {{ runtimeActivateOrAchive('archive-button', $lead->lead_active_state) }} card_archive_button_{{ $lead->lead_id }}"
            id="card_archive_button_{{ $lead->lead_id }}" data-confirm-title="{{ cleanLang(__('lang.archive_lead')) }}"
            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
            data-url="{{ url('/') }}/leads/{{ $lead->lead_id }}/archive"><i class="mdi mdi-archive"></i> <span
                class="x-highlight" id="lead-start-date">{{ cleanLang(__('lang.archive')) }}</span></span></div>
        @endif

        <!--restore-->
        @if($lead->permission_edit_lead && runtimeArchivingOptions())
        <div class="x-element x-action confirm-action-info  {{ runtimeActivateOrAchive('activate-button', $lead->lead_active_state) }} card_restore_button_{{ $lead->lead_id }}"
            id="card_restore_button_{{ $lead->lead_id }}" data-confirm-title="{{ cleanLang(__('lang.restore_lead')) }}"
            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
            data-url="{{ url('/') }}/leads/{{ $lead->lead_id }}/activate"><i class="mdi mdi-archive"></i> <span
                class="x-highlight" id="lead-start-date">{{ cleanLang(__('lang.restore')) }}</span></span></div>
        @endif


        <!--delete-->
        @if($lead->permission_delete_lead)
        <div class="x-element x-action confirm-action-danger"
            data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
            data-url="{{ url('/') }}/leads/{{ $lead->lead_id }}"><i class="mdi mdi-delete"></i> <span
                class="x-highlight" id="lead-start-date">{{ cleanLang(__('lang.delete')) }}</span></span></div>
        @endif
    </div>

    <!----------meta infor----------->
    <div class="x-section">
        <div class="x-title">
            <h6>{{ cleanLang(__('lang.information')) }}</h6>
        </div>
        <div class="x-element x-action">
            <table class=" table table-bordered table-sm">
                <tbody>
                    <tr>
                        <td>{{ cleanLang(__('lang.lead_id')) }}</td>
                        <td><strong>#{{ $lead->lead_id }}</strong></td>
                    </tr>
                    <tr>
                        <td>{{ cleanLang(__('lang.created_by')) }}</td>
                        <td><strong>{{ $lead->first_name }} {{ $lead->last_name }}</strong></td>
                    </tr>
                    <tr>
                        <td>{{ cleanLang(__('lang.date_created')) }}</td>
                        <td><strong>{{ runtimeDate($lead->lead_created) }}</strong></td>
                    </tr>
                    @if($lead->lead_converted == 'yes')
                    <tr>
                        <td>{{ cleanLang(__('lang.converted')) }}</td>
                        <td><strong>{{ runtimeDate($lead->lead_converted_date) }}</strong></td>
                    </tr>
                    <tr>
                        <td>{{ cleanLang(__('lang.converted_by')) }}</td>
                        <td><strong>{{ $lead->converted_by_first_name }}
                                {{ $lead->converted_by_last_name }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ cleanLang(__('lang.client_id')) }}</td>
                        <td><strong><a
                                    href="{{ url('client/'.$lead->lead_converted_clientid) }}">#{{ $lead->lead_converted_clientid }}</a></strong>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>




    <!----------------------------------------------------- components-------------------------------------------------------->

    <!--lead - contact name -->
    <div class="hidden" id="card-lead-name-popover">
        <div class="form-group row m-b-10">
            <label
                class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.first_name')) }}</label>
            <div class="col-sm-12 ">
                <input type="text" class="form-control form-control-sm" id="lead_firstname" name="lead_firstname"
                    placeholder="">
            </div>
        </div>
        <div class="form-group row m-b-10">
            <label
                class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.last_name')) }}</label>
            <div class="col-sm-12">
                <input type="text" class="form-control form-control-sm" id="lead_lastname" name="lead_lastname"
                    placeholder="">
            </div>
        </div>
        <div class="form-group text-right">
            <button type="button" class="btn btn-danger btn-sm" id="card-leads-update-name-button"
                data-progress-bar='hidden' data-url="{{ url('/leads/'.$lead->lead_id.'/update-name') }}"
                data-type="form" data-ajax-type="post" data-form-id="popover-body">
                {{ cleanLang(__('lang.update')) }}
            </button>
        </div>
    </div>

    <!--lead - value -->
    <div class="hidden" id="card-lead-value-popover">
        <div class="form-group row m-b-10">
            <div class="col-sm-12 ">
                <input type="number" class="form-control form-control-sm" id="lead_value" name="lead_value"
                    placeholder="">
            </div>
        </div>
        <div class="form-group text-right">
            <button type="button" class="btn btn-danger btn-sm" id="card-leads-update-value-button"
                data-progress-bar='hidden' data-url="{{ url('/leads/'.$lead->lead_id.'/update-value') }}"
                data-type="form" data-ajax-type="post" data-form-id="popover-body">
                {{ cleanLang(__('lang.update')) }}
            </button>
        </div>
    </div>


    <!--lead status - popover -->
    <div class="hidden" id="card-lead-status-popover">
        <div class="form-group m-t-10">
            <select class="custom-select col-12 form-control form-control-sm" id="lead_status" name="lead_status">
                @foreach($statuses as $statuse)
                <option value="{{ $statuse->leadstatus_id }}">
                    {{ runtimeLang($statuse->leadstatus_title) }}</option>
                @endforeach
            </select>
            <input type="hidden" id="current_lead_status_text" name="current_lead_status_text" value="">
        </div>
        <div class="form-group text-right">
            <button type="button" class="btn btn-danger btn-sm" id="card-leads-update-status-button"
                data-progress-bar='hidden' data-url="{{ url('/leads/'.$lead->lead_id.'/update-status') }}"
                data-type="form" data-ajax-type="post" data-form-id="popover-body">
                {{ cleanLang(__('lang.update')) }}
            </button>
        </div>
    </div>


    <!--lead category - popover -->
    <div class="hidden" id="card-lead-category-popover">
        <div class="form-group m-t-10">
            <select class="custom-select col-12 form-control form-control-sm" id="lead_categoryid"
                name="lead_categoryid">
                @foreach($categories as $category)
                <option value="{{ $category->category_id }}">
                    {{ runtimeLang($category->category_name) }}</option>
                @endforeach
            </select>
            <input type="hidden" id="current_lead_category_text" name="current_lead_category_text" value="">
        </div>
        <div class="form-group text-right">
            <button type="button" class="btn btn-danger btn-sm" id="card-leads-update-category-button"
                data-progress-bar='hidden' data-url="{{ url('/leads/'.$lead->lead_id.'/update-category') }}"
                data-type="form" data-ajax-type="post" data-form-id="popover-body">
                {{ cleanLang(__('lang.update')) }}
            </button>
        </div>
    </div>



    <!--lead - phone -->
    <div class="hidden" id="card-lead-phone-popover">
        <div class="form-group row m-b-10">
            <div class="col-sm-12 ">
                <input type="text" class="form-control form-control-sm" id="lead_phone" name="lead_phone"
                    placeholder="">
            </div>
        </div>
        <div class="form-group text-right">
            <button type="button" class="btn btn-danger btn-sm" id="card-leads-update-phone-button"
                data-progress-bar='hidden' data-url="{{ url('/leads/'.$lead->lead_id.'/update-phone') }}"
                data-type="form" data-ajax-type="post" data-form-id="popover-body">
                {{ cleanLang(__('lang.update')) }}
            </button>
        </div>
    </div>

    <!--lead - email -->
    <div class="hidden" id="card-lead-email-popover">
        <div class="form-group row m-b-10">
            <div class="col-sm-12 ">
                <input type="text" class="form-control form-control-sm" id="lead_email" name="lead_email"
                    placeholder="">
            </div>
        </div>
        <div class="form-group text-right">
            <button type="button" class="btn btn-danger btn-sm" id="card-leads-update-email-button"
                data-progress-bar='hidden' data-url="{{ url('/leads/'.$lead->lead_id.'/update-email') }}"
                data-type="form" data-ajax-type="post" data-form-id="popover-body">
                {{ cleanLang(__('lang.update')) }}
            </button>
        </div>
    </div>

    <!--lead source - popover -->
    <div class="hidden" id="card-lead-source-popover">
        <div class="form-group m-t-10">
            @if(config('system.settings_leads_allow_new_sources') == 'yes')
            <input type="text" name="lead_source" id="lead_source" class="col-12 form-control form-control-sm"
                list="sources">
            <datalist id="sources">
                @foreach($sources as $source)
                <option value="{{ $source->leadsources_title }}">
                    @endforeach
            </datalist>
            @else
            <select class="custom-select col-12 form-control form-control-sm" id="lead_source" name="lead_source">
                @foreach($sources as $source)
                <option value="{{ $source->leadsources_title }}">
                    {{ runtimeLang($source->leadsources_title) }}</option>
                @endforeach
            </select>
            @endif

        </div>
        @if($lead->permission_edit_lead)
        <div class="form-group text-right">
            <button type="button" class="btn btn-danger btn-sm" id="card-leads-update-source-button"
                data-progress-bar='hidden' data-url="{{ url('/leads/'.$lead->lead_id.'/update-source') }}"
                data-type="form" data-ajax-type="post" data-form-id="popover-body">
                {{ cleanLang(__('lang.update')) }}
            </button>
        </div>
        @endif
    </div>


    <!--assign user-->
    <div class="hidden" id="card-lead-team">
        <div class="card-assigned-popover-content">
            @foreach(config('system.team_members') as $staff)
            <div class="form-check m-b-15">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="assigned[{{ $staff->id }}]"
                        class="custom-control-input  assigned_user_{{ $staff->id }}">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description"><img
                            src="{{ getUsersAvatar($staff->avatar_directory, $staff->avatar_filename) }}"
                            class="img-circle avatar-xsmall"> {{ $staff->first_name }} {{ $staff->last_name }}</span>
                </label>
            </div>
            @endforeach
            <div class="form-group text-right">
                <button type="button" class="btn btn-danger btn-sm" id="card-leads-update-assigned"
                    data-progress-bar='hidden' data-url="{{ url('/leads/'.$lead->lead_id.'/update-assigned') }}"
                    data-type="form" data-ajax-type="post" data-form-id="popover-body">
                    {{ cleanLang(__('lang.update')) }}
                </button>
            </div>
        </div>
    </div>