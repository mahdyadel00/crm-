<ul class="nav nav-tabs customtab" role="tablist">
    <!--form builder-->
    <li class="nav-item"> <a class="nav-link {{ $page['menutab_builder'] ?? ''}}"
            href="{{ url('/app/settings/formbuilder/'.$webform->webform_id.'/build') }}" role="tab"><span
                class="hidden-sm-up"><i class="ti-home"></i></span> <span
                class="hidden-xs-down">@lang('lang.form_builder')</span></a> </li>
    <!--form settings-->
    <li class="nav-item"> <a class="nav-link cursor-pointer {{ $page['menutab_settings'] ?? '' }}"
            href="{{ url('/app/settings/formbuilder/'.$webform->webform_id.'/settings') }}" role="tab"><span
                class="hidden-sm-up ajax-request" href="javascript:void(0);"><i class="ti-user"></i></span> <span
                class="hidden-xs-down">@lang('lang.form_settings')</span></a>
    </li>
    <!--embed code-->
    <li class="nav-item"> <a class="nav-link cursor-pointer {{ $page['menutab_embed'] ?? '' }}"
            href="{{ url('/app/settings/formbuilder/'.$webform->webform_id.'/integrate') }}" role="tab"><span
                class="hidden-sm-up"><i class="ti-email"></i></span> <span
                class="hidden-xs-down">@lang('lang.embed_form')</span></a>
    </li>
</ul>