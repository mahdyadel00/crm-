<div class="row">
    <div class="col-lg-12">
        <!--title-->
        <div class="form-group row">
            <label class="col-12 text-left control-label col-form-label required">{{ $page['form_label_category_name'] ?? '' }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="category_name" name="category_name"
                    value="{{ $category->category_name ?? '' }}">
                <input type="hidden" name="category_type" value="{{ request('category_type') }}">
            </div>
        </div>


        <!--migrate to another category-->
        @if(isset($page['section']) && $page['section'] == 'edit')
        <div class="form-group row">
            <label class="col-12 text-left control-label col-form-label required">{{ $page['form_label_move_items'] ?? '' }} ({{ cleanLang(__('lang.optional')) }})</label>
            <div class="col-12">
                <select class="select2-basic form-control form-control-sm" id="migrate"
                    name="migrate">
                    <option>&nbsp;</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

    </div>
</div>