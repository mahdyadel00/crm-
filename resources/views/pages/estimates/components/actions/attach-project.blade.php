<div class="form-group row">
    <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.project')) }}</label>
    <div class="col-12">
        <select name="attach_project_id" id="attach_project_id" class="form-control form-control-sm js-select2-basic-search select2-hidden-accessible"
        data-ajax--url="{{ $payload['projects_feed_url'] }}"></select>
    </div>
</div>