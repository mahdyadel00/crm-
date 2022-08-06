<div class="splash-image" id="updatePasswordSplash">
    <img src="{{ url('/') }}/public/images/authentication-update-password.png" alt="404 - Not found" />
</div>
<div class="splash-text">
    {{ cleanLang(__('lang.change_your_password')) }}
</div>

<div class="form-group row">
    <div class="col-sm-12">
        <input type="password" class="form-control" id="password" name="password"
            placeholder="{{ cleanLang(__('lang.password')) }}">
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-12">
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
            placeholder="{{ cleanLang(__('lang.confirm_password')) }}">
    </div>
</div>

<!--if editing contact password-->
@if(auth()->user()->is_team)
<input type="hidden" class="form-control" id="contact_id" name="contact_id"
value="{{ request('contact_id') }}">
@endif

<!--if editing team password-->
@if(is_numeric(request('team_id')))
<input type="hidden" class="form-control" id="team_id" name="team_id"
value="{{ request('team_id') }}">
@endif