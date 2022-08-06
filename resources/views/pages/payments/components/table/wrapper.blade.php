<!--bulk actions-->
@include('pages.payments.components.actions.checkbox-actions')

<!--main table view-->
@include('pages.payments.components.table.table')

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.payments.components.misc.filter-payments')
@endif
<!--filter-->