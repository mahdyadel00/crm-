<div class="card-body card x-message p-t-0 hidden" id="ticket-editor">
    <form id="editTicketMessage">
        <div class="form-group row p-t-30">
            <label class="col-sm-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.subject')) }}*</label>
            <div class="col-sm-12">
                <input type="text" class="form-control form-control-sm" id="ticket_subject" name="ticket_subject" value="{{ $ticket->ticket_subject }}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.message')) }}*</label>
            <div class="col-sm-12">
                <textarea id="ticket_message" name="ticket_message" class="tinymce-textarea">{{ $ticket->ticket_message ?? '' }}</textarea>
            </div>
        </div>

        <!--ticket attachements-->
        <div class="x-attachements">
            <!--heading-->
            <div>
                <h5><i class="fa fa-paperclip m-r-10 m-b-10"></i> {{ cleanLang(__('lang.delete_selected_items')) }}</h5>
            </div>
            <!--attachments container-->
            <div class="row delete-attachments">
                <!--attachments-->
                @foreach($ticket->attachments as $attachment)
                <!--each file attachment-->
                <div class="col-md-12 col-lg-6">
                    <div class="file-attachment">
                        @if($attachment->attachment_type == 'image')
                        <div>
                            <img class="x-image" src="{{ url('storage/files/' . $attachment->attachment_directory .'/'. $attachment->attachment_thumbname) }}">
                        </div>
                        @else
                        <div class="x-image"> {{ $attachment->attachment_extension }}</div>
                        @endif
                        <div class="x-details">
                            <div class="x-name">{{ $attachment->attachment_filename }}</div>
                            <div class="x-date"><strong>{{ $attachment->creator->full_name ?? __('lang.unknown') }}</strong> -
                                {{
                                runtimeDate($attachment->attachment_created) }}</div>
                            <!--delete checkbox-->
                            <div class="x-delete hidden">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="attachments[{{ $attachment->attachment_id }}]">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!--each file attachment-->
                @endforeach
            </div>
        </div>

        <div class="text-right p-t-30">
            <input type="hidden" name="edit_source" value="page">
            <button type="button" class="btn btn-rounded-x btn-danger waves-effect ticket-editor-toggle">{{ cleanLang(__('lang.cancel')) }}</button>
            <button type="submit" id="editTicketMessageButton" class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request"
                data-url="{{ url('/tickets') }}/{{ $ticket->ticket_id }}" data-ajax-type="PUT" data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
        </div>
    </form>
</div>