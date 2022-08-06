@if(config('system.settings_projects_permissions_basis') == 'category_based')
<div class="alert alert-info">@lang('lang.projects_assigned_auto')</div>
<div class="alert alert-info">@lang('lang.you_can_change_in_settings')</div>
@else
<div class="form-group row">
    <div class="col-sm-12">
        <select name="assigned" id="assigned"
            class="form-control form-control-sm select2-basic select2-multiple select2-tags select2-hidden-accessible"
            multiple="multiple" tabindex="-1" aria-hidden="true">
            <!--array of assigned-->
            @foreach($users as $user)
            @php $assigned[] = $user->id; @endphp
            @endforeach
            <!--/#array of assigned-->
            <!--users list-->
            @foreach(config('system.team_members') as $user)
            <option value="{{ $user->id }}" {{ runtimePreselectedInArray($user->id ?? '', $assigned ?? []) }}>{{
                $user->full_name }}</option>
            @endforeach
            <!--/#users list-->
        </select>
    </div>
</div>
@endif