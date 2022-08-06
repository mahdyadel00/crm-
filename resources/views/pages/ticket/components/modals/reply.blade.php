<div class="row">
    <div class="col-lg-12">

        <!--comment-->
        <div class="form-group row">
            <label class="col-sm-12text-left control-label col-form-label">Description</label>
            <div class="col-sm-12">
                <textarea class="form-control form-control-sm tinymce-textarea" rows="5" name="ticketreply_text"
                    id="ticketreply_text"></textarea>
            </div>
        </div>

        <!--fileupload-->
        <div class="form-group row">
            <div class="col-12">
                <div class="dropzone dz-clickable" id="fileupload_ticket_reply">
                    <div class="dz-default dz-message">
                        <i class="icon-Upload-toCloud"></i>
                        <span>{{ cleanLang(__('lang.drag_drop_file')) }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!--fileupload-->

        <!--ticketid-->
        <input type="hidden" name="ticketreply_ticketid" value="{{ $ticket->ticket_id }}">
    </div>
</div>
