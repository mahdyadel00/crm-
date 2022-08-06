@foreach($clients as $client)
<!--each row-->
<tr id="client_{{ $client->client_id }}">
    <td class="clients_col_id" id="clients_col_id_{{ $client->client_id }}">{{ $client->client_id }}</td>
    <td class="clients_col_company" id="clients_col_id_{{ $client->client_id }}">
        <a href="/clients/{{ $client->client_id ?? '' }}">{{ str_limit($client->client_company_name, 35) }}</a>
    </td>
    <td class="clients_col_account_owner" id="clients_col_account_owner_{{ $client->client_id }}">
        <img src="{{ getUsersAvatar($client->avatar_directory, $client->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall">
        <span>{{ $client->first_name ?? '---' }}</span>
    </td>
    @if(config('visibility.modules.projects'))
    <td class="clients_col_projects" id="clients_col_projects_{{ $client->client_id }}">
        {{ $client->count_projects_all ?? '0' }}
    </td>
    @endif
    <td class="clients_col_invoices" id="clients_col_invoices_{{ $client->client_id }}">
        {{ runtimeMoneyFormat($client->sum_invoices_all) }}</td>
    <td class="clients_col_tags" id="clients_col_tags_{{ $client->client_id }}">


        <!--tag-->
        @if(count($client->tags) > 0)
        @foreach($client->tags->take(1) as $tag)
        <span class="label label-outline-default">{{ str_limit($tag->tag_title, 15) }}</span>
        @endforeach
        @else
        <span>---</span>
        @endif
        <!--/#tag-->

        <!--more tags (greater than tags->take(x) number above -->
        @if(count($client->tags) > 1)
        @php $tags = $client->tags; @endphp
        @include('misc.more-tags')
        @endif
        <!--more tags-->

    </td>
    <td class="clients_col_category" id="clients_col_category_{{ $client->client_id }}">
        <!--category-->
        <span class="label label-outline-default">{{ str_limit($client->category_name, 15) }}</span>
        <!--category-->
    </td>
    <td class="clients_col_status" id="clients_col_status_{{ $client->client_id }}">
        <span class="label {{ runtimeClientStatusLabel($client->client_status) }}">{{
            runtimeLang($client->client_status) }}</span>
    </td>

    @if(config('visibility.action_column'))
    <td class="clients_col_action actions_column" id="clients_col_action_{{ $client->client_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            @if(config('visibility.action_buttons_delete'))
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_client')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/clients/'.$client->client_id) }}">
                <i class="sl-icon-trash"></i>
            </button>
            @endif
            <!--edit-->
            @if(config('visibility.action_buttons_edit'))
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/clients/'.$client->client_id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_client')) }}"
                data-action-url="{{ urlResource('/clients/'.$client->client_id.'?ref=list') }}" data-action-method="PUT"
                data-action-ajax-loading-target="clients-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @endif

            <!--send email-->
            <button type="button" title="@lang('lang.send_email')"
                class="data-toggle-action-tooltip btn btn-outline-warning btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" 
                data-target="#commonModal"
                data-url="{{ url('/appwebmail/compose?view=modal&resource_type=client&resource_id='.$client->client_id) }}"
                data-loading-target="commonModalBody" 
                data-modal-title="@lang('lang.send_email')"
                data-action-url="{{ url('/appwebmail/send') }}"
                data-action-method="POST"
                data-modal-size="modal-xl"
                data-action-ajax-loading-target="clients-td-container">
                <i class="ti-email display-inline-block m-t-3"></i>
            </button>

            <a href="/clients/{{ $client->client_id ?? '' }}" class="btn btn-outline-info btn-circle btn-sm">
                <i class="ti-new-window"></i>
            </a>
        </span>
        <!--action button-->
        <!--more button (hidden)-->
        <span class="list-table-action dropdown hidden font-size-inherit">
            <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="btn btn-outline-default-light btn-circle btn-sm">
                <i class="ti-more"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="listTableAction">
                <a class="dropdown-item" href="javascript:void(0)">
                    <i class="ti-new-window"></i> {{ cleanLang(__('lang.view_details')) }}</a>
            </div>
        </span>
        <!--more button-->
    </td>
    @endif

</tr>
@endforeach
<!--each row-->