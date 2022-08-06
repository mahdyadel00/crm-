<!--main table view-->
@include('pages.tasks.components.kanban.kanban')

<!--Update Card Poistion (team only)-->
@if(auth()->user()->is_team || config('visibility.tasks_participate'))
<span id="js-tasks-kanban-wrapper" class="hidden" data-position="{{ url('tasks/update-position') }}">placeholder</script>
@endif