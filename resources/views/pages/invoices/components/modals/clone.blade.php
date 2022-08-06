<div class="row">
    <div class="col-lg-12">


        <!--invoice date-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.invoice_date')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control  form-control-sm pickadate" name="bill_date" autocomplete="off"
                    value="{{ runtimeDatepickerDate($invoice->bill_date ?? '') }}">
                <input class="mysql-date" type="hidden" name="bill_date" id="bill_date"
                    value="{{ $invoice->bill_date ?? '' }}">
            </div>
        </div>

        <!--due date-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.due_date')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm pickadate" name="bill_due_date"
                    autocomplete="off" value="{{ runtimeDatepickerDate($invoice->bill_due_date ?? '') }}">
                <input class="mysql-date" type="hidden" name="bill_due_date" id="bill_due_date"
                    value="{{ $invoice->bill_due_date ?? '' }}">
            </div>
        </div>

        <!--client-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.client')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <!--select2 basic search-->
                <select name="bill_clientid" id="bill_clientid"
                    class="clients_and_projects_toggle form-control form-control-sm js-select2-basic-search select2-hidden-accessible"
                    data-projects-dropdown="bill_projectid" data-feed-request-type="clients_projects"
                    data-ajax--url="{{ url('/') }}/feed/company_names">
                </select>
            </div>
        </div>

        
        <!--projects-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.project')) }}</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm dynamic_bill_projectid" id="bill_projectid" name="bill_projectid"
                    disabled>
                </select>
            </div>
        </div>
        
        <!--tags-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.tags')) }}</label>
            <div class="col-sm-12 col-lg-9">
                <select name="tags" id="tags"
                    class="form-control form-control-sm select2-multiple {{ runtimeAllowUserTags() }} select2-hidden-accessible"
                    multiple="multiple" tabindex="-1" aria-hidden="true">
                    <!--array of selected tags-->
                    @foreach($invoice->tags as $tag)
                    @php $selected_tags[] = $tag->tag_title ; @endphp
                    @endforeach
                    <!--/#array of selected tags-->
                    @foreach($tags as $tag)
                    <option value="{{ $tag->tag_title }}"
                        {{ runtimePreselectedInArray($tag->tag_title ?? '', $selected_tags  ?? []) }}>{{ $tag->tag_title }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <!--/#tags-->
        <!--invoice category-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.category')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="bill_categoryid" name="bill_categoryid">
                    @foreach($categories as $category)
                    <option value="{{ $category->category_id }}"
                        {{ runtimePreselected($invoice->bill_categoryid ?? '', $category->category_id) }}>{{
                                runtimeLang($category->category_name) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
</div>