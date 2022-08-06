@php
   $page['meta_title'] = __('lang.error_404');
@endphp
@extends(Auth::user() ? 'layout.wrapper' : 'layout.wrapperplain')
@section('content')
<!-- main content -->
<div class="container-fluid">
    <!-- page content -->
    <div class="row">
        <div class="col-12">
            <div class="permision-denied">
                <img src="{{ url('/') }}/public/images/404.png" alt="404 - Not found" /> 
                <div class="x-message"><h2>{{ $error['message'] ?? __('lang.error_not_found') }}</h2></div>
            </div>
        </div>
    </div>
    <!--page content -->
</div>
<!--main content -->
@endsection