<!--title-->
<div class="form-group row">
    <label
        class="col-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.category_name')) }}</label>
    <div class="col-12">
        <input type="text" class="form-control form-control-sm" id="kbcategory_title" name="kbcategory_title"
            value="{{ $category->kbcategory_title ?? '' }}">
    </div>
</div>

<!--category type-->
@if(config('visibility.select_category'))
<div class="form-group row">
    <label class="col-12 text-left control-label col-form-label required">@lang('lang.type')</label>
    <div class="col-12">
        <select class="select2-basic form-control form-control-sm select2-preselected" id="kbcategory_type"
            name="kbcategory_type" data-preselected="{{ $category->kbcategory_type ?? ''}}">
            <option value="text">@lang('lang.standard_text')</option>
            <option value="video">@lang('lang.video')</option>
        </select>
    </div>
</div>
@endif

<div class="form-group row">
    <label class="col-12 text-left control-label col-form-label">{{ cleanLang(__('lang.description')) }}</label>
    <div class="col-12">
        <textarea class="form-control form-control-sm tinymce-textarea" rows="5" name="kbcategory_description"
            id="kbcategory_description">{!! clean($category->kbcategory_description ?? '---') !!}</textarea>
    </div>
</div>

<!--visibility-->
<div class="form-group row">
    <label class="col-12 col-form-label text-left">{{ cleanLang(__('lang.visible_to')) }}</label>
    <div class="col-12">
        <select class="select2-basic form-control form-control-sm" id="kbcategory_visibility"
            name="kbcategory_visibility">
            <option value="everyone" {{ runtimePreselected('everyone', $category->kbcategory_visibility ?? '') }}>
                {{ cleanLang(__('lang.everyone')) }}
            </option>
            <option value="team" {{ runtimePreselected('team', $category->kbcategory_visibility ?? '') }}>
                {{ cleanLang(__('lang.team')) }}
            </option>
            <option value="client" {{ runtimePreselected('client', $category->kbcategory_visibility ?? '') }}>
                {{ cleanLang(__('lang.clients')) }}</option>
        </select>
    </div>
</div>

<!--display-icon-->
<div class="form-group row">
    <label class="col-12 col-form-label text-left">{{ cleanLang(__('lang.category_icon')) }}</label>
    <div class="col-12 js-switch-toggle-icons" id="fx-settings-kb-icon-wrapper"
        data-target="category_display_icons_section">
        @if(isset($page['section']) && $page['section'] == 'edit')
        <i id="icon-selector-display" class="{{ $category->kbcategory_icon ?? '' }}"></i>
        <input type="hidden" name="kbcategory_icon" id="kbcategory_icon" value="{{ $category->kbcategory_icon ?? '' }}">
        @else
        <i id="icon-selector-display" class="sl-icon-docs"></i>
        <input type="hidden" name="kbcategory_icon" id="kbcategory_icon" value="sl-icon-docs">
        @endif
    </div>
</div>
<!--spacer-->
<!--option toggle-->
<div class="hidden" id="category_display_icons_section">
    @include('misc.icons')
</div>
<!--option toggle-->

<!--migrate to another category-->
@if(isset($page['section']) && $page['section'] == 'edit')
<div class="line"></div>
<div class="form-group row">
    <label
        class="col-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.move_artiles_to_another_category')) }}
        ({{ cleanLang(__('lang.optional')) }})</label>
    <div class="col-12">
        <select class="select2-basic form-control form-control-sm" id="migrate" name="migrate">
            <option>&nbsp;</option>
            @foreach($categories as $category)
            <option value="{{ $category->kbcategory_id }}">{{ $category->kbcategory_title }}</option>
            @endforeach
        </select>
    </div>
</div>
@endif