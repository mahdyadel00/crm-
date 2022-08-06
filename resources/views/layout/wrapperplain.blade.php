<!DOCTYPE html>
<html lang="en" class="logged-out">

<!--html header-->
@include('layout.header')
<!--html header-->

<body class="{{ $page['page'] ?? '' }}">
    <!--preloader-->
    <div class="preloader">
        <div class="loader">
            <div class="loader-loading"></div>
        </div>
    </div>
    <!--preloader-->

    <!--main content-->
    <div id="main-wrapper">
        @yield('content')
    </div>

    <!--common modals-->
    @include('modals.actions-modal-wrapper')
    @include('modals.common-modal-wrapper')
</body>

@include('layout.footerjs')
<!--js automations-->
@include('layout.automationjs')
<!--[note: no sanitizing required] for this trusted content, which is added by the admin-->
{!! config('system.settings_theme_body') !!}

<!--[PRINTING]-->
@if(config('visibility.page_rendering') == 'print-page')
<script src="public/js/dynamic/print.js?v={{ config('system.versioning') }}"></script>
@endif

</html>