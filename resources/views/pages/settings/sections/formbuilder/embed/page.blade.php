@extends('pages.settings.ajaxwrapper')
@section('settings-page')

<!--tabs menu-->
@include('pages.settings.sections.formbuilder.misc.tabs')

<div id="webform-builder-wraper" class="p-t-40">


    <!--embed code-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">@lang('lang.embed_code')</label>
        <div class="col-12">
            <textarea class="form-control form-control-sm" rows="2" id="embed">{{ $embed_code }}</textarea>
        </div>
    </div>

    <div class="alert alert-info m-b-40">
        @lang('lang.embed_code_instructions')
    </div>

    <div class="alert alert-info m-b-40">
        @lang('lang.embed_code_instructions_2')
    </div>
    
    <div class="alert alert-info m-b-40">
        @lang('lang.embed_code_instructions_3')
    </div>

    <!--direct url code-->
    <div class="form-group row">
        <label class="col-12 control-label col-form-label">@lang('lang.direct_form_link')</label>
        <div class="col-12">
            <input type="text" class="form-control form-control-sm" rows="5" id="embed" value="{{ $direct_url }}">
        </div>
    </div>

</div>

@endsection