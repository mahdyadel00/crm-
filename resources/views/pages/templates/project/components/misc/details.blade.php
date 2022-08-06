<!-- Column -->
<div class="card" id="project_details" data-progress="{{ $project->project_progress }}">
    <div class="card-body p-t-10 p-b-10" id="project_progress_container">
        <!--project progress-->
        <div class="d-flex no-block">
            <div class="align-self-end no-shrink">
                <h5 class="m-b-0">{{ cleanLang(__('lang.progress')) }}</h5>
                <!--[team]-->
                @if(auth()->user()->is_team)
                @if($project->project_progress_manually == 'yes')
                <h6 class="text-muted">{{ cleanLang(__('lang.manually_set_progress')) }}</h6>
                @else
                <h6 class="text-muted">{{ cleanLang(__('lang.task_based_progress')) }}</h6>
                @endif
                @else
                <!--[client]-->
                <h6 class="text-muted">{{ cleanLang(__('lang.project_progress')) }}</h6>
                @endif
            </div>
            <div class="ml-auto">
                <div id="project_progress_chart"></div>
            </div>
        </div>
        <!--project progress-->
        <!--this item is archived notice-->
        @if($project->project_active_state == 'archived' && runtimeArchivingOptions())
        <div
            class="alert alert-warning p-t-7 p-b-7 m-t-10 m-b--20{{ runtimeActivateOrAchive('archived-notice', $project->project_active_state) }}">
            <i class="mdi mdi-archive"></i> @lang('lang.this_project_is_archived')
        </div>
        @endif
    </div>
    <!--hidden-->
    <div class="card-body p-t-0 p-b-0 d-none" id="project_progress_hidden">
        <div>
            <table class="table no-border m-b-0">
                <tbody>
                    <tr>
                        <td class="p-l-0 p-t-5">
                            <h5 class="m-b-0">{{ cleanLang(__('lang.progress')) }}</h5>
                            <h6 class="text-muted">{{ cleanLang(__('lang.tasks')) }}</h6>
                        </td>
                        <td class="font-medium p-r-0 p-t-5 w-50 vt">
                            <span class="project_progress_hidden_text">30%</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="m-t-10 m-b-10">
        <hr>
    </div>
    <div class="card-body p-t-0 p-b-0">
        <!--[client details]-->
        @if(auth()->user()->is_team)
        <div class="p-b-20">
            <h6><a href="/clients/{{ $project->client_id }}">{{ $project->client_company_name }}</a></h6>
            <div>
                @foreach($contacts as $contact)
                <span data-toggle="tooltip" title="{{ $contact->first_name	}} {{ $contact->last_name	}}"><img
                        src="{{ getUsersAvatar($contact->avatar_directory, $contact->avatar_filename) }}" alt="user"
                        class="img-circle avatar-xsmall"></span>
                @endforeach
            </div>
        </div>
        @endif

        <!--assigned-->
        <div class="row">
            <div class="col-sm-6">
                <div class="panel-label p-b-3">{{ cleanLang(__('lang.assigned')) }}</div>
                <div>
                    @foreach($project->assigned as $team)
                    <span data-toggle="tooltip" title="{{ $team->first_name	}} {{ $team->last_name	}}"><img
                            src="{{ getUsersAvatar($team->avatar_directory, $team->avatar_filename) }}" alt="user"
                            class="img-circle avatar-xsmall"></span>
                    @endforeach
                    @if($project->assigned()->count() == 0)
                    ---
                    @endif
                </div>
            </div>

            <!--project manager-->
            <div class="col-sm-6">
                @if(auth()->user()->is_team)
                <div class="panel-label p-b-3">{{ cleanLang(__('lang.project_manager')) }}</div>
                <div>
                    @foreach($project->managers as $team)
                    <span data-toggle="tooltip" title="{{ $team->first_name	}} {{ $team->last_name	}}"><img
                            src="{{ getUsersAvatar($team->avatar_directory, $team->avatar_filename) }}" alt="user"
                            class="img-circle avatar-xsmall"></span>
                    @endforeach
                    @if($project->managers()->count() == 0)
                    ---
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="m-t-10 m-b-10">
        <hr>
    </div>
    <!--dates-->
    <div class="card-body p-t-0 p-b-0">
        <div class="row">
            <div class="col-sm-6">
                <div>
                    <div class="panel-label p-b-3">{{ cleanLang(__('lang.start_date')) }}</div>
                    <div>{{ runtimeDate($project->project_date_start) }}</div>
                </div>

                <div class="m-t-20">
                    <div class="panel-label p-b-3">{{ cleanLang(__('lang.category')) }}</div>
                    <div>{{ $project->category_name }}</div>
                </div>

            </div>
            <div class="col-sm-6">
                <div>
                    <div class="panel-label p-b-3">{{ cleanLang(__('lang.due_date')) }}</div>
                    <div>{{ runtimeDate($project->project_date_due) }}</div>
                </div>
                <div class="m-t-20">
                    <div class="panel-label p-b-3">{{ cleanLang(__('lang.status')) }}</div>
                    <div><span
                            class="label {{ runtimeProjectStatusColors($project->project_status, 'label') }}">{{ runtimeLang($project->project_status) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="m-t-10 m-b-10">
        <hr>
    </div>
    <!--billing details-->
    @if(config('visibility.project_billing_summary'))
    <div class="card-body p-t-0 p-b-0">
        <div class="row">
            <div class="col-sm-6">
                <div>
                    <div class="panel-label p-b-3">{{ cleanLang(__('lang.billing_type')) }}</div>
                    @if($project->project_date_start == 'hourly')
                    <div>{{ cleanLang(__('lang.hourly')) }}</div>
                    @else
                    <div>{{ cleanLang(__('lang.fixed_fee')) }}</div>
                    @endif
                </div>

                <div class="m-t-20">
                    <div class="panel-label p-b-3">{{ cleanLang(__('lang.estimated_hours')) }}</div>
                    <div>{{ $project->project_billing_estimated_hours }} {{ strtolower(__('lang.hrs')) }}
                    </div>
                </div>

            </div>
            <div class="col-sm-6">
                <div>
                    <div class="panel-label p-b-3">{{ cleanLang(__('lang.rate')) }}</div>
                    <div>{{ runtimeMoneyFormat($project->project_billing_rate) }}</div>
                </div>
                <div class="m-t-20">
                    <div class="panel-label p-b-3">{{ cleanLang(__('lang.time_spent')) }}</div>
                    <div>{{ $payload['time_logged'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(config('settings.project_permissions_view_invoices'))
    <!--INVOICES-->
    <div class="m-t-10 m-b-10">
        <hr>
    </div>
    <div class="card-body p-t-0 p-b-0">
        <div>
            <table class="table no-border m-b-0">
                <tbody>
                    <tr>
                        <td class="p-l-0 p-t-5 w-50">{{ cleanLang(__('lang.all_invoices')) }}</td>
                        <td class="font-medium p-r-0 p-t-5">
                            {{ runtimeMoneyFormat($project->sum_invoices_all) }}
                            <div class="progress">
                                @if($project->sum_invoices_all > 0)
                                <div class="progress-bar bg-info w-100 h-px-3" role="progressbar">
                                    @else
                                    <div class="progress-bar bg-info w-0 h-px-3" role="progressbar">
                                        @endif
                                    </div>
                                </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-l-0 p-t-5">{{ cleanLang(__('lang.paid_invoices')) }}</td>
                        <td class="font-medium p-r-0 p-t-5">
                            {{ runtimeMoneyFormat($project->sum_invoices_paid) }}
                            <div class="progress">
                                <div class="progress-bar bg-success {{ runtimeProjectInvoicesBars($project->sum_invoices_all, $project->sum_invoices_paid) }}"
                                    role="progressbar"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-l-0 p-t-5">{{ cleanLang(__('lang.due_invoices')) }}</td>
                        <td class="font-medium p-r-0 p-t-5">
                            {{ runtimeMoneyFormat($project->sum_invoices_due) }}
                            <div class="progress">
                                <div class="progress-bar bg-warning {{ runtimeProjectInvoicesBars($project->sum_invoices_all, $project->sum_invoices_due) }}"
                                    role="progressbar"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-l-0 p-t-5">{{ cleanLang(__('lang.overdue_invoices')) }}</td>
                        <td class="font-medium p-r-0 p-t-5">
                            {{ runtimeMoneyFormat($project->sum_invoices_overdue) }}
                            <div class="progress">
                                <div class="progress-bar bg-danger {{ runtimeProjectInvoicesBars($project->sum_invoices_all, $project->sum_invoices_overdue) }}"
                                    role="progressbar"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif


    @if(config('visibility.project_show_custom_fields'))
    <!--CUSTOMER FIELDS-->
    <div class="m-t-10 m-b-10">
        <hr>
    </div>
    <div class="card-body p-t-0 p-b-0">
        @foreach($fields as $field)
        @if($field->customfields_show_project_page == 'yes')
        <div class="x-each-field m-b-18">
            <div class="panel-label p-b-3">{{ $field->customfields_title }}
            </div>
            <div class="x-content">{{ strip_tags(customFieldValue($field->customfields_name, $project, $field->customfields_datatype)) }}</div>
        </div>
        @endif
        @endforeach

        @if(config('app.application_demo_mode'))
        <!--DEMO INFO-->
        <div class="alert alert-info">
            <h5 class="text-info"><i class="sl-icon-info"></i> Demo Info</h5> 
            These are custom fields. You can change them or <a href="{{ url('app/settings/customfields/projects') }}">create your own.</a>
        </div>
        @endif
        
    </div>
    @endif

    <div class="d-none last-line">
        <hr>
    </div>
</div>
<!-- Column -->