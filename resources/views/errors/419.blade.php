@php
   $page['meta_title'] = __('lang.error_session_timeout');
@endphp
@extends(Auth::user() ? 'layout.wrapper' : 'layout.wrapperplain')
@section('content')
<!-- main content -->
<div class="container-fluid">
    <!-- page content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="page-notification">
                        <h2 class="font-weight-200">{{ cleanLang(__('lang.error_session_timeout')) }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--page content -->
</div>
<!--main content -->
@endsection