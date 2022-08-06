<!--main table view-->
@include('pages.milestones.components.table.table')

<!--Update Card Poistion-->
@if(config('visibility.milestone_actions'))
<span class="hidden" id="js-trigger-milestones-sorting">placeholder</span>
@endif