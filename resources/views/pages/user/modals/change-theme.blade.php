<div class="splash-image" id="updatePasswordSplash">
    <img src="{{ url('/') }}/public/images/theme.svg" alt="404 - Not found" />
</div>
<div class="splash-text">
    {{ cleanLang(__('lang.change_theme')) }}
</div>

<!--item-->
<div class="form-group row m-t-20">
    <div class="col-sm-12">
        <select class="select2-basic form-control form-control-sm select2-preselected" id="pref_theme"
            name="pref_theme" data-preselected="{{ auth()->user()->pref_theme ?? ''}}">
                @foreach(config('theme.list') as $theme)
                <option value="{{ $theme }}">{{ runtimeThemeName($theme) }}</option>
                @endforeach
        </select>
    </div>
</div>