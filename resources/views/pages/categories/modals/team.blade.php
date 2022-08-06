<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.category_users')) }}</label>
    <div class="col-sm-12">
        <select name="users" id="users"
            class="form-control form-control-sm select2-basic select2-multiple select2-tags select2-hidden-accessible"
            multiple="multiple" tabindex="-1" aria-hidden="true">
            <!--current-->
            @foreach($users as $current)
            @php $current_users[] = $current->categoryuser_userid; @endphp
            @endforeach
            <!--/#array of assigned-->
            <!--users list-->
            @foreach(config('system.team_members') as $user)
            <option value="{{ $user->id }}" {{ runtimePreselectedInArray($user->id ?? '', $current_users ?? []) }}>{{
                $user->full_name }}</option>
            @endforeach
            <!--/#users list-->
        </select>
    </div>
</div>


<div class="form-group row">
    <div class="col-sm-12">
        <div class="alert alert-info"><h5 class="text-info"><i class="sl-icon-info"></i> @lang('lang.info')</h5>@lang('lang.category_team_info')</div>
    </div>
</div>