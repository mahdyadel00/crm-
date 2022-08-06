<header class="topbar">

    <nav class="navbar top-navbar navbar-expand-md navbar-light">

        <div class="navbar-header" id="topnav-logo-container">


            @if(request('dashboard_section') == 'settings')
            <!--exist-->
            <div class="sidenav-menu-item exit-panel m-b-17">
                <a class="waves-effect waves-dark text-info" href="/home" id="settings-exit-button"
                    aria-expanded="false" target="_self">
                    <i class="sl-icon-logout text-info"></i>
                    <span id="settings-exit-text" class="font-14">{{ str_limit(__('lang.exit_settings'), 20) }}</span>
                </a>
            </div>
            @else
            <!--logo-->
            <div class="sidenav-menu-item logo m-t-0">
                <a class="navbar-brand" href="/home">
                    <img src="{{ runtimeLogoSmall() }}" alt="homepage" class="logo-small" />
                    <img src="{{ runtimeLogoLarge() }}" alt="homepage" class="logo-large" />
                </a>
            </div>
            @endif
        </div>


        <div class="navbar-collapse header-overlay" id="main-top-nav-bar">

            <div class="page-wrapper-overlay js-close-side-panels hidden" data-target=""></div>

            <ul class="navbar-nav mr-auto">

                <!--left menu toogle (hamburger menu) - main application -->
                @if(request('visibility_left_menu_toggle_button') == 'visible')
                <li class="nav-item main-hamburger-menu">
                    <a class="nav-link nav-toggler hidden-md-up waves-effect waves-dark" href="javascript:void(0)">
                        <i class="sl-icon-menu"></i>
                    </a>
                </li>
                <li class="nav-item main-hamburger-menu">
                    <a class="nav-link sidebartoggler hidden-sm-down waves-effect waves-dark update-user-ux-preferences"
                        data-type="leftmenu" data-progress-bar="hidden" data-url=""
                        data-url-temp="{{ url('/') }}/{{ auth()->user()->team_or_contact }}/updatepreferences"
                        data-preference-type="leftmenu" href="javascript:void(0)">
                        <i class="sl-icon-menu"></i>
                    </a>
                </li>
                @endif


                <!--left menu toogle (hamburger menu) - settings section -->
                @if(request('visibility_settings_left_menu_toggle_button') == 'visible')
                <li class="nav-item settings-hamburger-menu hidden">
                    <a class="nav-link waves-effect waves-dark js-toggle-settings-menu" href="javascript:void(0)">
                        <i class="sl-icon-menu"></i>
                    </a>
                </li>
                @endif

                <!--timer-->
                @if(auth()->user()->is_team && config('visibility.modules.timetracking'))
                <li class="nav-item dropdown hidden-xs-down my-timer-container {{ runtimeVisibility('topnav-timer', request('show_users_running_timer')) }}"
                    id="my-timer-container-topnav">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="javascript:void(0)"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="timer-container"><i class="ti-timer font-18"></i>
                            <span class="my-timer-time-topnav" id="my-timer-time-topnav">{!!
                                clean(runtimeSecondsHumanReadable(request('users_running_timer'),
                                false)) !!}</span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-left">
                        <div class="active-timer-topnav" id="active-timer-topnav-container">
                            @if(request('users_running_timer_task'))
                            @include('misc.timer-topnav-details')
                            @else
                            <div class="x-heading">@lang('lang.active_timer')</div>
                            <div class="x-task">@lang('lang.task_not_found')</div>
                            <div class="x-button"><button type="button"
                                    class="btn waves-effect waves-light btn-sm btn-danger js-timer-button js-ajax-request timer-stop-button"
                                    data-url="{{ url('tasks/timer/stop?source=topnav') }}"
                                    data-form-id="tasks-list-table"
                                    data-progress-bar='hidden'>@lang('lang.stop_timer')</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </li>
                @endif



                <!--[UPCOMING] search icon-->
                <li class="nav-item hidden-xs-down search-box hidden">
                    <a class="nav-link hidden-sm-down waves-effect waves-dark" href="javascript:void(0)">
                        <i class="icon-Magnifi-Glass2"></i>
                    </a>
                    <form class="app-search">
                        <input type="text" class="form-control" placeholder="Search & enter">
                        <a class="srh-btn">
                            <i class="ti-close"></i>
                        </a>
                    </form>
                </li>
            </ul>


            <!--RIGHT SIDE-->
            <ul class="navbar-nav navbar-top-right my-lg-0" id="right-topnav-navbar">

                <!-- Reminders Notification-->
                <li class="nav-item dropdown {{ request('user_has_due_reminder') }}" id="topnav-reminders-dropdown"
                    data-url="{{ url('reminders/topnav-feed') }}" data-progress-bar='hidden'
                    data-loading-target="topnav-reminders-container">
                    <a class="nav-link dropdown-toggle font-22 waves-dark text-danger p-t-9" href="javascript:void(0)"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti-alarm-clock display-inline-block text-danger"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right mailbox animated bounceInDown top-nav-events">
                        <ul>
                            <li>
                                <div class="drop-title">{{ cleanLang(__('lang.reminders_due')) }}</div>
                            </li>
                            <li>
                                <!--events container-->
                                <div class="message-center topnav-reminders-container" id="topnav-reminders-container">
                                    <!--events added dynamically here-->


                                </div>
                            </li>
                            <li class="hidden" id="topnav-reminders-container-footer">
                                <a class="nav-link text-center " href="javascript:void(0);"
                                    id="topnav-reminders-delete-all"
                                    data-url="{{ url('reminders/delete-all-my-due-reminders') }}"
                                    data-progress-bar='hidden'>
                                    <strong>{{ cleanLang(__('lang.delete_all')) }}</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- Reminders Notification-->


                <!-- event notifications -->
                <li class="nav-item">
                    <a class="nav-link waves-effect waves-dark font-22 p-t-10 p-r-10 js-toggle-notifications-panel"
                        href="javascript:void(0);" data-url="{{ url('events/topnav?eventtracking_status=unread') }}"
                        data-loading-target="sidepanel-notifications-body" data-target="sidepanel-notifications"
                        data-progress-bar='hidden' aria-expanded="false">
                        <i class="sl-icon-bell"></i>
                        <div class="notify {{ runtimeVisibilityNotificationIcon(auth()->user()->count_unread_notifications) }}"
                            id="topnav-notification-icon">
                            <span class="heartbit"></span>
                            <span class="point"></span>
                        </div>
                    </a>
                </li>

                <!--notifications -->

                <!-- record time -->
                @if(auth()->user()->is_team && config('visibility.modules.timetracking'))
                <li class="nav-item" id="topnav-record-time-icon">
                    <a class="nav-link waves-effect waves-dark font-22 p-t-9 p-r-10 edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="#" id="32" data-toggle="modal" data-target="#commonModal"
                        data-modal-title="@lang('lang.record_your_work_time')"
                        data-url="{{ url('/timesheets/create') }}" data-action-url="{{ urlResource('/timesheets') }}"
                        data-modal-size="modal-sm" data-loading-target="commonModalBody" data-action-method="POST"
                        aria-expanded="false">
                        <i class="ti-timer"></i>
                    </a>
                </li>
                @endif

                <!-- settings -->
                @if(auth()->user()->is_admin)
                <li class="nav-item">
                    <a class="nav-link waves-effect waves-dark font-22 p-t-10 p-r-10" href="/settings" id="32"
                        aria-expanded="false">
                        <i class="sl-icon-settings"></i>
                    </a>
                </li>
                @endif

                <!-- add content -->
                @if(auth()->user()->is_team && auth()->user()->can_add_content)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="javascript:void(0)"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="mdi mdi-plus-circle-multiple-outline text-danger font-28"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">

                        <!-- client -->
                        @if(auth()->user()->role->role_projects >= 2)
                        <a href="javascript:void(0)"
                            class="dropdown-item dropdown-item-iconed edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal" data-url="{{ url('clients/create') }}"
                            data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.add_client')) }}"
                            data-action-url="{{ url('/clients') }}" data-action-method="POST"
                            data-action-ajax-loading-target="commonModalBody" data-save-button-class=""
                            data-project-progress="0">
                            <i class="sl-icon-people"></i> {{ cleanLang(__('lang.client')) }}</a>
                        @endif

                        <!-- project -->
                        @if(config('visibility.modules.projects') && auth()->user()->role->role_projects >= 2)
                        <a href="javascript:void(0)"
                            class="dropdown-item dropdown-item-iconed edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal" data-url="{{ url('projects/create') }}"
                            data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.add_project')) }}"
                            data-action-url="{{ url('/projects') }}" data-action-method="POST"
                            data-action-ajax-loading-target="commonModalBody" data-save-button-class=""
                            data-project-progress="0">
                            <i class="ti-folder"></i> {{ cleanLang(__('lang.project')) }}</a>
                        @endif

                        <!-- task -->
                        @if(config('visibility.modules.tasks') && auth()->user()->role->role_tasks >= 2)
                        <a href="javascript:void(0)"
                            class="dropdown-item dropdown-item-iconed edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal"
                            data-url="{{ url('/tasks/create?ref=quickadd') }}" data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.add_task')) }}"
                            data-action-url="{{url('/tasks?ref=quickadd') }}" data-action-method="POST"
                            data-action-ajax-loading-target="commonModalBody" data-save-button-class=""
                            data-project-progress="0">
                            <i class="ti-menu-alt"></i> {{ cleanLang(__('lang.task')) }}</a>
                        @endif

                        <!-- lead -->
                        @if(config('visibility.modules.leads') && auth()->user()->role->role_leads >= 2)
                        <a href="javascript:void(0)"
                            class="dropdown-item dropdown-item-iconed edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal"
                            data-url="{{ url('/leads/create?ref=quickadd') }}" data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.add_lead')) }}"
                            data-action-url="{{url('/leads?ref=quickadd') }}" data-action-method="POST"
                            data-action-ajax-loading-target="commonModalBody" data-save-button-class=""
                            data-project-progress="0">
                            <i class="sl-icon-call-in"></i> {{ cleanLang(__('lang.lead')) }}</a>
                        @endif

                        <!-- invoice -->
                        @if(config('visibility.modules.invoices') && auth()->user()->role->role_invoices >= 2)
                        <a href="javascript:void(0)"
                            class="dropdown-item dropdown-item-iconed edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal"
                            data-url="{{ url('/invoices/create?ref=quickadd') }}" data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.add_invoice')) }}"
                            data-action-url="{{url('/invoices?ref=quickadd') }}" data-action-method="POST"
                            data-action-ajax-loading-target="commonModalBody" data-save-button-class=""
                            data-project-progress="0">
                            <i class="sl-icon-doc"></i> {{ cleanLang(__('lang.invoice')) }}</a>
                        @endif


                        <!-- estimate -->
                        @if(config('visibility.modules.estimates') && auth()->user()->role->role_estimates >= 2)
                        <a href="javascript:void(0)"
                            class="dropdown-item dropdown-item-iconed edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal"
                            data-url="{{ url('/estimates/create?ref=quickadd') }}" data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.add_estimate')) }}"
                            data-action-url="{{url('/estimates?ref=quickadd') }}" data-action-method="POST"
                            data-action-ajax-loading-target="commonModalBody" data-save-button-class=""
                            data-project-progress="0">
                            <i class="sl-icon-calculator"></i> {{ cleanLang(__('lang.estimate')) }}</a>
                        @endif

                        
                        <!-- proposal -->
                        @if(config('visibility.modules.proposals') && auth()->user()->role->role_proposals >= 2)
                        <a href="javascript:void(0)"
                            class="dropdown-item dropdown-item-iconed edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal"
                            data-url="{{ url('/proposals/create?ref=quickadd') }}" data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.add_proposal')) }}"
                            data-action-url="{{url('/proposals?ref=quickadd') }}" data-action-method="POST"
                            data-action-ajax-loading-target="commonModalBody" data-save-button-class=""
                            data-project-progress="0">
                            <i class="ti-bookmark-alt"></i> {{ cleanLang(__('lang.proposal')) }}</a>
                        @endif

                        <!-- payment -->
                        @if(config('visibility.modules.payments') && auth()->user()->role->role_invoices >= 2)
                        <a href="javascript:void(0)"
                            class="dropdown-item dropdown-item-iconed edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal"
                            data-url="{{ url('/payments/create?ref=quickadd') }}" data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.add_payment')) }}"
                            data-action-url="{{url('/payments?ref=quickadd') }}" data-action-method="POST"
                            data-action-ajax-loading-target="commonModalBody" data-save-button-class=""
                            data-project-progress="0">
                            <i class="ti-credit-card"></i> {{ cleanLang(__('lang.payment')) }}</a>
                        @endif
                        
                        <!-- subscription -->
                        @if(config('visibility.modules.subscriptions') && auth()->user()->role->role_subscriptions >= 2)
                        <a href="javascript:void(0)"
                            class="dropdown-item dropdown-item-iconed edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal"
                            data-url="{{ url('/subscriptions/create?ref=quickadd') }}"
                            data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.add_subscription')) }}"
                            data-action-url="{{url('/subscriptions?ref=quickadd') }}" data-action-method="POST"
                            data-action-ajax-loading-target="commonModalBody"
                            data-action-ajax-class="js-ajax-ux-request" data-project-progress="0">
                            <i class="sl-icon-layers"></i> {{ cleanLang(__('lang.subscription')) }}</a>
                        @endif

                        <!-- expense -->
                        @if(config('visibility.modules.expenses') && auth()->user()->role->role_expenses >= 2)
                        <a href="javascript:void(0)"
                            class="dropdown-item dropdown-item-iconed edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal"
                            data-url="{{ url('/expenses/create?ref=quickadd') }}" data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.add_expense')) }}"
                            data-action-url="{{url('/expenses?ref=quickadd') }}" data-action-method="POST"
                            data-action-ajax-loading-target="commonModalBody" data-save-button-class=""
                            data-project-progress="0">
                            <i class="ti-receipt"></i> {{ cleanLang(__('lang.expense')) }}</a>
                        @endif


                        <!-- knowledgebase article -->
                        @if(config('visibility.modules.knowledgebase') && auth()->user()->role->role_knowledgebase >= 2)
                        <a href="javascript:void(0)"
                            class="dropdown-item dropdown-item-iconed edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal" data-url="{{ url('kb/create') }}"
                            data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.add_article')) }}" data-action-url="{{ url('kb') }}"
                            data-action-method="POST" data-action-ajax-loading-target="commonModalBody"
                            data-save-button-class="">
                            <i class="sl-icon-docs"></i> {{ cleanLang(__('lang.article')) }}</a>
                        @endif

                    </div>
                </li>
                @endif


                <!-- language -->
                @if(config('system.settings_system_language_allow_users_to_change') == 'yes')
                <li class="nav-item dropdown" id="topnav-language-icon">
                    <a class="nav-link dropdown-toggle p-t-10 waves-effect waves-dark" href="javascript:void(0)"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="sl-icon-globe"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right animated bounceInDown language">
                        <div class="row">
                            @foreach(request('system_languages') as $key => $language)
                            <div class="col-6">
                                <a class="dropdown-item js-ajax-request text-capitalize" href="javascript:void(0)"
                                    data-url="{{ url('user/updatelanguage') }}" data-type="form" data-ajax-type="post"
                                    data-form-id="topNavLangauage{{ $key }}">{{ $language }}
                                </a>
                                <span id="topNavLangauage{{ $key }}">
                                    <input type="hidden" name="language" value="{{ $language }}">
                                    <input type="hidden" name="current_url" value="{{ url()->full() }}">
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </li>
                @endif
                <!--language -->


                <!-- profile -->
                <li class="nav-item dropdown u-pro">
                    <a class="nav-link dropdown-toggle p-l-20 p-r-20 waves-dark profile-pic" href="javascript:void(0)"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ auth()->user()->avatar }}" id="topnav_avatar" alt="user" class="" />
                        <span class="hidden-md-down" id="topnav_username">{{ auth()->user()->first_name }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right animated flipInY">
                        <ul class="dropdown-user">
                            <li>
                                <div class="dw-user-box">
                                    <div class="u-img"><img src="{{ auth()->user()->avatar }}"
                                            id="topnav_dropdown_avatar" alt="user"></div>
                                    <div class="u-text">
                                        <h4 id="topnav_dropdown_full_name">{{ auth()->user()->first_name }}
                                            {{ auth()->user()->last_name }}</h4>
                                        <p class="text-muted" id="topnav_dropdown_email">{{ auth()->user()->email }}</p>
                                        <a href="javascript:void(0)"
                                            class="btn btn-rounded btn-danger btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                                            data-toggle="modal" data-target="#commonModal"
                                            data-url="{{ url('/user/avatar') }}" data-loading-target="commonModalBody"
                                            data-modal-size="modal-sm"
                                            data-modal-title="{{ cleanLang(__('lang.update_avatar')) }}"
                                            data-header-visibility="hidden" data-header-extra-close-icon="visible"
                                            data-action-url="{{ url('/user/avatar') }}"
                                            data-action-method="PUT">{{ cleanLang(__('lang.update_avatar')) }}</a>
                                    </div>
                                </div>
                            </li>
                            <li role="separator" class="divider"></li>
                            <!--my profile-->
                            <li>
                                <a href="javascript:void(0)"
                                    class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                                    data-toggle="modal" data-target="#commonModal"
                                    data-url="{{ url('/contacts/'.auth()->id().'/edit?type=profile') }}"
                                    data-loading-target="commonModalBody"
                                    data-modal-title="{{ cleanLang(__('lang.update_my_profile')) }}"
                                    data-action-url="{{ url('/contacts/'.auth()->id()) }}" data-action-method="PUT"
                                    data-action-ajax-class="" data-modal-size="modal-lg"
                                    data-action-ajax-loading-target="team-td-container">
                                    <i class="ti-user p-r-4"></i>
                                    {{ cleanLang(__('lang.update_my_profile')) }}</a>
                            </li>

                            <!--my timesheets-->
                            @if(auth()->user()->is_team && auth()->user()->role->role_timesheets >= 1)
                            <li>
                                <a href="{{ url('/timesheets/my') }}">
                                    <i class="ti-timer p-r-4"></i>
                                    {{ cleanLang(__('lang.my_time_sheets')) }}</a>
                            </li>
                            @endif

                            @if(auth()->user()->is_client_owner)
                            <!--edit company profile-->
                            <li>
                                <a href="javascript:void(0)"
                                    class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                                    data-toggle="modal" data-target="#commonModal"
                                    data-url="{{ url('/clients/'.auth()->user()->clientid.'/edit') }}"
                                    data-loading-target="commonModalBody"
                                    data-modal-title="{{ cleanLang(__('lang.company_details')) }}"
                                    data-action-url="{{ url('/clients/'.auth()->user()->clientid) }}"
                                    data-action-method="PUT">
                                    <i class="ti-pencil-alt p-r-4"></i>
                                    {{ cleanLang(__('lang.company_details')) }}</a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"
                                    class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                                    data-toggle="modal" data-target="#commonModal" data-url="{{ url('/clients/logo') }}"
                                    data-loading-target="commonModalBody" data-modal-size="modal-sm"
                                    data-modal-title="{{ cleanLang(__('lang.update_avatar')) }}"
                                    data-header-visibility="hidden" data-header-extra-close-icon="visible"
                                    data-action-url="{{ url('/clients/logo') }}" data-action-method="PUT">
                                    <i class="ti-pencil-alt p-r-4"></i>
                                    {{ cleanLang(__('lang.company_logo')) }}</a>
                            </li>
                            @endif

                            <!--update notifcations-->
                            <li>
                                <a href="javascript:void(0)" id="topnavUpdateNotificationsButton"
                                    class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                                    data-toggle="modal" data-target="#commonModal"
                                    data-url="{{ url('user/updatenotifications') }}"
                                    data-loading-target="commonModalBody"
                                    data-modal-title="{{ cleanLang(__('lang.notification_settings')) }}"
                                    data-action-url="{{ url('user/updatenotifications') }}" data-action-method="PUT"
                                    data-modal-size="modal-lg" data-form-design="form-material"
                                    data-header-visibility="hidden" data-header-extra-close-icon="visible"
                                    data-action-ajax-class="js-ajax-ux-request"
                                    data-action-ajax-loading-target="commonModalBody">
                                    <i class="sl-icon-bell p-r-4"></i>
                                    {{ cleanLang(__('lang.notification_settings')) }}</a>
                            </li>

                            <!--update theme-->
                            <li>
                                <a href="javascript:void(0)" id="topnavUpdatePasswordButton"
                                    class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                                    data-toggle="modal" data-target="#commonModal"
                                    data-url="{{ url('user/updatetheme') }}" data-loading-target="commonModalBody"
                                    data-modal-title="{{ cleanLang(__('lang.change_theme')) }}"
                                    data-action-url="{{ url('user/updatetheme') }}" data-action-method="PUT"
                                    data-action-ajax-class="" data-modal-size="modal-sm"
                                    data-form-design="form-material" data-header-visibility="hidden"
                                    data-header-extra-close-icon="visible"
                                    data-action-ajax-loading-target="commonModalBody">
                                    <i class="ti-image p-r-4"></i>
                                    {{ cleanLang(__('lang.change_theme')) }}</a>
                            </li>

                            <!--update password-->
                            <li>
                                <a href="javascript:void(0)" id="topnavUpdatePasswordButton"
                                    class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                                    data-toggle="modal" data-target="#commonModal"
                                    data-url="{{ url('user/updatepassword') }}" data-loading-target="commonModalBody"
                                    data-modal-title="{{ cleanLang(__('lang.update_password')) }}"
                                    data-action-url="{{ url('user/updatepassword') }}" data-action-method="PUT"
                                    data-action-ajax-class="" data-modal-size="modal-sm"
                                    data-form-design="form-material" data-header-visibility="hidden"
                                    data-header-extra-close-icon="visible"
                                    data-action-ajax-loading-target="commonModalBody">
                                    <i class="ti-lock p-r-4"></i>
                                    {{ cleanLang(__('lang.update_password')) }}</a>
                            </li>

                            <li role="separator" class="divider"></li>
                            <li>
                                <a href="/logout">
                                    <i class="fa fa-power-off p-r-4"></i> {{ cleanLang(__('lang.logout')) }}</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- /#profile -->
            </ul>
        </div>
    </nav>


</header>