<!--[dynamic link] -  project tab menu [MODULES]-->
<li class="nav-item" id="{{ __($payload['id_li']) }}">
    <a class="nav-link tabs-menu-item   js-dynamic-url js-ajax-ux-request"
        id="{{ __($payload['id_a']) }}"
        data-toggle="tab"
        data-loading-class="loading-tabs" 
        data-loading-target="embed-content-container"
        data-dynamic-url="{{ _url('/projects') }}/{{ $payload['id'] ?? 0 }}/modules/{{ __($payload['url_title']) }}"
        data-url="{{ $href }}" 
        href="javascript:void(0);"
        role="tab">{{ __($payload['name']) }}</a>
</li>