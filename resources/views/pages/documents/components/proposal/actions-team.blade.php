<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-6 align-self-center text-right parent-page-actions p-b-9"
    id="list-page-actions-container-proposals">
    <div id="list-page-actions">


        <!--reminder-->
        @if(config('visibility.modules.reminders'))
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.reminder')) }}"
            id="reminders-panel-toggle-button"
            class="reminder-toggle-panel-button list-actions-button btn btn-page-actions waves-effect waves-dark js-toggle-reminder-panel ajax-request {{ $document->reminder_status }}"
            data-url="{{ url('reminders/start?resource_type='.$document->doc_type.'&resource_id='.$document->doc_id) }}"
            data-loading-target="reminders-side-panel-body" data-progress-bar='hidden'
            data-target="reminders-side-panel" data-title="@lang('lang.my_reminder')">
            <i class="ti-alarm-clock"></i>
        </button>
        @endif

        @if(config('visibility.document_options_buttons'))
        <!--publish-->
        @if($document->doc_status == 'draft')
        <button type="button" data-toggle="tooltip" title="@lang('lang.publish_document')"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark confirm-action-info"
            href="javascript:void(0)" data-confirm-title="@lang('lang.publish_document')"
            data-confirm-text="@lang('lang.documeny_publish_confirm')"
            data-url="{{ urlResource('/'.$document->doc_type.'s/'.$document->doc_id.'/publish') }}"
            id="document-action-publish"><i class="sl-icon-share-alt"></i></button>
        @endif
        <!--email invoice-->
        <button type="button" data-toggle="tooltip" title="@lang('lang.send_email')"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark confirm-action-info"
            href="javascript:void(0)" data-confirm-title="@lang('lang.send_email')"
            data-confirm-text="@lang('lang.confirm')"
            data-url="{{ urlResource('/'.$document->doc_type.'s/'.$document->doc_id.'/resend') }}"
            id="document-action-email"><i class="ti-email"></i></button>

        @if(config('visibility.document_edit_button'))
        <!--edit button-->
        <a data-toggle="tooltip" title="@lang('lang.edit')"
            href="{{ urlResource('/'.$document->doc_type.'s/'.$document->doc_id.'/edit') }}"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark">
            <i class="sl-icon-note"></i>
        </a>

        <!--settings-->
        <span class="dropdown">
            <button type="button" data-toggle="dropdown" title="{{ cleanLang(__('lang.edit')) }}" aria-haspopup="true"
                aria-expanded="false"
                class="data-toggle-tooltip list-actions-button btn btn-page-actions waves-effect waves-dark">
                <i class="sl-icon-wrench"></i>
            </button>

            <div class="dropdown-menu" aria-labelledby="listTableAction">
                <a class="dropdown-item" href="{{ url('/proposals/view/'.$document->doc_unique_id.'?action=preview') }}"
                    target="_blank"><i class="ti-new-window display-inline-block p-r-5"></i>
                    @lang('lang.proposal_url')</a>

                <!--Mark As Accepted-->
                <a class="dropdown-item confirm-action-info {{ runtimeVisibility('document-status', 'accepted', $document->doc_status)}}"
                    href="javascript:void(0)" data-confirm-title="{{ cleanLang(__('lang.mark_as_accepted')) }}"
                    id="bill-actions-dettach-project" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                    data-url="{{ url('/proposals/'.$document->doc_id.'/change-status?status=accepted') }}">
                    <i class="sl-icon-check display-inline-block p-r-5"></i> @lang('lang.mark_as_accepted')</a>

                <!--Mark As Declined-->
                <a class="dropdown-item confirm-action-danger {{ runtimeVisibility('document-status', 'declined', $document->doc_status)}}"
                    href="javascript:void(0)" data-confirm-title="{{ cleanLang(__('lang.mark_as_accepted')) }}"
                    id="bill-actions-dettach-project" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                    data-url="{{ url('/proposals/'.$document->doc_id.'/change-status?status=declined') }}">
                    <i class="sl-icon-close display-inline-block p-r-5"></i> @lang('lang.mark_as_declined')</a>

                <!--Mark As Revised-->
                <a class="dropdown-item confirm-action-danger {{ runtimeVisibility('document-status', 'revised', $document->doc_status)}}"
                    href="javascript:void(0)" data-confirm-title="{{ cleanLang(__('lang.mark_as_revised')) }}"
                    id="bill-actions-dettach-project" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                    data-url="{{ url('/proposals/'.$document->doc_id.'/change-status?status=revised') }}">
                    <i class="sl-icon-close display-inline-block p-r-5"></i> @lang('lang.mark_as_revised')</a>

            </div>
        </span>
        @endif

        <!--print-->
        <a data-toggle="tooltip" title="@lang('lang.print')"
            href="{{ url('proposals/'.$document->doc_id.'?render=print') }}" target="_blank"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark">
            <i class="sl-icon-printer"></i>
        </a>

        <!--edit cost estimate-->
        @if(config('visibility.document_edit_estimate_button'))
        <button type="button"
            class="list-actions-button btn-text btn btn-page-actions waves-effect waves-dark js-toggle-side-panel"
            id="js-document-billing"
            data-url="{{ url('estimates/'.$estimate->bill_estimateid.'/edit-estimate?estimate_mode=document') }}"
            data-progress-bar="hidden" data-loading-target="documents-side-panel-billing-content"
            data-target="documents-side-panel-billing">
            @lang('lang.edit_billing')
        </button>
        @endif

        <!--show variables-->
        @if(config('visibility.document_edit_variables_button'))
        <button type="button"
            class="list-actions-button btn-text btn btn-page-actions waves-effect waves-dark js-toggle-side-panel"
            data-target="documents-side-panel-variables">
            @lang('lang.variables')
        </button>
        @endif

        <!--exit buton-->
        @if(config('visibility.document_edit_variables_button'))
        <a data-toggle="tooltip" title="@lang('lang.exit_editing_mode')"
            href="{{ url('proposals/'.$document->doc_id) }}"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark">
            <i class="sl-icon-logout"></i>
        </a>
        @endif
        @endif


        <!--delete proposal-->
        @if(config('visibility.delete_proposal_button'))
        <!--delete-->
        <button type="button" data-toggle="tooltip" title="{{ cleanLang(__('lang.delete_proposal')) }}"
            class="list-actions-button btn btn-page-actions waves-effect waves-dark confirm-action-danger"
            data-confirm-title="{{ cleanLang(__('lang.delete_proposal')) }}"
            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
            data-url="{{ url('/proposals/'.$document->proposal_id.'?source=page') }}"><i
                class="sl-icon-trash"></i></button>
        @endif
    </div>
</div>
<!-- action buttons -->