<div class="form-group row">
    <label for="example-month-input" class="col-12 col-form-label text-left">{{ cleanLang(__('lang.category')) }}</label>
    <div class="col-12">
        <select class="select2-basic form-control form-control-sm" id="category"
            name="category">
            @foreach($categories as $category)
            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
            @endforeach
        </select>
    </div>
</div>