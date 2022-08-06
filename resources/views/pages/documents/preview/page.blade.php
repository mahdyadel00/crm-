@extends('pages.documents.wrapper')
@section('document')
<div class="col-12">

    <div class="docs-main-wrapper editing-mode box-shadow">

        <!--hero header-->
        <div class="hero-header-wrapper" id="hero-header-wrapper">
            <!--[element] here header-->
            @include('pages.documents.elements.hero')
        </div>


        <!--[element] doc to and by-->
        @include('pages.documents.elements.doc-to-by')

        <!--[element] dates-->
        @include('pages.documents.elements.doc-details')

        <div class="doc-body">
            {!! $document->doc_body !!}


            <!--signatures (signed) -->
            @if($document->doc_status == 'accepted')
            @include('pages.documents.elements.signatures')
            @endif


            <!-- accept, decline & print buttons -->
            @if(config('visibility.proposal_accept_decline_button_footer'))
            <div class="doc-footer-actions-container">
                <div class="line m-t-20"></div>
                <div class="p-t-25 invoice-pay" id="invoice-buttons-container">
                    <div class="text-right">

                        <!--print-->
                        <a class="btn btn-secondary btn-outline"
                            href="{{ url('proposals/'.$document->doc_id.'?render=print') }}" target="_blank">
                            <span><i class="sl-icon-printer"></i> @lang('lang.print_proposal')</span> </a>

                        @if(config('visibility.proposal_accept_decline_buttons'))
                        <!--decline-->
                        <button class="buttons-accept-decline btn btn-danger confirm-action-danger"
                            data-confirm-title="@lang('lang.decline_proposal')"
                            data-confirm-text="@lang('lang.confirm_decline_proposal')" data-ajax-type="GET"
                            data-url="{{ url('proposals/'.$document->doc_unique_id.'/decline') }}">
                            @lang('lang.decline_proposal') </button>

                        <!--accept-->
                        <button type="button"
                            class="buttons-accept-decline btn btn-success edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal" data-progress-bar="hidden"
                            data-url="{{ url('proposals/'.$document->doc_unique_id.'/sign') }}"
                            data-loading-target="commonModalBody" data-modal-title="@lang('lang.accept_proposal')"
                            data-action-form-id="" data-modal-size="modal-lg"
                            data-action-url="{{ url('proposals/'.$document->doc_unique_id.'/accept') }}"
                            data-action-method="POST" data-action-ajax-class="js-ajax-ux-request">
                            @lang('lang.accept_proposal')
                        </button>
                        @endif

                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>



    <!--signature details-->

</div>
@endsection