<div class="table-responsive">
    @if (@count($emails) > 0)
    <table id="demo-email-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
        <thead>
            <tr>
                <th class="emails_col_emaillog_created">@lang('lang.date')</th>
                <th class="emails_col_emaillog_to">@lang('lang.to')</th>
                <th class="emails_col_emaillog_subject">@lang('lang.subject')</th>
                <th class="emails_col_action"><a href="javascript:void(0)">@lang('lang.action')</a></th>
            </tr>
        </thead>
        <tbody id="emails-td-container">
            <!--ajax content here-->
            @include('pages.settings.sections.email.log.ajax')
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
            <tr>
                <td colspan="20">
                    <div class="text-right">
                        <button type="button"
                            class="btn btn-danger btn-sm waves-effect text-left confirm-action-danger"
                            data-confirm-title="@lang('lang.delete_all_emails')"
                            data-confirm-text="@lang('lang.are_you_sure')" data-ajax-type="DELETE"
                            data-url="{{ url('settings/email/log/purge') }}">@lang('lang.delete_all_emails')</button>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    @endif
    @if (@count($emails) == 0)
    <!--nothing found-->
    @include('notifications.no-results-found')
    <!--nothing found-->
    @endif
</div>