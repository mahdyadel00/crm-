<!--options menu-->
<div class="col-sm-12 col-lg-3">
    <div class="card">
        <div class="row">
            <div class="col-lg-12">
                <div class="ticket-panel">
                    <div class="x-top-header">
                        {{ cleanLang(__('lang.ticket_options')) }}
                    </div>
                    <div class="x-body form-horizontal">
                        @if(auth()->user()->is_team)
                        <div class="form-group row">
                            <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.client')) }}</label>
                            <div class="col-12">
                                <select name="ticket_clientid" id="ticket_clientid" class="clients_and_projects_toggle form-control form-control-sm js-select2-basic-search select2-hidden-accessible"
                                    data-projects-dropdown="ticket_projectid" data-feed-request-type="clients_projects"
                                    data-ajax--url="{{ url('/') }}/feed/company_names"></select>
                            </div>
                        </div>

                        <!--project-->
                        <div class="form-group row">
                            <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.project')) }}</label>
                            <div class="col-12">
                                <select class="select2-basic form-control form-control-sm dynamic_ticket_projectid" id="ticket_projectid" name="ticket_projectid"
                                    disabled>
                                </select>
                            </div>
                        </div>
                        @endif
                        <!--department-->
                        <div class="form-group row">
                            <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.department')) }}</label>
                            <div class="col-12">
                                <select class="select2-basic form-control  form-control-sm" id="ticket_categoryid" name="ticket_categoryid">
                                    <option></option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}">
                                        {{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!--clients projects-->
                        @if(auth()->user()->is_client)
                        <div class="form-group row">
                            <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.project')) }}</label>
                            <div class="col-12">
                                <select class="select2-basic form-control  form-control-sm" id="ticket_projectid" name="ticket_projectid"
                                    data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach($clients_projects as $project)
                                    <option value="{{ $project->project_id }}">
                                        {{ $project->project_title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <!--priority-->
                        @if(auth()->user()->is_team)
                        <div class="form-group row">
                            <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.priority')) }}</label>
                            <div class="col-12">
                                <select class="select2-basic form-control  form-control-sm" id="ticket_priority" name="ticket_priority">
                                    @foreach(config('settings.ticket_priority') as $key => $value)
                                    <option value="{{ $key }}">{{ runtimeLang($key) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>