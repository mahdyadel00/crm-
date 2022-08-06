<form action="" class="w-100" method="post" id="ticket-compose-form"  data-user-type="{{ auth()->user()->type }}">
    <div class="row ticket-compose" id="ticket-compose">
        <!--options panel-->
        @include('pages.tickets.components.create.options')


        <!--compose panel-->
        <div class="col-sm-12 col-lg-9">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body">
                            <div class="form-group">
                                <input class="form-control" name="ticket_subject" id="ticket_subject"
                                    placeholder="{{ cleanLang(__('lang.subject')) }}:">
                            </div>
                            <div class="form-group">
                                <textarea class="tinymce-textarea" name="ticket_message" id="ticket_message"
                                    rows="15"></textarea>
                            </div>
                            <!--fileupload-->
                            <div class="form-group row">
                                <div class="col-12">
                                    <div class="dropzone dz-clickable" id="fileupload_ticket">
                                        <div class="dz-default dz-message">
                                            <i class="icon-Upload-toCloud"></i>
                                            <span>{{ cleanLang(__('lang.drag_drop_file')) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--fileupload-->
                            <div class="text-lg-right">
                                <button type="submit" class="btn btn-rounded-x btn-danger m-t-20"
                                    id="ticket-compose-form-button" data-url="{{ url('/tickets') }}" data-type="form"
                                    data-ajax-type="post" data-loading-overlay-target="wrapper-tickets"
                                    data-loading-overlay-classname="overlay"
                                    data-form-id="ticket-compose">{{ cleanLang(__('lang.submit_ticket')) }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>