@if(config('visibility.settings_customfileds_tabs_menu'))
<ul class="nav nav-tabs customtab" role="tablist" id="settings-custom-fields-menu">
    <!--simple text-->
    <li class="nav-item">
        <a class="nav-link ajax-request {{ runtimeCustomsFieldsTabs(request('filter_field_type'), 'text') }}"
            id="custom-fields-tab-text" data-toggle="tab" href="javascript:void(0);" role="tab"
            data-url="{{ url('settings/customfields/'.$payload['tab_menu_type'].'?filter_field_type=text') }}"
            data-loading-target="embed-content-container">
            <span class="hidden-xs-down">@lang('lang.field_simple_text')</span>
        </a>
    </li>
    <!--paragraph-->
    <li class="nav-item">
        <a class="nav-link ajax-request {{ runtimeCustomsFieldsTabs(request('filter_field_type'), 'paragraph') }}"
            id="custom-fields-tab-paragraph" data-toggle="tab" href="javascript:void(0);" role="tab"
            data-url="{{ url('settings/customfields/'.$payload['tab_menu_type'].'?filter_field_type=paragraph') }}"
            data-loading-target="embed-content-container">
            <span class="hidden-xs-down">@lang('lang.field_paragraph')</span>
        </a>
    </li>
    <!--date-->
    <li class="nav-item">
        <a class="nav-link ajax-request {{ runtimeCustomsFieldsTabs(request('filter_field_type'), 'date') }}"
            id="custom-fields-tab-date" data-toggle="tab" href="javascript:void(0);" role="tab"
            data-url="{{ url('settings/customfields/'.$payload['tab_menu_type'].'?filter_field_type=date') }}"
            data-loading-target="embed-content-container">
            <span class="hidden-xs-down">@lang('lang.field_date')</span>
        </a>
    </li>
    <!--checkbox-->
    <li class="nav-item">
        <a class="nav-link ajax-request {{ runtimeCustomsFieldsTabs(request('filter_field_type'), 'checkbox') }}"
            id="custom-fields-tab-checkbox" data-toggle="tab" href="javascript:void(0);" role="tab"
            data-url="{{ url('settings/customfields/'.$payload['tab_menu_type'].'?filter_field_type=checkbox') }}"
            data-loading-target="embed-content-container">
            <span class="hidden-xs-down">@lang('lang.field_checkbox')</span>
        </a>
    </li>
    <!--dropdown-->
    <li class="nav-item">
        <a class="nav-link ajax-request {{ runtimeCustomsFieldsTabs(request('filter_field_type'), 'dropdown') }}"
            id="custom-fields-tab-dropdown" data-toggle="tab" href="javascript:void(0);" role="tab"
            data-url="{{ url('settings/customfields/'.$payload['tab_menu_type'].'?filter_field_type=dropdown') }}"
            data-loading-target="embed-content-container">
            <span class="hidden-xs-down">@lang('lang.field_dropdown')</span>
        </a>
    </li>
    <!--number-->
    <li class="nav-item">
        <a class="nav-link ajax-request {{ runtimeCustomsFieldsTabs(request('filter_field_type'), 'number') }}"
            id="custom-fields-tab-number" data-toggle="tab" href="javascript:void(0);" role="tab"
            data-url="{{ url('settings/customfields/'.$payload['tab_menu_type'].'?filter_field_type=number') }}"
            data-loading-target="embed-content-container">
            <span class="hidden-xs-down">@lang('lang.field_number')</span>
        </a>
    </li>
    <!--decimal-->
    <li class="nav-item">
        <a class="nav-link ajax-request {{ runtimeCustomsFieldsTabs(request('filter_field_type'), 'decimal') }}"
            id="custom-fields-tab-decimal" data-toggle="tab" href="javascript:void(0);" role="tab"
            data-url="{{ url('settings/customfields/'.$payload['tab_menu_type'].'?filter_field_type=decimal') }}"
            data-loading-target="embed-content-container">
            <span class="hidden-xs-down">@lang('lang.field_decimal')</span>
        </a>
    </li>
    <!--standard form-->
    <li class="nav-item">
        <a class="nav-link ajax-request {{ runtimeCustomsFieldsTabs(request('tab_menu'), 'standard_form') }}"
            id="custom-fields-tab-decimal" data-toggle="tab" href="javascript:void(0);" role="tab"
            data-url="{{ url('settings/customfields/standard-form?tab_menu_type='.$payload['tab_menu_type'].'&tab_menu=standard_form') }}"
            data-loading-target="embed-content-container">
            <span class="hidden-xs-down">@lang('lang.standard_form')</span>
        </a>
    </li>
</ul>
@endif