@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->

<div class="row">
    <div class="col-md-6">
        <div class="card card-outline-warning">
            <div class="card-header fx-settings-logo-card">
                <h4 class="m-b-0">{{ cleanLang(__('lang.large_logo')) }}</h4>
            </div>
            <div class="card-body text-center">
                <div class="p-b-20"> <img class="logo-large display-inline-block" src="{{ runtimeLogoLarge() }}" alt="{{ cleanLang(__('lang.large_logo')) }}">
                </div>
                <p class="card-text">{{ cleanLang(__('lang.logo_used_when_menu_is_expanded')) }}</p>
                <p class="card-text">{{ cleanLang(__('lang.logo_used_on_pdf_documents')) }}</p>
                <p class="card-text text-bold font-weight-500">{{ cleanLang(__('lang.best_image_dimensions')) }} : (185px X
                    45px)</p>

                <a href="javascript:void(0)"
                    class="btn btn-rounded-x btn-danger waves-effect js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
                    data-toggle="modal" data-target="#commonModal" data-url="{{ url('/settings/logos/uploadlogo?logo_size=large') }}"
                    data-loading-target="commonModalBody" data-modal-size="modal-sm"
                    data-header-close-icon="hidden"
                    data-add-class="settings-logo"
                    data-modal-title="{{ cleanLang(__('lang.update_avatar')) }}" data-header-visibility="hidden"
                    data-header-extra-close-icon="visible" data-action-url="{{ url('/settings/logos/uploadlogo') }}"
                    data-action-method="PUT">{{ cleanLang(__('lang.change_logo')) }}</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline-warning">
            <div class="card-header fx-settings-logo-card">
                <h4 class="m-b-0">{{ cleanLang(__('lang.small_logo')) }}</h4>
            </div>
            <div class="card-body text-center">
                <div class="p-b-20"> <img class="logo-small display-inline-block" src="{{ runtimeLogoSmall() }}" alt="{{ cleanLang(__('lang.small_logo')) }}">
                </div>
                <p class="card-text">{{ cleanLang(__('lang.logo_used_when_menu_is_collapsed')) }}</p>
                <p class="card-text text-bold font-weight-500">{{ cleanLang(__('lang.best_image_dimensions')) }} : (45px X
                    45px)</p>

                    <a href="javascript:void(0)"
                    class="btn btn-rounded-x btn-danger waves-effect js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
                    data-toggle="modal" data-target="#commonModal" data-url="{{ url('/settings/logos/uploadlogo?logo_size=small') }}"
                    data-loading-target="commonModalBody" data-modal-size="modal-sm"
                    data-header-close-icon="hidden"
                    data-add-class="settings-logo"
                    data-modal-title="{{ cleanLang(__('lang.update_avatar')) }}" data-header-visibility="hidden"
                    data-header-extra-close-icon="visible" data-action-url="{{ url('/settings/logos/uploadlogo') }}"
                    data-action-method="PUT">{{ cleanLang(__('lang.change_logo')) }}</a>
            </div>
        </div>
    </div>
</div>

@endsection