<!--[plain link] - dyamic menu markup for [MODULES]-->
@if($payload['type'] == 'standard-link')
<li data-modular-id="{{ $payload['id_li'] }}" id="{{ $payload['id_li'] }}"
    class="sidenav-menu-item menu-tooltip menu-with-tooltip {{ $payload['classes_li'] }}"
    title="{{ __($payload['title']) }}">
    <a class="waves-effect waves-dark  {{ $payload['classes_a'] }}" href="{{ $href }}" aria-expanded="false"
        target="{{ $payload['target'] }}">
        <i class="{{ $payload['icon'] }}"></i>
        <span class="hide-menu">{{ __($payload['name']) }}
        </span>
    </a>
</li>
@endif


<!--add item modal - dyamic menu markup for [MODULES]-->
@if($payload['type'] == 'dymanic-modal')
<li data-modular-id="{{ $payload['id_li'] }}" id="{{ $payload['id_li'] }}"
    class="sidenav-menu-item menu-tooltip menu-with-tooltip {{ $payload['classes_li'] }}"
    title="{{ __($payload['title']) }}">
    <a class="waves-effect waves-dark  {{ $payload['classes_a'] }} edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
        href="javascript:void(0);" aria-expanded="false" data-toggle="modal" data-target="#{{ $payload['target'] }}" data-loading-target="commonModalBody"
        data-modal-title="{{ __($payload['modal_title']) }}"
        data-footer-visibility="hidden"
        data-url="{{ $href }}">
        <i class="{{ $payload['icon'] }}"></i>
        <span class="hide-menu">{{ __($payload['name']) }}
        </span>
    </a>
</li>
@endif



<!--[stdnard modal] add item modal - dyamic menu markup for [MODULES]-->
@if($payload['type'] == 'standard-modal')
<li data-modular-id="{{ $payload['id_li'] }}" id="{{ $payload['id_li'] }}"
    class="sidenav-menu-item menu-tooltip menu-with-tooltip {{ $payload['classes_li'] }}"
    title="{{ __($payload['title']) }}">
    <a class="waves-effect waves-dark  {{ $payload['classes_a'] }}"
        href="javascript:void(0);" aria-expanded="false" data-toggle="modal" data-target="#{{ $payload['target'] }}"
        data-modal-title="{{ __($payload['modal_title']) }}">
        <i class="{{ $payload['icon'] }}"></i>
        <span class="hide-menu">{{ __($payload['name']) }}
        </span>
    </a>
</li>
@endif