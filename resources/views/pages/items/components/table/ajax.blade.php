@foreach($items as $item)
<!--each row-->
<tr id="item_{{ $item->item_id }}">
    @if(config('visibility.items_col_checkboxes'))
    <td class="items_col_checkbox checkitem" id="items_col_checkbox_{{ $item->item_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-items-{{ $item->item_id }}" name="ids[{{ $item->item_id }}]"
                class="listcheckbox listcheckbox-items filled-in chk-col-light-blue items-checkbox"
                data-actions-container-class="items-checkbox-actions-container" data-item-id="{{ $item->item_id }}"
                data-unit="{{ $item->item_unit }}" data-quantity="1" data-description="{{ $item->item_description }}"
                data-rate="{{ $item->item_rate }}">
            <label for="listcheckbox-items-{{ $item->item_id }}"></label>
        </span>
    </td>
    @endif
    <td class="items_col_description" id="items_col_description_{{ $item->item_id }}">
        @if(config('settings.trimmed_title'))
        {{ str_limit($item->item_description ?? '---', 45) }}
        @else
        {{ $item->item_description }}
        @endif
    </td>
    <td class="items_col_rate" id="items_col_rate_{{ $item->item_id }}">
        {{ runtimeMoneyFormat($item->item_rate) }}
    </td>
    <td class="items_col_unit" id="items_col_unit_{{ $item->item_id }}">{{ $item->item_unit }}</td>
    @if(config('visibility.items_col_category'))
    <td class="items_col_category ucwords" id="items_col_category_{{ $item->item_id }}">
        {{ str_limit($item->category_name ?? '---', 30) }}</td>
    @endif
    @if(config('visibility.items_col_action'))
    <td class="items_col_action actions_column" id="items_col_action_{{ $item->item_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            @if(config('visibility.action_buttons_delete'))
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_product')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                data-ajax-type="DELETE" data-url="{{ url('/') }}/items/{{ $item->item_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @endif
            <!--edit-->
            @if(config('visibility.action_buttons_edit'))
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/items/'.$item->item_id.'/edit') }}" data-loading-target="commonModalBody"
                data-modal-title="{{ cleanLang(__('lang.edit_product')) }}"
                data-action-url="{{ urlResource('/items/'.$item->item_id.'?ref=list') }}" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="items-td-container">
                <i class="sl-icon-note"></i>
            </button>
            @endif
            <!--more button (team)-->
            @if(config('visibility.action_buttons_edit') == 'show')
            <span class="list-table-action dropdown font-size-inherit">
                <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" title="{{ cleanLang(__('lang.more')) }}"
                    class="data-toggle-action-tooltip btn btn-outline-default-light btn-circle btn-sm">
                    <i class="ti-more"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="listTableAction">
                    <!--actions button - change category-->
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                        data-modal-title="{{ cleanLang(__('lang.change_category')) }}" data-url="{{ url('/items/change-category') }}"
                        data-action-url="{{ urlResource('/items/change-category?id='.$item->item_id) }}"
                        data-loading-target="actionsModalBody" data-action-method="POST">
                        {{ cleanLang(__('lang.change_category')) }}</a>
                    <!--actions button - attach project -->
                </div>
            </span>
            @endif
            <!--more button-->
        </span>
        <!--action button-->
    </td>
    @endif
</tr>
@endforeach
<!--each row-->