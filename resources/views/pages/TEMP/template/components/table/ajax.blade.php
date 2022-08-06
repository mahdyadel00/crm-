@foreach($foos as $foo)
<!--each row-->
<tr id="foos_{{ $foos->foo_id }}">

    @if(config('visibility.foos_col_checkboxes'))
    <td class="col_foos_checkbox checkfoo" id="foos_col_checkbox_{{ $foo->foo_id }}">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-foos-{{ $foo->foo_id }}" name="ids[{{ $foo->foo_id }}]"
                class="listcheckbox listcheckbox-foos filled-in chk-col-light-blue foos-checkbox"
                data-actions-container-class="foos-checkbox-actions-container" data-foo-id="{{ $foo->foo_id }}">
            <label for="listcheckbox-foos-{{ $foo->foo_id }}"></label>
        </span>
    </td>
    @endif
    
    <!--actions-->
    <td class="col_foos_actions actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            <button type="button" title="@lang('lang.delete')"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="@lang('lang.delete_item')" data-confirm-text="@lang('lang.are_you_sure')"
                data-ajax-type="DELETE" data-url="{{ url('/foos/'.$foos->foo_id) }}">
                <i class="sl-icon-trash"></i>
            </button>
            <!--edit-->
            <button type="button" title="@lang('lang.edit')"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/foos/'.$foo->foo_id.'/edit') }}" data-loading-target="commonModalBody"
                data-modal-title="@lang('lang.edit_item')" data-action-url="{{ urlResource('/foos/'.$foo->foo_id) }}"
                data-action-method="PUT" data-action-ajax-class="js-ajax-ux-request"
                data-action-ajax-loading-target="foos-td-container">
                <i class="sl-icon-note"></i>
            </button>
            <!--view-->
            <a href="{{ url('/foo/'.$foos->foo_id) }}" title="@lang('lang.view')"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
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
                    <i class="ti-new-window"></i> @lang('lang.view_details')</a>
            </div>
        </span>
        <!--more button-->
    </td>
</tr>
@endforeach
<!--each row-->