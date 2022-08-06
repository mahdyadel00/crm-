<!-- right-sidebar -->
<div class="right-sidebar" id="sidepanel-filter-timesheets">
    <form>
        <div class="slimscrollright">
            <!--title-->
            <div class="rpanel-title">
                <i class="icon-Filter-2"></i>{{ cleanLang(__('lang.filter_timesheets')) }}
                <span>
                    <i class="ti-close js-close-side-panels" data-target="sidepanel-filter-timesheets"></i>
                </span>
            </div>
            <!--body-->
            <div class="r-panel-body">


                <!-- team member -->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.team_members')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                    <select name="filter_timer_creatorid" id="filter_timer_creatorid" class="form-control form-control-sm select2-basic select2-multiple select2-tags select2-hidden-accessible"
                                    multiple="multiple" tabindex="-1" aria-hidden="true">
                                    @foreach(config('system.team_members') as $user)
                                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                @if(config('visibility.filter_panel_resource'))
                <!--project-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.project')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                    <select name="filter_timer_projectid" id="filter_timer_projectid" class="form-control form-control-sm js-select2-basic-search select2-hidden-accessible"
                                    data-ajax--url="{{ url('/') }}/feed/projects?ref=general"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <!--lead-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.lead')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                    <select name="filter_timer_leadid" id="filter_timer_leadid" class="form-control form-control-sm js-select2-basic-search select2-hidden-accessible"
                                    data-ajax--url="{{ url('/') }}/feed/leads?ref=general"></select>
                            </div>
                        </div>
                    </div>
                </div>
                @endif


                <!--grouping-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.grouping')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select class="select2-basic form-control form-control-sm" id="filter_grouping"
                                    name="filter_grouping">
                                    <option value="none">{{ cleanLang(__('lang.no_grouping')) }}</option>
                                    <option value="task">{{ cleanLang(__('lang.group_by_task')) }}</option>
                                    <option value="user">{{ cleanLang(__('lang.group_by_user')) }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!--date range-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.date')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="filter_date_created_start" 
                                    class="form-control form-control-sm pickadate" autocomplete="off"
                                    placeholder="Start">
                                <input class="mysql-date" type="hidden" name="filter_date_created_start" id="filter_date_created_start"value="">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="filter_date_created_end" 
                                    class="form-control form-control-sm pickadate" autocomplete="off" placeholder="End">
                                <input class="mysql-date" type="hidden" name="filter_date_created_end"
                                    id="filter_date_created_end" value="">
                            </div>
                        </div>
                    </div>
                </div>

                <!--buttons-->
                <div class="buttons-block">
                    <button type="button" name="foo1"
                        class="btn btn-rounded-x btn-secondary js-reset-filter-side-panel">{{ cleanLang(__('lang.reset')) }}</button>
                    <input type="hidden" name="action" value="search">
                    <input type="hidden" name="source" value="{{ $page['source_for_filter_panels'] ?? '' }}">
                    <button type="button" class="btn btn-rounded-x btn-danger js-ajax-ux-request apply-filter-button"
                    data-url="{{ urlResource('/timesheets/search?') }}"
                    data-type="form" data-ajax-type="GET">{{ cleanLang(__('lang.apply_filter')) }}</button>
                </div>
            </div>
            <!--body-->
        </div>
    </form>
</div>
<!--sidebar-->