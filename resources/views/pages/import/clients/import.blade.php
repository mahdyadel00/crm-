@extends('pages.import.wrapper')
<!--SECOND STEP FORM-->
@section('second-step-form')
<div class="form-group form-group-checkbox row">
    <label class="col-sm-12 col-lg-5 text-left control-label col-form-label required">{{ cleanLang(__('lang.send_welcome_email')) }}*</label>
    <div class="col-sm-12 col-lg-7 text-left" style="padding-top:5px;">
        <input type="checkbox" id="send_email" name="send_email" class="filled-in chk-col-light-blue" checked>
        <label for="send_email"></label>
    </div>
</div>
@endsection