<!-- right-sidebar -->
<div class="right-sidebar documents-side-panel-details sidebar-md" id="documents-side-panel-details">
    <form>
        <div class="slimscrollright">
            <!--title-->
            <div class="rpanel-title">
                <!--add class'due'to title panel -->
                <i class="sl-icon-note display-inline-block m-t--5"></i>
                <div class="display-inline-block">
                    @lang('lang.edit_details')
                </div>
                <span>
                    <i class="ti-close js-close-side-panels" data-target="documents-side-panel-details"
                        id="documents-side-panel-details-close-icon"></i>
                </span>
            </div>
            <!--title-->
            <!--body-->
            <div class="r-panel-body documents-side-panel-details-body  p-b-80" id="documents-side-panel-details-body">



                <!--doc_date_start-->
                <div class="form-group row">
                    <label class="col-sm-12 text-left control-label col-form-label required">
                        @if($document->doc_type == 'proposal')
                        <span>@lang('lang.proposal_date'):</span>
                        @else
                        <span>@lang('lang.contract_start_date'):</span>
                        @endif
                    </label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control form-control-sm pickadate" autocomplete="off"
                            name="doc_date_start" value="{{ runtimeDatepickerDate($document->doc_date_start ?? '') }}">
                        <input class="mysql-date" type="hidden" name="doc_date_start" id="doc_date_start"
                            value="{{ $document->doc_date_start ?? '' }}">
                    </div>
                </div>


                <!--doc_date_end-->
                <div class="form-group row">
                    <label class="col-sm-12 text-left control-label col-form-label required">
                        @if($document->doc_type == 'proposal')
                        <span>@lang('lang.valid_until'):</span>
                        @else
                        <span>@lang('lang.contract_end_date'):</span>
                        @endif
                    </label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control form-control-sm pickadate" autocomplete="off"
                            name="doc_date_end" value="{{ runtimeDatepickerDate($document->doc_date_end ?? '') }}">
                        <input class="mysql-date" type="hidden" name="doc_date_end" id="doc_date_end"
                            value="{{ $document->doc_date_end ?? '' }}">
                    </div>
                </div>


                <!--created by-->
                <div class="form-group row">
                    <label
                        class="col-sm-12 text-left control-label col-form-label required">@lang('lang.prepared_by')</label>
                    <div class="col-sm-12">
                        <select class="select2-basic form-control form-control-sm"
                            id="doc_creatorid" name="doc_creatorid">
                            <option></option>
                            @foreach(config('system.team_members') as $user)
                            <option value="{{ $user->id }}" {{ runtimePreselected($document->doc_creatorid ?? '', $user->id) }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <!--category-->
                <div class="form-group row">
                    <label
                        class="col-sm-12 text-left control-label col-form-label required">@lang('lang.category')</label>
                    <div class="col-sm-12">
                        <select class="select2-basic form-control form-control-sm" id="doc_categoryid"
                            name="doc_categoryid">
                            @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" {{ runtimePreselected($document->doc_categoryid ?? '', $category->category_id) }}>{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <!--status-->
                <div class="form-group row">
                    <label
                        class="col-sm-12 text-left control-label col-form-label required">@lang('lang.status')</label>
                    <div class="col-sm-12">
                        <select class="select2-basic form-control form-control-sm" id="doc_status"
                            name="doc_status">
                            <option value="draft" {{ runtimePreselected($document->doc_status ?? '','draft') }}>@lang('lang.draft')</option>
                            <option value="new" {{ runtimePreselected($document->doc_status ?? '','new') }}>@lang('lang.new')</option>
                            <option value="accepted" {{ runtimePreselected($document->doc_status ?? '','accepted') }}>@lang('lang.accepted')</option>
                            <option value="declined" {{ runtimePreselected($document->doc_status ?? '','declined') }}>@lang('lang.declined')</option>
                            <option value="revised"{{ runtimePreselected($document->doc_status ?? '','revised') }}>@lang('lang.revised')</option>
                        </select>
                    </div>
                </div>


                <!--document type-->
                <input type="hidden" name="doc_type" value="{{ $document->doc_type ?? '' }}">

                <!--buttons-->
                <div class="buttons-block">
                    <button type="button" class="btn btn-rounded-x btn-info js-ajax-ux-request"
                        data-url="{{ url('documents/'.$document->doc_id.'/update/details') }}" data-type="form"
                        data-form-id="documents-side-panel-details"
                        data-ajax-type="post">@lang('lang.save_changes')</button>
                </div>

            </div>
            <!--body-->
        </div>
    </form>
</div>
<!--sidebar-->