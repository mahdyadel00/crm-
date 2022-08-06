@foreach($projects as $project)
<div class="col-sm-12 col-md-4 col-lg-3 click-url" id="project_{{ $project->project_id }}" data-url="{{ url('/projects/'.$project->project_id) }}">

    <div class="grid-card m-b-35">

        <!--COVER IMAGE-->
        @if(config('visibility.card_cover_image'))
        <div class="grid-card-img-container" {!! clean(getCoverImage($project->project_cover_directory ?? '', $project->project_cover_filename ?? '')) !!}>
        </div>
        @endif

        <div class="grid-card-content project-card">

            <!--TITLE-->
            <div class="x-title wordwrap" title="{{ $project->project_title }}">{{ str_limit($project->project_title ??'---', 28) }}
                <!--ACTION BUTTONS (team)-->
                @if(config('visibility.action_buttons_edit'))
                <span class="x-action-button" id="card-action-button-123" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></span>
                <div class="dropdown-menu dropdown-menu-small dropdown-menu-right js-stop-propagation"
                    aria-labelledby="listTableAction">
                    @include('pages.projects.views.common.dropdown-menu-team')
                    <!--change cover image-->
                    @if(config('visibility.edit_card_cover_image'))
                    <a class="dropdown-item js-ajax-ux-request edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                        data-modal-title="{{ cleanLang(__('lang.change_cover_image')) }}"
                        data-url="{{ urlResource('/projects/'.$project->project_id.'/change-cover-image') }}"
                        data-action-url="{{ urlResource('/projects/'.$project->project_id.'/change-cover-image') }}"
                        data-loading-target="commonModalBody" data-action-method="POST">
                        {{ cleanLang(__('lang.change_cover_image')) }}</a>
                    @endif
                </div>
                @endif
            </div>

            <!--PROGRESS-->
            <div class="projects_col_progress progress m-t-4" data-toggle="tooltip"
                title="{{ $project->project_progress }}%">
                @if($project->project_progress == 100)
                <div class="progress-bar bg-success w-100 h-px-4 font-11 font-weight-500" data-toggle="tooltip"
                    title="100%" role="progressbar"></div>
                @else
                <div class="progress-bar bg-info h-px-4 font-16 font-weight-500 w-{{ round($project->project_progress) }}"
                    role="progressbar"></div>
                @endif
            </div>

            <div class="x-meta p-t-15">

                <!--STATUS-->
                <div class="projects_col_status m-b-9">
                    <span
                        class="label {{ runtimeProjectStatusColors($project->project_status, 'label') }}">{{ runtimeLang($project->project_status) }}</span>
                    <!--archived-->
                    @if($project->project_active_state == 'archived' && runtimeArchivingOptions())
                    <span class="projects_col_status label label-icons label-icons-default" data-toggle="tooltip"
                        data-placement="top" title="@lang('lang.archived')"><i class="ti-archive"></i></span>
                    @endif
                </div>

                <!--ID-->
                <div class="projects_col_client p-l-3">
                    <span><strong>@lang('lang.client'):</strong> <a
                            href="/clients/{{ $project->project_clientid }}">{{ str_limit($project->client_company_name ??'---', 30) }}</a></span>
                </div>

                <!--DATE CREATED-->
                <div class="projects_col_start_date p-l-3">
                    <span><strong>@lang('lang.start_date'):</strong> {{ runtimeDate($project->project_created) }}</span>
                </div>

                <!--DUE DATE-->
                <div class="projects_col_end_date p-l-3">
                    <span><strong>@lang('lang.due_date'):</strong> {{ runtimeDate($project->project_date_due) }}</span>
                </div>

                <!--ID-->
                <div class="p-l-3">
                    <span><strong>@lang('lang.id'):</strong> {{ $project->project_id }}</span>
                </div>

                <div class="p-t-3">
                    <!--assigned users-->
                    @if(count($project->assigned) > 0)
                    @foreach($project->assigned->take(7) as $user)
                    <img src="{{ $user->avatar }}" data-toggle="tooltip" title="{{ $user->first_name }}"
                        data-placement="top" alt="{{ $user->first_name }}"
                        class="img-circle avatar-xsmall w-px-25 h-px-25">
                    @endforeach
                    @endif
                    <!--assigned users-->
                    <!--more users-->
                    @if(count($project->assigned) > 1)
                    @php $more_users_title = __('lang.assigned_users'); $users = $project->assigned; @endphp
                    @include('misc.more-users')
                    @endif
                    <!--more users-->


                </div>

            </div>
        </div>
    </div>

</div>
@endforeach
<!--each row-->