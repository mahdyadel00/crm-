<div class="card" id="team-table-wrapper" id="team-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (!$users->isEmpty())
            <table id="team-list-table" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
                <thead>
                    <tr>
                        <th class="team_col_first_name"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_first_name" href="javascript:void(0)"
                                data-url="{{ urlResource('/team?action=sort&orderby=first_name&sortorder=asc') }}">{{ cleanLang(__('lang.first_name')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <th class="team_col_position"><a class="js-ajax-ux-request js-list-sorting" id="sort_position"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/team?action=sort&orderby=position&sortorder=asc') }}">{{ cleanLang(__('lang.position')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span>
                        </th>
                        @if(config('visibility.action_super_user'))
                        <th class="team_col_role"><a class="js-ajax-ux-request js-list-sorting" id="sort_role_id"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/team?action=sort&orderby=role_id&sortorder=asc') }}">{{ cleanLang(__('lang.role')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span>
                        </th>
                        @endif
                        <th class="team_col_email"><a class="js-ajax-ux-request js-list-sorting" id="sort_email"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/team?action=sort&orderby=email&sortorder=asc') }}">{{ cleanLang(__('lang.email')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span>
                        </th>
                        <th class="team_col_phone"><a class="js-ajax-ux-request js-list-sorting" id="sort_phone"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/team?action=sort&orderby=phone&sortorder=asc') }}">{{ cleanLang(__('lang.phone')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span>
                        </th>
                        @if(config('visibility.action_super_user'))
                        <th class="team_col_last_active"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_last_seen" href="javascript:void(0)"
                                data-url="{{ urlResource('/team?action=sort&orderby=last_seen&sortorder=asc') }}">{{ cleanLang(__('lang.last_seen')) }}<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></th>
                        @endif
                        @if(config('visibility.action_super_user'))
                        <th class="team_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a>
                        </th>
                        @endif
                    </tr>
                </thead>
                <tbody id="team-td-container">
                    <!--ajax content here-->
                    @include('pages.team.components.table.ajax')
                    <!--ajax content here-->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="20">
                            <!--load more button-->
                            @include('misc.load-more-button')
                            <!--load more button-->
                        </td>
                    </tr>
                </tfoot>
            </table>
            @else
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>