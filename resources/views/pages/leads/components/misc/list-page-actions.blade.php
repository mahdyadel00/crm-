<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-7 p-b-9 align-self-center text-right {{ $page['list_page_actions_size'] ?? '' }} {{ $page['list_page_container_class'] ?? '' }}"
    id="list-page-actions-container">
    <div id="list-page-actions">
        <!--SEARCH BOX-->
        <div class="header-search" id="header-search">
            <i class="sl-icon-magnifier"></i>
            <input type="text" class="form-control search-records list-actions-search"
                data-url="{{ $page['dynamic_search_url'] ?? '' }}" data-type="form" data-ajax-type="post"
                data-form-id="header-search" id="search_query" name="search_query"
                placeholder="{{ cleanLang(__('lang.search')) }}">
        </div>

        <!--SHOW ARCHIVED LEADS-->
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.show_archive_leads')) }}"
            id="pref_filter_show_archived_leads"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark js-ajax-ux-request {{ runtimeActive(auth()->user()->pref_filter_show_archived_leads) }}"
            data-url="{{ url('/leads/search?action=search&toggle=pref_filter_show_archived_leads') }}">
            <i class="ti-archive"></i>
        </button>

        <!--SHOW OWN LEADS-->
        @if( config('visibility.own_leads_toggle_button'))
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.my_leads')) }}"
            id="pref_filter_own_leads"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark js-ajax-ux-request {{ runtimeActive(auth()->user()->pref_filter_own_leads) }}"
            data-url="{{ url('/leads/search?action=search&toggle=pref_filter_own_leads') }}">
            <i class="sl-icon-user"></i>
        </button>
        @endif


        <!--LEADS - KANBAN VIEW & SORTING-->
        <!--leads kanban toggle-->
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.kanban_view')) }}"
            id="pref_view_leads_layout"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark js-ajax-ux-request {{ runtimeActive(auth()->user()->pref_view_leads_layout) }}"
            data-url="{{ urlResource('/leads/search?action=search&toggle=layout') }}">
            <i class="sl-icon-list"></i>
        </button>
        <!--leads kanban task sorting-->
        <div class="btn-group" id="list_actions_sort_kanban">
            <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="list-actions-button btn waves-effect waves-dark dropdown-toggle">
                <i class="mdi mdi-sort"></i></button>
            <div class="dropdown-menu dropdown-menu-right fx-kaban-sorting-dropdown">
                <div class="fx-kaban-sorting-dropdown-container">{{ cleanLang(__('lang.sort_by')) }}</div>
                <a class="dropdown-item js-ajax-ux-request" id="sort_kanban_lead_created" href="javascript:void(0)"
                    data-url="{{ urlResource('/leads?action=sort&orderby=lead_created&sortorder=asc') }}">{{ cleanLang(__('lang.date_created')) }}</a>
                <a class="dropdown-item js-ajax-ux-request" id="sort_kanban_lead_firstname" href="javascript:void(0)"
                    data-url="{{ urlResource('/leads?action=sort&orderby=lead_firstname&sortorder=asc') }}">{{ cleanLang(__('lang.name')) }}</a>
                <a class="dropdown-item js-ajax-ux-request" id="sort_kanban_lead_value" href="javascript:void(0)"
                    data-url="{{ urlResource('/leads?action=sort&orderby=lead_value&sortorder=desc') }}">{{ cleanLang(__('lang.value')) }}</a>
                <a class="dropdown-item js-ajax-ux-request" id="sort_kanban_lead_last_contacted" href="javascript:void(0)"
                    data-url="{{ urlResource('/leads?action=sort&orderby=lead_last_contacted&sortorder=desc') }}">{{ cleanLang(__('lang.date_last_contacted')) }}</a>
            </div>
        </div>

        <!--IMPORTING-->
        @if(config('visibility.list_page_actions_importing'))
        <button type="button" title="{{ cleanLang(__('lang.import_leads')) }}" id="leads-import-button"
            class="p-t-5 data-toggle-tooltip list-actions-button btn btn-page-actions waves-effect waves-dark edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
            data-toggle="modal" data-target="#commonModal" data-footer-visibility="hidden" data-top-padding="none"
            data-action-url="{{ url('import/leads') }}" data-action-method="POST" data-loading-target="commonModalBody"
            data-action-ajax-loading-target="commonModalBody" data-modal-title="@lang('lang.import_leads')"
            data-url="{{ url('import/leads/create') }}">
            <i class="growicon-0191-folder-download"></i>
        </button>
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