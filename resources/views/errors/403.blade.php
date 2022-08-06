@php
   $page['meta_title'] = __('lang.error_no_permission_for_resource');
@endphp
@extends(Auth::user() ? 'layout.wrapper' : 'layout.wrapperplain')
@section('content')
<!-- main content -->
<div class="container-fluid">
    <!-- page content -->
    <div class="row">
        <div class="col-12">
            <div class="permision-denied">
                <img src="{{ url('/') }}/public/images/permission-denied.png" alt="permission denied" /> 
                <div class="x-message"><h2>{{ cleanLang(__('lang.error_no_permission_for_resource')) }}</h2></div>
            </div>
        </div>
    </div>
    <!--page content -->
</div>
<!--main content -->
@endsection