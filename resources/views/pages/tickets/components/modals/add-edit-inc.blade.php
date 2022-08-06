<div class="row">
    <div class="col-lg-12">

        @if(config('system.settings_tickets_edit_subject') == 'yes')
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.subject')) }}*</label>
            <div class="col-sm-12">
                <input type="text" class="form-control form-control-sm" id="ticket_subject" name="ticket_subject"
                    value="{{ $ticket->ticket_subject }}">
            </div>
        </div>
        @endif
        @if(config('system.settings_tickets_edit_body') == 'yes')
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.message')) }}*</label>
            <div class="col-sm-12">
                <textarea id="ticket_message" name="ticket_message"
                    class="tinymce-textarea">{{ $ticket->ticket_message ?? '' }}</textarea>
            </div>
        </div>
        @endif

        @if(request('edit_type') == 'options' || request('edit_type') == 'all')
        <!--department-->
        <div class="form-group row">
            <label for="example-month-input"
                class="col-sm-12 col-lg-3 col-form-label text-left required">{{ cleanLang(__('lang.department')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control  form-control-sm" id="ticket_categoryid"
                    name="ticket_categoryid">
                    @foreach($categories as $category)
                    <option value="{{ $category->category_id }}"
                        {{ runtimePreselected($ticket->ticket_categoryid ?? '', $category->category_id) }}>
                        {{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!--project-->
        <div class="form-group row">
            <label for="example-month-input"
                class="col-sm-12 col-lg-3 col-form-label text-left required">{{ cleanLang(__('lang.project')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control  form-control-sm" id="ticket_projectid"
                    name="ticket_projectid">
                    @foreach($projects as $project)
                    <option value="{{ $project->project_id }}"
                        {{ runtimePreselected($ticket->ticket_projectid ?? '', $project->project_id) }}>
                        {{ $project->project_title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!--status-->
        <div class="form-group row">
            <label for="example-month-input"
                class="col-sm-12 col-lg-3 col-form-label text-left required">{{ cleanLang(__('lang.status')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control  form-control-sm" id="ticket_status" name="ticket_status">
                    @foreach(config('settings.ticket_statuses') as $key => $value)
                    <option value="{{ $key }}" {{ runtimePreselected($ticket->ticket_status ?? '', $key) }}>{{
                        runtimeLang($key) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!--priority-->
        <div class="form-group row">
            <label for="example-month-input"
                class="col-sm-12 col-lg-3 col-form-label text-left required">{{ cleanLang(__('lang.priority')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control  form-control-sm" id="ticket_priority" name="ticket_priority">
                    @foreach(config('settings.ticket_priority') as $key => $value)
                    <option value="{{ $key }}" {{ runtimePreselected($ticket->ticket_priority ?? '', $key) }}>{{
                        runtimeLang($key) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        <!--type-->
        <input type="hidden" name="edit_type" value="{{ request('edit_type') }}">
        <input type="hidden" name="edit_source" value="{{ request('edit_source') }}">


        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>
    </div>
</div>