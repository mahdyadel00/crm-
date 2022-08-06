<div class="board">
    <div class="board-body {{ runtimeKanbanBoardColors($board['color']) }}">
        <div class="board-heading clearfix">
            <div class="pull-left">{{ runtimeLang($board['name']) }}</div>
            <div class="pull-right x-action-icons">
                <!--action add-->
                @if(config('visibility.kanban_board_add_buttons'))
                <span class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form cursor-pointer"
                    data-toggle="modal" data-target="#commonModal"
                    data-url="{{ urlResource('/tasks/create?status='.$board['id']) }}"
                    data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.add_task')) }}"
                    data-action-url="{{ urlResource('/tasks?type=kanban') }}" data-action-method="POST"
                    data-action-ajax-loading-target="commonModalBody"
                    data-save-button-class="" data-action-ajax-loading-target="commonModalBody"><i
                        class="mdi mdi-plus-circle"></i></span>
                @endif
            </div>
        </div>
        <!--cards-->
        <div class="content kanban-content" id="kanban-board-wrapper-{{ $board['id'] }}" data-board-name="{{ $board['id'] }}">

            <!--dynamic content-->
            @if(@count($board['tasks']) > 0)
            @include('pages.tasks.components.kanban.card')
            @endif

            <!-- dynamic load more button-->
            <div class="autoload loadmore-button-container x {{ $board['load_more'] }} hidden" id="tasks-loadmore-container-{{ $board['id'] }}">
                <a data-url="{{ $board['load_more_url'] }}"
                    href="javascript:void(0)" class="btn btn-rounded-x btn-secondary js-ajax-ux-request"
                    id="load-more-button-{{ $board['id'] }}">{{ cleanLang(__('lang.show_more')) }}</a>
            </div>
            <!-- /#dynamic load more button-->
        </div>
    </div>
</div>