<!-- right-sidebar -->
<div class="right-sidebar" id="sidepanel-filter-tags">
    <form>
        <div class="slimscrollright">
            <!--title-->
            <div class="rpanel-title">
                <i class="icon-Filter-2"></i>{{ cleanLang(__('lang.filter_tags')) }}
                <span>
                    <i class="ti-close js-close-side-panels" data-target="sidepanel-filter-tags"></i>
                </span>
            </div>
            <!--title-->
            <!--body-->
            <div class="r-panel-body">

                <!--resource id-->
                <div class="filter-block">
                    <div class="title">
                        Tag {{ cleanLang(__('lang.title')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <input class="form-control form-control-sm" type="text" name="filter_tag_title"
                                    id="filter_tag_title">
                            </div>
                        </div>
                    </div>
                </div>


                <!--resource type-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.resource_type')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select class="select2-basic form-control form-control-sm" id="filter_tagresource_type"
                                    name="filter_tagresource_type">
                                    <option></option>
                                    @foreach($resource_types as $type)
                                    <option value="{{ $type }}">{{ runtimeLang($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!--resource id-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.resource_id')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <input class="form-control form-control-sm" type="number" name="filter_tagresource_id"
                                    id="filter_tagresource_id">
                            </div>
                        </div>
                    </div>
                </div>


                <!--created by -->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.added_by')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select name="filter_tag_creatorid" id="filter_tag_creatorid"
                                    class="form-control form-control-sm select2-basic select2-multiple select2-tags select2-hidden-accessible"
                                    multiple="multiple" tabindex="-1" aria-hidden="true">
                                    @foreach(config('system.team_members') as $user)
                                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!--date added-->
                <div class="filter-block">
                    <div class="title">
                        Date Created
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="filter_tag_created_start"
                                    class="form-control form-control-sm pickadate" autocomplete="off"
                                    placeholder="Start">
                                <input class="mysql-date" type="hidden" name="filter_tag_created_start"
                                    id="filter_tag_created_start" value="">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="filter_tag_created_end"
                                    class="form-control form-control-sm pickadate" autocomplete="off" placeholder="End">
                                <input class="mysql-date" type="hidden" name="filter_tag_created_end"
                                    id="filter_tag_created_end" value="">
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
                        data-url="{{ urlResource('/tags/search?') }}" data-type="form" data-ajax-type="GET">{{ cleanLang(__('lang.apply_filter')) }}</button>
                </div>
            </div>
            <!--body-->
        </div>
    </form>
</div>
<!--sidebar-->