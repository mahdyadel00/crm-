<!-- right-sidebar -->
<div class="right-sidebar" id="sidepanel-filter-clients">
    <form>
        <div class="slimscrollright">
            <!--title-->
            <div class="rpanel-title">
                <i class="icon-Filter-2"></i>{{ cleanLang(__('lang.filter_clients')) }}
                <span>
                    <i class="ti-close js-close-side-panels" data-target="sidepanel-filter-clients"></i>
                </span>
            </div>
            <!--title-->
            <!--body-->
            <div class="r-panel-body">

                <!--company name-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.company_name')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <!--select2 basic search-->
                                <select name="filter_client_id" id="filter_client_id"
                                    class="form-control form-control-sm js-select2-basic-search select2-multiple select2-hidden-accessible"
                                    multiple="multiple" tabindex="-1" aria-hidden="true"
                                    data-ajax--url="{{ url('/') }}/feed/company_names"></select>
                                <!--select2 basic search-->
                            </div>
                        </div>
                    </div>
                </div>
                <!--company name-->

                <!--categories-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.category')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select name="filter_client_categoryid" id="filter_client_categoryid"
                                    class="form-control form-control-sm select2-basic select2-multiple select2-hidden-accessible"
                                    multiple="multiple" tabindex="-1" aria-hidden="true">
                                    @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!--categories-->

                <!--tags-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.tags')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select name="filter_tags" id="filter_tags"
                                    class="form-control form-control-sm select2-multiple {{ runtimeAllowUserTags() }} select2-hidden-accessible"
                                    multiple="multiple" tabindex="-1" aria-hidden="true">
                                    @foreach($tags as $tag)
                                    <option value="{{ $tag->tag_title }}">
                                        {{ $tag->tag_title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!--tags-->

                <!--filter item-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.date_created')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="filter_date_created_start" 
                                    class="form-control form-control-sm pickadate" autocomplete="off"
                                    placeholder="{{ cleanLang(__('lang.start')) }}">
                                <input class="mysql-date" type="hidden" name="filter_date_created_start" id="filter_date_created_start" value="">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="filter_date_created_end" 
                                    autocomplete="off" class="form-control form-control-sm pickadate"
                                    placeholder="{{ cleanLang(__('lang.end')) }}">
                                <input class="mysql-date" type="hidden" name="filter_date_created_end" id="filter_date_created_end" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <!--filter item-->

                <!--status-->
                <div class="filter-block">
                    <div class="title">
                        {{ cleanLang(__('lang.status')) }}
                    </div>
                    <div class="fields">
                        <div class="row">
                            <div class="col-md-12">
                                <select name="filter_client_status" id="filter_client_status"
                                    class="form-control form-control-sm select2-basic select2-multiple"
                                    multiple="multiple" tabindex="-1" aria-hidden="true">
                                    <option value="active">{{ cleanLang(__('lang.active')) }}</option>
                                    <option value="suspended">{{ cleanLang(__('lang.suspended')) }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!--status->


                <!--buttons-->
                <div class="buttons-block">
                    <button type="button" name="foo1"
                        class="btn btn-rounded-x btn-secondary js-reset-filter-side-panel">{{ cleanLang(__('lang.reset')) }}</button>
                    <input type="hidden" name="action" value="search">
                    <input type="hidden" name="source" value="{{ $page['source_for_filter_panels'] ?? '' }}">
                    <button type="button" class="btn btn-rounded-x btn-danger js-ajax-ux-request apply-filter-button"
                    data-url="{{ urlResource('/clients/search') }}"
                    data-type="form" data-ajax-type="GET">{{ cleanLang(__('lang.apply_filter')) }}</button>
                </div>


            </div>
            <!--body-->
        </div>
    </form>
</div>
<!--sidebar-->