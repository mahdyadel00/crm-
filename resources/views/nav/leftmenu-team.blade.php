<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar" id="js-trigger-nav-team">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar" id="main-scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav" id="main-sidenav">
            <ul id="sidebarnav"  data-modular-id="main_menu_team">



                <!--home-->
                <li data-modular-id="main_menu_team_home"
                    class="sidenav-menu-item {{ $page['mainmenu_home'] ?? '' }} menu-tooltip menu-with-tooltip"
                    title="{{ cleanLang(__('lang.home')) }}">
                    <a class="waves-effect waves-dark" href="/home" aria-expanded="false" target="_self">
                        <i class="ti-home"></i>
                        <span class="hide-menu">{{ cleanLang(__('lang.dashboard')) }}
                        </span>
                    </a>
                </li>
                <!--home-->


                <!--users[done]-->
                @if(runtimeGroupMenuVibility([config('visibility.modules.clients'),
                config('visibility.modules.users')]))
                <li data-modular-id="main_menu_team_clients"
                    class="sidenav-menu-item {{ $page['mainmenu_customers'] ?? '' }}">
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0);" aria-expanded="false">
                        <i class="sl-icon-people"></i>
                        <span class="hide-menu">{{ cleanLang(__('lang.customers')) }}
                        </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        @if(config('visibility.modules.clients'))
                        <li class="sidenav-submenu {{ $page['submenu_customers'] ?? '' }}" id="submenu_clients">
                            <a href="/clients"
                                class="{{ $page['submenu_customers'] ?? '' }}">{{ cleanLang(__('lang.clients')) }}</a>
                        </li>
                        @endif
                        @if(config('visibility.modules.users'))
                        <li class="sidenav-submenu {{ $page['submenu_contacts'] ?? '' }}" id="submenu_contacts">
                            <a href="/users"
                                class="{{ $page['submenu_contacts'] ?? '' }}">{{ cleanLang(__('lang.client_users')) }}</a>
                        </li>
                        @endif
                        <!--contracts [hidden]-->
                        @if(config('visibility.modules.contracts.foo'))
                        <li class="sidenav-submenu {{ $page['submenu_contracts'] ?? '' }}" id="submenu_contracts">
                            <a href="/contracts"
                                class="{{ $page['submenu_contracts'] ?? '' }}">{{ cleanLang(__('lang.contracts')) }}</a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                <!--customers-->

                <!--projects[done]-->
                @if(config('visibility.modules.projects'))
                <li data-modular-id="main_menu_team_projects" class="sidenav-menu-item {{ $page['mainmenu_projects'] ?? '' }}">
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0);" aria-expanded="false">
                        <i class="ti-folder"></i>
                        <span class="hide-menu">{{ cleanLang(__('lang.projects')) }}
                        </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        @if(config('system.settings_projects_categories_main_menu') == 'yes')
                        @foreach(config('projects_categories') as $category)
                        <li class="sidenav-submenu" id="submenu_projects">
                            <a href="{{ _url('/projects?filter_category='.$category->category_id) }}"
                                class="{{ $page['submenu_projects_category_'.$category->category_id] ?? '' }}">{{ $category->category_name }}</a>
                        </li>
                        @endforeach
                        @else
                        <li class="sidenav-submenu {{ $page['submenu_projects'] ?? '' }}" id="submenu_projects">
                            <a href="{{ _url('/projects') }}"
                                class="{{ $page['submenu_projects'] ?? '' }}">{{ cleanLang(__('lang.projects')) }}</a>
                        </li>
                        @endif
                        <li class="sidenav-submenu {{ $page['submenu_templates'] ?? '' }}"
                            id="submenu_project_templates">
                            <a href="{{ _url('/templates/projects') }}"
                                class="{{ $page['submenu_templates'] ?? '' }}">{{ cleanLang(__('lang.templates')) }}</a>
                        </li>
                    </ul>
                </li>
                @endif
                <!--projects-->


                <!--tasks[done]-->
                @if(config('visibility.modules.tasks'))
                <li data-modular-id="main_menu_team_tasks" class="sidenav-menu-item {{ $page['mainmenu_tasks'] ?? '' }} menu-tooltip menu-with-tooltip"
                    title="{{ cleanLang(__('lang.tasks')) }}">
                    <a class="waves-effect waves-dark" href="/tasks" aria-expanded="false" target="_self">
                        <i class="ti-menu-alt"></i>
                        <span class="hide-menu">{{ cleanLang(__('lang.tasks')) }}
                        </span>
                    </a>
                </li>
                @endif
                <!--tasks-->

                <!--leads[done]-->
                @if(config('visibility.modules.leads'))
                <li data-modular-id="main_menu_team_leads" class="sidenav-menu-item {{ $page['mainmenu_leads'] ?? '' }} menu-tooltip menu-with-tooltip"
                    title="{{ cleanLang(__('lang.leads')) }}">
                    <a class="waves-effect waves-dark" href="/leads" aria-expanded="false" target="_self">
                        <i class="sl-icon-call-in"></i>
                        <span class="hide-menu">{{ cleanLang(__('lang.leads')) }}
                        </span>
                    </a>
                </li>
                @endif
                <!--leads-->

                <!--sales-->
                @if(runtimeGroupMenuVibility([config('visibility.modules.invoices'),
                config('visibility.modules.payments'), config('visibility.modules.estimates'),
                config('visibility.modules.products'), config('visibility.modules.expenses')]))
                <li data-modular-id="main_menu_team_billing" class="sidenav-menu-item {{ $page['mainmenu_sales'] ?? '' }}">
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0);" aria-expanded="false">
                        <i class="ti-wallet"></i>
                        <span class="hide-menu">{{ cleanLang(__('lang.sales')) }}
                        </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        @if(config('visibility.modules.invoices'))
                        <li class="sidenav-submenu {{ $page['submenu_invoices'] ?? '' }}" id="submenu_invoices">
                            <a href="/invoices"
                                class=" {{ $page['submenu_invoices'] ?? '' }}">{{ cleanLang(__('lang.invoices')) }}</a>
                        </li>
                        @endif
                        @if(config('visibility.modules.payments'))
                        <li class="sidenav-submenu {{ $page['submenu_payments'] ?? '' }}" id="submenu_payments">
                            <a href="/payments"
                                class=" {{ $page['submenu_payments'] ?? '' }}">{{ cleanLang(__('lang.payments')) }}</a>
                        </li>
                        @endif
                        @if(config('visibility.modules.estimates'))
                        <li class="sidenav-submenu {{ $page['submenu_estimates'] ?? '' }}" id="submenu_estimates">
                            <a href="/estimates"
                                class=" {{ $page['submenu_estimates'] ?? '' }}">{{ cleanLang(__('lang.estimates')) }}</a>
                        </li>
                        @endif
                        @if(config('visibility.modules.products'))
                        <li class="sidenav-submenu {{ $page['submenu_products'] ?? '' }}" id="submenu_products">
                            <a href="/products"
                                class=" {{ $page['submenu_products'] ?? '' }}">{{ cleanLang(__('lang.products')) }}</a>
                        </li>
                        @endif
                        @if(config('visibility.modules.expenses'))
                        <li class="sidenav-submenu {{ $page['submenu_expenses'] ?? '' }}" id="submenu_expenses">
                            <a href="/expenses"
                                class=" {{ $page['submenu_expenses'] ?? '' }}">{{ cleanLang(__('lang.expenses')) }}</a>
                        </li>
                        @endif
                        @if(config('visibility.modules.proposals'))
                        <li class="sidenav-submenu {{ $page['submenu_proposals'] ?? '' }}" id="submenu_proposals">
                            <a href="/proposals"
                                class=" {{ $page['submenu_proposals'] ?? '' }}">{{ cleanLang(__('lang.proposals')) }}</a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                <!--billing-->


                <!--[MODULES] - dynamic menu-->
                {!! config('module_menus.main_menu_team') !!}

                <!--subscriptions-->
                @if(config('visibility.modules.subscriptions'))
                <li data-modular-id="main_menu_team_subscriptions" class="sidenav-menu-item {{ $page['mainmenu_subscription'] ?? '' }} menu-tooltip menu-with-tooltip"
                    title="{{ cleanLang(__('lang.subscriptions')) }}">
                    <a class="waves-effect waves-dark p-r-20" href="/subscriptions" aria-expanded="false"
                        target="_self">
                        <i class="sl-icon-layers"></i>
                        <span class="hide-menu">{{ cleanLang(__('lang.subscriptions')) }}
                        </span>
                    </a>
                </li>
                @endif


                <!--tickets-->
                @if(config('visibility.modules.tickets'))
                <li class="sidenav-menu-item {{ $page['mainmenu_tickets'] ?? '' }} menu-tooltip menu-with-tooltip"
                    title="{{ cleanLang(__('lang.tickets')) }}">
                    <a class="waves-effect waves-dark" href="/tickets" aria-expanded="false" target="_self">
                        <i class="ti-comments"></i>
                        <span class="hide-menu">{{ cleanLang(__('lang.support')) }}
                        </span>
                    </a>
                </li>
                @endif
                <!--tickets-->


                <!--knowledgebase-->
                @if(config('visibility.modules.knowledgebase'))
                <li data-modular-id="main_menu_team_knowledgebase" class="sidenav-menu-item {{ $page['mainmenu_kb'] ?? '' }} menu-tooltip menu-with-tooltip"
                    title="{{ cleanLang(__('lang.knowledgebase')) }}">
                    <a class="waves-effect waves-dark p-r-20" href="/knowledgebase" aria-expanded="false"
                        target="_self">
                        <i class="sl-icon-docs"></i>
                        <span class="hide-menu">{{ cleanLang(__('lang.knowledgebase')) }}
                        </span>
                    </a>
                </li>
                @endif
                <!--knowledgebase-->

                <!--team-->
                @if(auth()->user()->is_admin)
                <li data-modular-id="main_menu_team_team" class="sidenav-menu-item {{ $page['mainmenu_settings'] ?? '' }}">
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0);" aria-expanded="false">
                        <i class="ti-panel"></i>
                        <span class="hide-menu">{{ cleanLang(__('lang.other')) }}
                        </span>
                    </a>
                    <ul aria-expanded="false" class="position-top collapse">
                        @if(config('visibility.modules.team'))
                        <li class="sidenav-submenu mainmenu_team {{ $page['submenu_team'] ?? '' }}" id="submenu_team">
                            <a href="/team"
                                class="{{ $page['submenu_team'] ?? '' }}">{{ cleanLang(__('lang.team_members')) }}</a>
                        </li>
                        @endif
                        @if(config('visibility.modules.timesheets'))
                        <li class="sidenav-submenu mainmenu_timesheets {{ $page['submenu_timesheets'] ?? '' }}"
                            id="submenu_timesheets">
                            <a href="/timesheets"
                                class="{{ $page['submenu_timesheets'] ?? '' }}">{{ cleanLang(__('lang.time_sheets')) }}</a>
                        </li>
                        @endif
                        @if(auth()->user()->is_admin)
                        <li class="sidenav-submenu mainmenu_settings {{ $page['submenu_settings'] ?? '' }}"
                            id="submenu_settings">
                            <a href="/settings"
                                class="{{ $page['submenu_settings'] ?? '' }}">{{ cleanLang(__('lang.settings')) }}</a>
                        </li>
                        @endif
                    </ul>
                </li>
                @else
                @if(runtimeGroupMenuVibility([config('visibility.modules.team'),
                config('visibility.modules.timesheets')]))
                <li data-modular-id="main_menu_team_other" class="sidenav-menu-item {{ $page['mainmenu_settings'] ?? '' }}">
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0);" aria-expanded="false">
                        <i class="ti-panel"></i>
                        <span class="hide-menu">{{ cleanLang(__('lang.other')) }}
                        </span>
                    </a>
                    <ul aria-expanded="false" class="position-top collapse">
                        @if(config('visibility.modules.team'))
                        <li class="sidenav-submenu mainmenu_team {{ $page['submenu_team'] ?? '' }}" id="submenu_team">
                            <a href="/team"
                                class="{{ $page['submenu_team'] ?? '' }}">{{ cleanLang(__('lang.team_members')) }}</a>
                        </li>
                        @endif
                        @if(config('visibility.modules.timesheets'))
                        <li class="sidenav-submenu mainmenu_timesheets {{ $page['submenu_timesheets'] ?? '' }}"
                            id="submenu_timesheets">
                            <a href="/timesheets"
                                class="{{ $page['submenu_timesheets'] ?? '' }}">{{ cleanLang(__('lang.time_sheets')) }}</a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @endif
                <!--team-->
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>