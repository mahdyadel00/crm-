<div class="row">
    <div class="col-lg-12">
        <!-- Nav tabs -->
        <ul data-modular-id="project_tabs_menu" class="nav nav-tabs profile-tab project-top-nav list-pages-crumbs"
            role="tablist">
            <!--overview-->
            <li class="nav-item">
                <a class="nav-link tabs-menu-item" href="/projects/{{ $project->project_id }}" role="tab"
                    id="tabs-menu-overview">{{ cleanLang(__('lang.overview')) }}</a>
            </li>
            <!--details-->
            <li class="nav-item">
                <a class="nav-link tabs-menu-item   js-dynamic-url js-ajax-ux-request" data-toggle="tab"
                    id="tabs-menu-details" data-loading-class="loading-tabs"
                    data-loading-target="embed-content-container"
                    data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/details"
                    data-url="{{ _url('/projects') }}/{{ $project->project_id }}/project-details"
                    href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.details')) }}</a>
            </li>
            <!--[tasks]-->
            @if(config('settings.project_permissions_view_tasks'))
            <li class="nav-item">
                <a class="nav-link tabs-menu-item   js-dynamic-url js-ajax-ux-request" data-toggle="tab"
                    id="tabs-menu-tasks" data-loading-class="loading-tabs" data-loading-target="embed-content-container"
                    data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/tasks"
                    data-url="{{ url('/tasks') }}?source=ext&taskresource_type=project&taskresource_id={{ $project->project_id }}"
                    href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.tasks')) }}</a>
            </li>
            @endif
            <!--[milestones]-->
            @if(config('settings.project_permissions_view_milestones'))
            <li class="nav-item">
                <a class="nav-link  tabs-menu-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_milestones'] ?? '' }}"
                    data-toggle="tab" id="tabs-menu-milestones" data-loading-class="loading-tabs"
                    data-loading-target="embed-content-container"
                    data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/milestones"
                    data-url="{{ url('/milestones') }}?source=ext&milestoneresource_type=project&milestoneresource_id={{ $project->project_id }}"
                    href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.milestones')) }}</a>
            </li>
            @endif

            <!--[files]-->
            @if(config('settings.project_permissions_view_files'))
            <li class="nav-item">
                <a class="nav-link  tabs-menu-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_files'] ?? '' }}"
                    data-toggle="tab" id="tabs-menu-files" data-loading-class="loading-tabs"
                    data-loading-target="embed-content-container"
                    data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/files"
                    data-url="{{ url('/files') }}?source=ext&fileresource_type=project&fileresource_id={{ $project->project_id }}"
                    href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.files')) }}</a>
            </li>
            @endif
            <!--[comments]-->
            @if(config('settings.project_permissions_view_comments'))
            <li class="nav-item ">
                <a class="nav-link  tabs-menu-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_discussions'] ?? '' }}"
                    id="tabs-menu-comments" data-toggle="tab" data-loading-class="loading-tabs"
                    data-loading-target="embed-content-container"
                    data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/comments"
                    data-url="{{ url('/comments') }}?source=ext&commentresource_type=project&commentresource_id={{ $project->project_id }}"
                    href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.comments')) }}</a>
            </li>
            @endif
            <!--billing-->
            @if(auth()->user()->is_team || auth()->user()->is_client_owner)
            <li data-modular-id="project_tabs_menu_financial"
                class="nav-item dropdown {{ $page['tabmenu_more'] ?? '' }}">
                <a class="nav-link dropdown-toggle  tabs-menu-item" data-loading-class="loading-tabs"
                    data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true"
                    id="tabs-menu-billing" aria-expanded="false">
                    <span class="hidden-xs-down">{{ cleanLang(__('lang.financial')) }}</span>
                </a>
                <div class="dropdown-menu" x-placement="bottom-start" id="fx-topnav-dropdown">
                    <!--[invoices]-->
                    @if(config('settings.project_permissions_view_invoices'))
                    <a class="dropdown-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_invoices'] ?? '' }}"
                        data-toggle="tab" data-loading-class="loading-tabs"
                        data-loading-target="embed-content-container"
                        data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/invoices"
                        data-url="{{ url('/invoices') }}?source=ext&invoiceresource_id={{ $project->project_id }}&invoiceresource_type=project"
                        href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.invoices')) }}</a>
                    @endif
                    <!--[estimate]-->
                    @if(auth()->user()->role->role_estimates >= 1)
                    <a class="dropdown-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_estimates'] ?? '' }}"
                        data-toggle="tab" data-loading-class="loading-tabs"
                        data-loading-target="embed-content-container"
                        data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/estimates"
                        data-url="{{ url('/estimates') }}?source=ext&estimateresource_id={{ $project->project_id }}&estimateresource_type=project"
                        href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.estimates')) }}</a>
                    @endif
                    <!--[payments]-->
                    @if(config('settings.project_permissions_view_payments'))
                    <a class="dropdown-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_invoices'] ?? '' }}"
                        data-toggle="tab" data-loading-class="loading-tabs"
                        data-loading-target="embed-content-container"
                        data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/payments"
                        data-url="{{ url('/payments') }}?source=ext&paymentresource_id={{ $project->project_id }}&paymentresource_type=project"
                        href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.payments')) }}</a>
                    @endif
                    <!--[expenses]-->
                    @if(config('settings.project_permissions_view_expenses'))
                    <a class="dropdown-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_invoices'] ?? '' }}"
                        data-toggle="tab" data-loading-class="loading-tabs"
                        data-loading-target="embed-content-container"
                        data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/expenses"
                        data-url="{{ url('/expenses') }}?source=ext&expenseresource_id={{ $project->project_id }}&expenseresource_type=project"
                        href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.expenses')) }}</a>
                    @endif
                    <!--[timesheets]-->
                    @if(config('settings.project_permissions_view_timesheets'))
                    <a class="dropdown-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_timesheets'] ?? '' }}"
                        data-toggle="tab" data-loading-class="loading-tabs"
                        data-loading-target="embed-content-container"
                        data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/timesheets"
                        data-url="{{ url('/timesheets') }}?source=ext&timesheetresource_id={{ $project->project_id }}&timesheetresource_type=project"
                        href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.timesheets')) }}</a>
                    @endif
                </div>
            </li>
            @endif

            <!--[MODULES] - dynamic menu-->
            {!! config('module_menus.project_tabs_menu') !!}

            <!--[MODULES]-->
            <li data-modular-id="project_tabs_menu_more" class="nav-item dropdown {{ $page['tabmenu_more'] ?? '' }}">
                <a class="nav-link dropdown-toggle  tabs-menu-item" data-loading-class="loading-tabs"
                    data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true"
                    id="tabs-menu-billing" aria-expanded="false">
                    <span class="hidden-xs-down">{{ cleanLang(__('lang.more')) }}</span>
                </a>
                <div class="dropdown-menu" x-placement="bottom-start" id="fx-topnav-dropdown">

                    <!--[MODULES-->


                    <!--tickets-->
                    @if(config('settings.project_permissions_view_tickets'))
                    <a class="dropdown-item tabs-menu-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_tickets'] ?? '' }}"
                        id="tabs-menu-tickets" data-toggle="tab" data-loading-class="loading-tabs"
                        data-loading-target="embed-content-container"
                        data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/tickets"
                        data-url="{{ url('/tickets') }}?source=ext&ticketresource_type=project&ticketresource_id={{ $project->project_id }}"
                        href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.tickets')) }}</a>
                    @endif

                    <!--notes-->
                    @if(config('settings.project_permissions_view_notes'))
                    <a class="dropdown-item js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_notes'] ?? '' }}"
                        id="tabs-menu-notes" data-toggle="tab" data-loading-class="loading-tabs"
                        data-loading-target="embed-content-container"
                        data-dynamic-url="{{ _url('/projects') }}/{{ $project->project_id }}/notes"
                        data-url="{{ url('/notes') }}?source=ext&noteresource_type=project&noteresource_id={{ $project->project_id }}"
                        href="#projects_ajaxtab" role="tab">{{ cleanLang(__('lang.notes')) }}</a>
                    @endif

                </div>
            </li>
        </ul>
        <!-- Tab panes -->

        @include('pages.files.components.actions.checkbox-actions')

    </div>
</div>