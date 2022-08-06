@extends('layout.wrapper')
@section('content')
<!-- main content -->
<div class="container-fluid {{ $document->doc_type }}">

    <!--[proposal] heading-->
    @if($document->doc_type == 'proposal')
    <div class="row page-titles">
        @include('pages.documents.components.proposal.crumbs')
        @if(auth()->user()->is_team)
        @include('pages.documents.components.proposal.actions-team')
        @else
        @include('pages.documents.components.proposal.actions-client')
        @endif
    </div>
    @endif

    <!--[proposal] heading-->
    @if($document->doc_type == 'contract')
    <div class="row page-titles">
        @include('pages.documents.components.contract.crumbs')
        @if(auth()->user()->is_team)
        @include('pages.documents.components.contract.actions-team')
        @else
        @include('pages.documents.components.contract.actions-client')
        @endif
    </div>
    @endif
    
    <!--container-->
    <div class="row" id="embed-content-container">

        @yield('document')

    </div>

</div>
<!--page content -->
</div>

<!--boot js-->
<script src="public/js/core/docs.js?v={{ config('system.versioning') }}"></script>
@endsection