<div class="boards count-{{ @count($leads) }} js-trigger-leads-kanban-board" id="leads-view-wrapper" data-position="{{ url('leads/update-position') }}">
    <!--each board-->
    @foreach($boards as $board)
    <!--board-->
    @include('pages.leads.components.kanban.board')
    @endforeach
</div>