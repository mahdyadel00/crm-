@extends('layout.wrapperplain')
@section('content')
<!-- main content -->
<div class="container-fluid {{ $document->doc_type }}">

    <!--container-->
    <div class="row" id="embed-content-container">

        <!--HEADER & ACTIONS-->
        @if(config('visibility.proposal_accept_decline_button_header'))
        @include('pages.documents.components.proposal.actions-public')
        @endif

        <!--DOCUMENT-->
        @yield('document')

    </div>

</div>
<!--page content -->
</div>
@endsection