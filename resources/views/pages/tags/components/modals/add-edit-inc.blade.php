<div class="row">
    <div class="col-lg-12">
        @if(isset($page['section']) && $page['section'] == 'create')
        <div class="form-group row">
            <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.resource_type')) }}</label>
            <div class="col-12">
                <select class="select2-basic form-control form-control-sm" id="tagresource_type"
                    name="tagresource_type">
                    <option></option>
                    @foreach($resource_types as $type)
                    <option value="{{ $type }}">{{ runtimeLang($type) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
        <!--title-->
        <div class="form-group row">
            <label class="col-12 text-left control-label col-form-label">Tag {{ cleanLang(__('lang.title')) }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="tag_title" name="tag_title"
                    value="{{ $tag->tag_title ?? '' }}">
            </div>
        </div>
        <!--pass source-->
        <input type="hidden" name="source" value="{{ request('source') }}">
        <!--notes-->
        @if(isset($page['section']) && $page['section'] == 'create')
        <div class="row">
            <div class="col-12">
                <div><small>{{ cleanLang(__('lang.tags_available_to_all_users')) }}</small></div>
            </div>
        </div>
        @endif
    </div>
</div>