<div class="x-section">
    <div class="x-title">
        <h6>@lang('lang.tags')</h6>
    </div>
    <!--current tags-->
    <div id="card-tags-current-tags-container">
        @if(count($current_tags) >0)
        <div class="x-tags">
            @foreach($current_tags as $current)
            <span class="x-each-tag">{{ $current->tag_title }}</span>
            <!--dynamic js script array-->
            <script>
                NX.array_1.push('{{ $current->tag_title }}');
            </script>
            @endforeach
        </div>
        @endif
        @if($task->permission_edit_task)
        <div class="x-edit-tabs"><a href="javascript:void(0);" id="card-tags-button-edit">@lang('lang.edit_tags')</a>
        </div>
        @endif
    </div>
    <!--edit tags-->
    @if($task->permission_edit_task)
    <div id="card-tags-edit-tags-container" class="hidden">
        <select name="tags" id="card_tags"
            class="form-control form-control-sm select2-multiple {{ runtimeAllowUserTags() }} select2-hidden-accessible"
            multiple="multiple" tabindex="-1" aria-hidden="true">
            <!--array of selected tags-->
            @foreach($current_tags as $selected)
            @php $selected_tags[] = $selected->tag_title ; @endphp
            @endforeach
            <!--/#array of selected tags-->
            @foreach($tags as $tag)
            <option value="{{ $tag->tag_title }}"
                {{ runtimePreselectedInArray($tag->tag_title ?? '', $selected_tags  ?? []) }}>
                {{ $tag->tag_title }}
            </option>
            @endforeach
        </select>
        <div id="card-edit-tags-buttons" class="p-t-10 hidden text-right" style="display: block;">
            <button type="button" class="btn waves-effect waves-light btn-xs btn-default"
                id="card-tags-button-cancel">@lang('lang.close')</button>
            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger ajax-request"
                data-url="{{ url('tasks/'.$task->task_id.'/update-tags') }}" data-progress-bar="hidden"
                data-type="form" data-form-id="card-tags-container" data-ajax-type="post"
                id="card-tags-button-save">@lang('lang.save')</button>
        </div>
    </div>
    @endif
</div>