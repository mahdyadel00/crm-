<div class="doc-dates-container" id="doc-details">
    <div class="doc-dates-wrapper  {{ documentEditingModeCheck1($payload['mode'] ?? '') }}">
        <!--editing icons-->
        <div class="doc-edit-icon  {{ documentEditingModeCheck2($payload['mode'] ?? '') }}">
            <span class="x-edit-icon js-toggle-side-panel" data-target="documents-side-panel-details">
                <i class="sl-icon-note"></i>
            </span>
        </div>


        <div class="pull-left x-dates">
            <table>
                <tbody>
                    <!--issue date-->
                    <tr id="document_dates_section_1">
                        <td class="x-left-lang font-weight-500">
                            @if($document->doc_type == 'proposal')
                            <span>@lang('lang.proposal_date'):</span>
                            @else
                            <span>@lang('lang.contract_start_date'):</span>
                            @endif
                        </td>
                        <td class="x-left-date p-l-25">
                            <span>{{ runtimeDate($document->doc_date_start) }}</span>
                        </td>
                    </tr>
                    <!--valid until-->
                    <tr id="document_dates_section_2">
                        <td class="x-left-lang  p-t-10 font-weight-500">
                            @if($document->doc_type == 'proposal')
                            <span>@lang('lang.valid_until'):</span>
                            @else
                            <span>@lang('lang.contract_end_date'):</span>
                            @endif
                        </td>
                        <td class="x-left-id p-l-25 p-t-10">
                            @if($document->doc_type == 'proposal')
                            <span>{{ runtimeDate($document->doc_date_end) }}</span>
                            @else
                            <span>{{ runtimeDate($document->doc_date_end, __('lang.open_ended')) }}</span>
                            @endif
                        </td>
                    </tr>
                    <!--prepared by-->
                    <tr id="document_dates_section_3">
                        <td class="x-left-lang p-t-10 font-weight-500">
                            <span id="doc_prepared_by_title">@lang('lang.prepared_by'):</span> </td>
                        <td class="x-left-id p-l-25 p-t-10">
                            <span id="doc_prepared_by_name">{{ $document->first_name ?? '---' }}
                                {{ $document->last_name ?? '---' }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="pull-right">
            <table>
                <tbody>
                    <!----DOC ID-->
                    <tr id="document_dates_section_4">
                        <td class="x-left-lang p-t-10 font-weight-500">
                            @if($document->doc_type == 'proposal')
                            <span>@lang('lang.proposal_id'):</span>
                            @else
                            <span>@lang('lang.contract_id'):</span>
                            @endif </td>
                        <td class="x-left-id p-l-25 p-t-10">
                            @if($document->doc_type == 'proposal')
                            <span>#{{ runtimeProposalIdFormat($document->doc_id) }}</span>
                            @else
                            <span>{{ runtimeContractIdFormat($document->doc_id) }}</span>
                            @endif
                        </td>
                    </tr>
                    <!--value-->
                    <tr id="document_dates_section_5">
                        <td class="x-left-lang p-t-10 font-weight-500">
                            <span>@lang('lang.value'):</span>
                        <td class="x-left-id p-l-25 p-t-10">
                            <span>{{ runtimeMoneyFormat($document->bill_final_amount ?? 0) }}</span>
                        </td>
                    </tr>
                    <!--status-->
                    <tr id="document_dates_section_6">
                        <td class="x-right-lang  p-t-10 font-weight-500">@lang('lang.status'): </td>
                        <td class="x-right-status  p-t-4 p-l-25">
                            <span class="x-right-label">
                                @if($document->doc_type == 'proposal')
                                <label class="label label-rounded m-b-0 m-t-6 p-l-15 p-r-15 {{ runtimeProposalStatusColors($document->doc_status, 'label') }}"
                                    id="document-status-label">{{ runtimeLang($document->doc_status) }}</label>
                                @else
                                <label class="label label-rounded m-b-0 m-t-6 p-l-15 p-r-15 runtimeProposalStatusColors{{ runtimeContractStatusColors($document->doc_status, 'label') }}"
                                    id="document-status-label">{{ runtimeLang($document->doc_status) }}</label>
                                @endif
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="clear-both">
            <!--fix-->
        </div>
    </div>
</div>