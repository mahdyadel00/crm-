<div class="line m-t-20"></div>
<div class="doc-signed-panel">
    <div class="row">
        <div class="col-sm-12 col-6">
        </div>
        <div class="col-sm-12 col-6 text-right">
            <div class="p-r-40">
                <ul>
                    <li>
                        <h5>@lang('lang.client')</h5>
                    </li>
                    <li>{{ $document->doc_signed_first_name }}
                        {{ $document->doc_signed_last_name }}</li>
                    <li>
                        <img src="{{ url('storage/files/'.$document->doc_signed_signature_directory .'/'.$document->doc_signed_signature_filename) }}"
                            alt="@lang('lang.signature')" />
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>