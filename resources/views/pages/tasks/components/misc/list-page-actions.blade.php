<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-7 p-b-9 align-self-center text-right {{ $page['list_page_actions_size'] ?? '' }} {{ $page['list_page_container_class'] ?? '' }}"
    id="list-page-actions-container">
    <div id="list-page-actions">
        <!--SEARCH BOX-->
        @if( config('visibility.list_page_actions_search'))
        <div class="header-search" id="header-search">
            <i class="sl-icon-magnifier"></i>
            <input type="text" class="form-control search-records list-actions-search"
                data-url="{{ $page['dynamic_search_url'] ?? '' }}" data-type="form" data-ajax-type="post"
                data-form-id="header-search" id="search_query" name="search_query"
                placeholder="{{ cleanLang(__('lang.search')) }}">
        </div>
        @endif

        <!--ARCHIVED TASKS-->
        @if(config('visibility.archived_tasks_toggle_button'))
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.show_archive_tasks')) }}"
            id="pref_filter_show_archived_tasks"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark js-ajax-ux-request {{ runtimeActive(auth()->user()->pref_filter_show_archived_tasks) }}"
            data-url="{{ urlResource('/tasks/search?action=search&toggle=pref_filter_show_archived_tasks') }}">
            <i class="ti-archive"></i>
        </button>
        @endif

        <!--SHOW OWN TASKS-->
        @if(config('visibility.own_tasks_toggle_button'))
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.my_tasks')) }}"
            id="pref_filter_own_tasks"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark js-ajax-ux-request {{ runtimeActive(auth()->user()->pref_filter_own_tasks) }}"
            data-url="{{ urlResource('/tasks/search?action=search&toggle=pref_filter_own_tasks') }}">
            <i class="sl-icon-user"></i>
        </button>
        @endif

        <!--TOGGLE STATS-->
        @if(config('visibility.stats_toggle_button'))
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.quick_stats')) }}"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark js-toggle-stats-widget update-user-ux-preferences"
            data-type="statspanel" data-progress-bar="hidden"
            data-url-temp="{{ urlResource('/') }}/{{ auth()->user()->team_or_contact }}/updatepreferences" data-url=""
            data-target="list-pages-stats-widget">
            <i class="ti-stats-up"></i>
        </button>
        @endif


        <!--TASKS - KANBAN VIEW & SORTING-->
        @if(config('visibility.tasks_kanban_actions'))
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.kanban_view')) }}"
            id="pref_view_tasks_layout"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark js-ajax-ux-request {{ runtimeActive(auth()->user()->pref_view_tasks_layout) }}"
            data-url="{{ urlResource('/tasks/search?action=search&toggle=layout') }}">
            <i class="sl-icon-list"></i>
        </button>
        <!--kanban task sorting-->
        <div class="btn-group" id="list_actions_sort_kanban">
            <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="list-actions-button btn waves-effect waves-dark dropdown-toggle">
                <i class="mdi mdi-sort"></i></button>
            <div class="dropdown-menu dropdown-menu-right fx-kaban-sorting-dropdown">
                <div class="fx-kaban-sorting-dropdown-container">{{ cleanLang(__('lang.sort_by')) }}</div>
                <a class="dropdown-item js-ajax-ux-request" id="sort_kanban_task_created" href="javascript:void(0)"
                    data-url="{{ urlResource('/tasks?action=sort&orderby=task_created&sortorder=asc') }}">{{ cleanLang(__('lang.date_created')) }}</a>
                <a class="dropdown-item js-ajax-ux-request" id="sort_kanban_task_date_start" href="javascript:void(0)"
                    data-url="{{ urlResource('/tasks?action=sort&orderby=task_date_start&sortorder=asc') }}">{{ cleanLang(__('lang.start_date')) }}</a>
                <a class="dropdown-item js-ajax-ux-request" id="sort_kanban_task_date_due" href="javascript:void(0)"
                    data-url="{{ urlResource('/tasks?action=sort&orderby=task_date_due&sortorder=asc') }}">{{ cleanLang(__('lang.due_date')) }}</a>
                <a class="dropdown-item js-ajax-ux-request" id="sort_kanban_task_title" href="javascript:void(0)"
                    data-url="{{ urlResource('/tasks?action=sort&orderby=task_title&sortorder=asc') }}">{{ cleanLang(__('lang.title')) }}</a>
            </div>
        </div>
        @endif


        <!--FILTERING-->
        @if(config('visibility.list_page_actions_filter_button'))
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.filter')) }}"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark js-toggle-side-panel"
            data-target="{{ $page['sidepanel_id'] ?? '' }}">
            <i class="mdi mdi-filter-outline"></i>
        </button>
        @endif


        <!--ADD NEW ITEM-->
        @if(config('visibility.list_page_actions_add_button'))
        <button type="button"
            class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form {{ $page['add_button_classes'] ?? '' }}"
            data-toggle="modal" data-target="#commonModal" data-url="{{ $page['add_modal_create_url'] ?? '' }}"
            data-loading-target="commonModalBody" data-modal-title="{{ $page['add_modal_title'] ?? '' }}"
            data-action-url="{{ $page['add_modal_action_url'] ?? '' }}"
            data-action-method="{{ $page['add_modal_action_method'] ?? '' }}"
            data-action-ajax-class="{{ $page['add_modal_action_ajax_class'] ?? '' }}"
            data-modal-size="{{ $page['add_modal_size'] ?? '' }}"
            data-action-ajax-loading-target="{{ $page['add_modal_action_ajax_loading_target'] ?? '' }}"
            data-save-button-class="{{ $page['add_modal_save_button_class'] ?? '' }}" data-project-progress="0">
            <i class="ti-plus"></i>
        </button>
        @endif

        <!--add new button (link)-->
        @if( config('visibility.list_page_actions_add_button_link'))
        <a id="fx-page-actions-add-button" type="button" class="btn btn-danger btn-add-circle edit-add-modal-button"
            href="{{ $page['add_button_link_url'] ?? '' }}">
            <i class="ti-plus"></i>
        </a>
        @endif
    </div>
</div>