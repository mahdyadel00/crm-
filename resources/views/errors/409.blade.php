@extends(Auth::user() ? 'layout.wrapper' : 'layout.wrapperplain')
@section('content')
<!-- main content -->
<div class="container-fluid">
    <!-- General errors and notifications -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="page-notification">
                        <img class="m-b-30" src="{{ url('/') }}/public/images/404.png" alt="404 - Not found" /> 
                        <h2  class="m-b-30 font-weight-200"> {{ $error['message'] ?? '' }} </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--General errors and notifications -->
</div>
<!--main content -->
@endsection