<div class="boards count-{{ @count($tasks) }}" id="tasks-view-wrapper">
    <!--each board-->
    @foreach($boards as $board)
    <!--board-->
    @include('pages.tasks.components.kanban.board')
    @endforeach
</div>
<!--ajax element-->
<span class="hidden" data-url=""></span>

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.tasks.components.misc.filter-tasks')
@endif
<!--filter-->