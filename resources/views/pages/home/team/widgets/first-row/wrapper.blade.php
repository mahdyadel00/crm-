<div class="row">
    <!--PROJECTS PENDING-->
    @include('pages.home.team.widgets.first-row.projects-pending')

    <!--PROJECTS COMPLETED-->
    @include('pages.home.team.widgets.first-row.tasks-new')

    <!--INVOICES DUE-->
    @include('pages.home.team.widgets.first-row.tasks-inprogress')

    <!--INVOICES OVERDUE-->
    @include('pages.home.team.widgets.first-row.tasks-feedback')
</div>