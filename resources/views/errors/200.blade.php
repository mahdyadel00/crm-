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
                        <h2 class="font-weight-200">{{ $exception->getMessage() }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--General errors and notifications -->
</div>
<!--main content -->
@endsection